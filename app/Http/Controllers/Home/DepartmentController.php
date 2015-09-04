<?php

namespace App\Http\Controllers\Home;

use App\Department;
use App\Http\Requests\DepartmentRequest;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartmentController extends AuthController
{
    /**
     * Allow administrator, department manager to access this class and its functions.
     */
    public function __construct()
    {
        parent::__construct();

        if(Auth::user() && !Auth::user()->hasRole(['administrator', 'department_manager'])) {
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
        $pageTitle = 'All Departments';

        // consider to optimise the order of departments
        $originalDepartments = Department::all();

        $i = 0;
        $managedDepartment = DB::table('departments')->where('manager', Auth::user()->id)->pluck('id');
        foreach($originalDepartments as $originalDepartment) {
            $manager = $originalDepartment->manager ? User::find($originalDepartment->manager)->name : 'TBD';

            $parent_department = $originalDepartment->parent_department ? Department::find($originalDepartment->parent_department)->display_name : '--';

            $isManageable = 0;
            if(Auth::user()->hasRole(['administrator'])) {
                $isManageable = 1;
            }
            if(Auth::user()->hasRole(['department_manager'])) {
                if(isManageableDepartment($managedDepartment, $originalDepartment->id)) {
                    $isManageable = 1;
                }
            }

            $departments[$i] = array(
                'number'            => $i+1,
                'id'                => $originalDepartment->id,
                'name'              => $originalDepartment->name,
                'display_name'      => $originalDepartment->display_name,
                'description'       => $originalDepartment->description,
                'manager'           => $manager,
                'parent_department' => $parent_department,
                'is_manageable'     => $isManageable
            );
            $i++;
        }

        return view('home.departments.index', compact('pageTitle', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $pageTitle = 'Add New Department';

        // Initiate
        $existedDepartments = array();
        // All departments are available if user is administrator but only departments that assigned below if user is department manager.
        if(Auth::user()->hasRole(['administrator'])) {
            $existedDepartments = Department::all();
        }
        if(Auth::user()->hasRole(['department_manager'])) {
            $managedDepartment = DB::table('departments')->where('manager', Auth::user()->id)->first();
            array_push($existedDepartments, $managedDepartment);
            $subDepartments = DB::table('departments')->where('parent_department', $managedDepartment->id)->get();
            foreach($subDepartments as $department) {
                array_push($existedDepartments, $department);
            }
        }

        return view('home.departments.create', compact('pageTitle', 'existedDepartments'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(DepartmentRequest $request)
    {
        $newDepartment = $request->all();

        Department::create($newDepartment);

        return redirect('home/department');
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
        $department = Department::find($id);
        $parentDepartment = Department::find($department->parent_department);
        $department->parent_department_name = $parentDepartment ? $parentDepartment->display_name : '--';

        $pageTitle = 'Update Department: '.$department->display_name;

        return view('home.departments.edit', compact('pageTitle', 'department'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(DepartmentRequest $request, $id)
    {
        $department = Department::find($id);

        $department->update($request->all());

        return redirect('home/department');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        Department::destroy($id);

        return 'deleted successfully.';
    }
}
