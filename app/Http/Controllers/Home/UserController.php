<?php

namespace App\Http\Controllers\Home;

use App\Department;
use App\Http\Requests\UserRequest;
use App\Role;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends AuthController
{
    /**
     * Allow administrator, department manager, category manager to access this class and its functions.
     */
    public function __construct()
    {
        parent::__construct();

        if(Auth::user() && !Auth::user()->hasRole(['administrator', 'department_manager', 'category_manager'])) {
            abort(404);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $pageTitle = 'All Users';

        // Initiate users, while, in fact, there is already one user since install.
        $users = array();

        $originalUsers = User::all();

        $i = 0;
        $managedDepartment = DB::table('departments')->where('manager', Auth::user()->id)->pluck('id');
        foreach($originalUsers as $originalUser) {
            // Currently, assuming users are in single department.
            $department = User::find($originalUser->id)->departments->first();
            $departmentDisplayName = $department ? $department->display_name : 'TBD';

            $role = User::find($originalUser->id)->roles->first();
            $roleDisplayName = $role ? $role->display_name : 'TBD';

            $isManageable = 0;
            if(Auth::user()->hasRole(['administrator'])) {
                $isManageable = 1;
            }
            if((Auth::user()->hasRole(['department_manager']))
                && (isManageableDepartment($managedDepartment, $department['id']))
                && ($originalUser->id != 1)) {
                // Exclude Administrator
                $isManageable = 1;
            }
            if(Auth::user()->hasRole(['category_manager'])) {

            }

            $users[$i] = array(
                'number'        => $i+1,
                'id'            => $originalUser->id,
                'name'          => $originalUser->name,
                'email'         => $originalUser->email,
                'department'    => $departmentDisplayName,
                'role'          => $roleDisplayName,
                'is_manageable' => $isManageable
            );

            $i++;
        }

        return view('home.users.index', compact('pageTitle', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $pageTitle = 'Add New User';

        // Department Manager and Category Manager can create users under their own managed department and sub departments,
        // While Administrator can assign to all departments.
        $departments = array();
        if(Auth::user()->hasRole(['administrator'])) {
            $departments = Department::all()->toArray();
        }
        else {
            $managedDepartment = Auth::user()->departments->first();
            $allDepartments = Department::all();
            foreach($allDepartments as $oneDepartment) {
                if(isManageableDepartment($managedDepartment->id, $oneDepartment->id)) {
                    array_push($departments, $oneDepartment->toArray());
                }
            }
        }

        return view('home.users.create', compact('pageTitle', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(UserRequest $request)
    {
        $input = $request->all();

        // Add new user
        // $input中多余数据处理暂时不考虑，由User Model自行处理
        // password需要Hash加密
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);

        // Add user/department relationship
        $user->departments()->attach($input['department']);

        // Add user/role relationship
        if($input['role'] != '0') {
            // role selected
            $user->roles()->attach($input['role']);
            // Redundancy record manager info in departments table
            if($input['role'] == '2') {
                $department = Department::find($input['department']);
                $department->manager = $user->id;
                $department->save();
            }
        }

        return redirect('home/user');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $user = User::find($id);

        $pageTitle = 'Update User: '.$user->name;

        // Department Manager and Category Manager can create users under their own managed department and sub departments,
        // While Administrator can assign to all departments.
        $departments = array();
        if(Auth::user()->hasRole(['administrator'])) {
            $departments = Department::all()->toArray();
        }
        else {
            $managedDepartment = Auth::user()->departments->first();
            $allDepartments = Department::all();
            foreach($allDepartments as $oneDepartment) {
                if(isManageableDepartment($managedDepartment->id, $oneDepartment->id)) {
                    array_push($departments, $oneDepartment->toArray());
                }
            }
        }

        $currentDepartment = $user->departments ? $user->departments->first() : null;

        $currentRole = $user->roles ? $user->roles->first() : null;

        return view('home.users.edit', compact('pageTitle', 'user', 'departments', 'currentDepartment', 'currentRole'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(UserRequest $request, $id)
    {
        $input = $request->all();

        $user = User::find($id);

        if($input['password'] != $user->password) {
            $input['password'] = Hash::make($input['password']);
        }

        $user->update($input);

        // Add user/department relationship
        $user->departments()->sync([$input['department']]);

        // Add user/role relationship
        if($input['role'] != '0') {
            // role selected
            $user->roles()->sync([$input['role']]);
            // Redundancy record manager info in departments table
            if($input['role'] == '2') {
                $department = Department::find($input['department']);
                $department->manager = $user->id;
                $department->save();
            }
        }

        return redirect('home/user');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        User::destroy($id);

        return 'deleted successfully.';
    }

    public function getAvailableRoles($departmentId)
    {
        $department = Department::find($departmentId);

        // Administrator
        if(Auth::user()->hasRole(['administrator'])) {
            if($department->manager != 0) {
                // This department already has manager.
                return Role::where('id', '>', 2)->get();
            }
            else {
                // This department doesn't have manager yet.
                return Role::where('id', '>', 1)->get();
            }
        }

        // Department Manager
        if(Auth::user()->hasRole(['department_manager'])) {
            if($departmentId == Auth::user()->departments->first()->id) {
                // This department is manager currently managed.
                return Role::where('id', '>', 2)->get();
            }
            else {
                if($department->manager != 0) {
                    // This department already has manager.
                    return Role::where('id', '>', 2)->get();
                }
                else {
                    // This department doesn't have manager yet.
                    return Role::where('id', '>', 1)->get();
                }
            }
        }

        // Category Manager
        if(Auth::User()->hasRole(['category_manager'])) {
            // Whatever the department is, category manager can only create article manager account.
            return Role::where('id', '>', '3')->get();
        }
    }

    public function getCategoryManagers($department)
    {
        $originalUsers = Department::find($department)->users;

        $users = array();
        foreach($originalUsers as $originalUser) {
            if($originalUser->hasRole(['category_manager'])) {
                array_push($users, $originalUser);
            }
        }

        return $users;
    }
}
