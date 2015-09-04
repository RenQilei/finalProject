<?php

namespace App\Http\Controllers\Home;

use App\Category;
use App\Department;
use App\Http\Requests\CategoryRequest;
use App\Template;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryController extends AuthController
{
    /**
     * Allow administrator, department manager, category manager to access this class and its functions.
     */
//    public function __construct()
//    {
//        parent::__construct();
//
//        if(Auth::user() && !Auth::user()->hasRole(['administrator', 'department_manager', 'category_manager'])) {
//            abort(404);
//        }
//    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $pageTitle = 'All Categories';

        $originalCategories = Category::all(); // 需要修改为登陆用户可以查看的Categories，而不是全部

        $i = 0;
        $categories = array(); // 如果一个分类都没有添加，将不会进入foreach循环，故此处需要提前申明并赋值一个$categories
        foreach($originalCategories as $originalCategory) {
            $department = Department::find($originalCategory->department_id);
            $parent_category = $originalCategory->parent_category ? Category::find($originalCategory->parent_category)->display_name : '--';

            $isManageable = 0;
            if(Auth::user()->hasRole(['administrator'])) {
                $isManageable = 1;
            }
            if((Auth::user()->hasRole(['department_manager']))
                && ($department->id == Auth::user()->departments->first()->id)) {
                $isManageable = 1;
            }
            if((Auth::user()->hasRole(['category_manager']))
                && ($department->manager == Auth::user()->id)) {
                $isManageable = 1;
            }

            $categories[$i] = array(
                'number'            => $i+1,
                'id'                => $originalCategory->id,
                'name'              => $originalCategory->name,
                'display_name'      => $originalCategory->display_name,
                'description'       => $originalCategory->description,
                'manager'           => $originalCategory->manager,
                'department'        => $department->display_name,
                'parent_category'   => $parent_category,
                'is_manageable'     => $isManageable
            );
            $i++;
        }

        return view('home.categories.index', compact('pageTitle', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $pageTitle = 'Add New Category';

        // If user is administrator
        if(Auth::user()->hasRole(['administrator'])) {
            $departments = Department::all()->toArray();
        }
        // If user is department manager
        if(Auth::user()->hasRole(['department_manager'])) {
            $managedDepartment = Auth::user()->departments->first();
            $allDepartments = Department::all();
            foreach($allDepartments as $oneDepartment) {
                if(isManageableDepartment($managedDepartment->id, $oneDepartment->id)) {
                    array_push($departments, $oneDepartment->toArray());
                }
            }
        }
        // If user is category manager
        if(Auth::user()->hasRole(['category_manager'])) {
            $departments = Auth::user()->departments->toArray();
        }

        return view('home.categories.create', compact('pageTitle', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(CategoryRequest $request)
    {
        $category = $request->all();

        // Allocate manager for category
        // If user is administrator or department manager, category manager can be determined in request
        // But for category manager, it must be allocated to themselves
        if(Auth::user()->hasRole(['category_manager'])) {
            $category['manager'] = Auth::user()->id;
        }

        Category::create($category);

        return redirect('home/category');
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
        $category = Category::find($id);

        $pageTitle = 'Update Category: '.$category->display_name;

        $category->department_name = Department::find($category->department_id)->display_name;
        $category->parent_category_name = $category->parent_category ? Category::find($category->parent_category)->display_name : 'This is root.';

        return view('home.categories.edit', compact('pageTitle', 'category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(CategoryRequest $request, $id)
    {
        $category = Category::find($id);
        $input = $request->all();

        // Allocate manager for category
        // If user is administrator or department manager, category manager can be determined in request
        // But for category manager, it must be allocated to themselves
        if(Auth::user()->hasRole(['category_manager'])) {
            $input['manager'] = Auth::user()->id;
        }

        $category->update($input);

        return redirect('home/category');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        Category::destroy($id);

        return 'deleted successfully.';
    }

    public function getCategoryTemplateList($categoryId)
    {
        $templates = Template::where('category_id', '=', $categoryId)->get();

        return $templates;
    }

    public function getAvailableParentCategories($departmentId)
    {
        $department = Department::find($departmentId);

        $categories = $department->categories;

        return $categories;
    }
}
