<?php

namespace App\Http\Controllers;

use App\Category;
use App\Department;
use App\Http\Requests\UserRequest;
use App\Permission;
use App\Role;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InstallController extends Controller
{

    /**
     * Start point to install.
     *
     * @return \Illuminate\View\View
     */
    public function index() {

        return view('install.index');
    }

    /**
     * First step of installing, required to set up the basic information of system, and register of admin account.
     *
     * WARNING: Only accept the request from '/install'
     *
     * @return \Illuminate\View\View
     */
    public function stepOne(Request $request) {
        if(substr($request->session()->previousUrl(), -7, 7) == 'install') {
            return view('install.step_one');
        } else {
            dd("123");
        }
    }

    /**
     * Handle the request from stepOne.
     *
     * ISSUE: Once request is wrong and redirect back to '/install/step_one', it will trigger 404 out.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function stepOneHandler(UserRequest $request) {

        // Currently, request all is for creating admin account, but need to change if more requested inputs appear.
        /*
         * Add first new user.
         */

        $user = $this->initiateUser($request);
        // Get id of new admin account
        $adminId = $user->first()->id;

        /*
         * Initiate roles, including administrator, department manager, category manager and article manager
         */
        $administratorId = $this->initiateRole();

        /*
         * Assign new user as Administrator.
         */
        $user->roles()->attach($administratorId);

        /*
         * Initiate first department.
         */
        $mainDepartmentId = $this->initiateDepartment();

        /*
         * Initiate first category.
         */
        $defaultCategoryId = $this->initiateCategory();

        /*
         * Allocate new user to Main Department
         */
        $user->departments()->attach($mainDepartmentId);

        /*
         * Initiate permissions, according to user requirements.
         */
        $this->initiatePermission();

        /*
         * Assign roles to permissions.
         */
        $this->initiatePermissionRole();

        /*
         * Assign IS_INSTALLED to 'true'
         */
        if(getenv('IS_INSTALLED') == 'false') {
            $filePath = base_path()."/.env";
            $content = file_get_contents($filePath);
            $contentReplaced = str_replace('IS_INSTALLED=false', 'IS_INSTALLED=true', $content);
            file_put_contents($filePath, $contentReplaced);
        }

        /*
         * Log into the system and redirect to admin page '/home'.
         */
        // Login via Auth::loginUsingId mechanism
        if (Auth::loginUsingId($adminId)) {
            // Authentication passed...
            return redirect('home');
        } else {
            dd('Oops, Sorry but when you saw this, it means something goes wrong in the process of authentication. Please contact developers.');
        }

    }

    /*
     * All private functions below could be redesigned and reused later probably.
     */

    /**
     * Initiate a new user.
     *
     * @param Request $request
     * @return static
     */
    private function initiateUser(Request $request) {
        $newUser = $request->all();
        $newUser['password'] = Hash::make($newUser['password']);

        return User::create($newUser);
    }

    /**
     * Initiate new roles.
     * According to requirements, currently roles are:
     * Administrator, Department Manager, Category Manager, Article Manager
     *
     * @param $adminId
     * @return mixed
     */
    private function initiateRole() {
        // Administrator
        $role = new Role();
        $role->name = 'administrator';
        $role->display_name = 'Administrator';
        $role->description = 'The administrator of system';
        $role->save();
        // get id of admin
        $administratorId = DB::table('roles')->where('name', 'administrator')->pluck('id');

        // Department Manager
        $role = new Role();
        $role->name = 'department_manager';
        $role->display_name = 'Department Manager';
        $role->description = 'The department manager of system';
        $role->save();

        // Category Manager
        $role = new Role();
        $role->name = 'category_manager';
        $role->display_name = 'Category Manager';
        $role->description = 'The category manager of system';
        $role->save();

        // Article Manager
        $role = new Role();
        $role->name = 'article_manager';
        $role->display_name = 'Article Manager';
        $role->description = 'The article manager of system';
        $role->save();

        return $administratorId;
    }

    /**
     * Initiate new department, currently with default one -- 'Main Department'
     *
     * @param $adminId
     * @return mixed
     */
    private function initiateDepartment() {
        $department = new Department;
        $department->name = 'main_department';
        $department->display_name = 'Main Department';
        $department->description = 'The main department of system.';
        $department->manager = 0;
        $department->save();
        // get id of main department
        return DB::table('departments')->where('name', 'main_department')->pluck('id');
    }

    private function initiateCategory() {
        $category = new Category;
        $category->name = 'default_category';
        $category->display_name = 'Default Category';
        $category->description = 'This is default category.';
        $category->department_id = 1;
        $category->save();
        // get id of main department
        return DB::table('categories')->where('name', 'default_category')->pluck('id');
    }

    /**
     * Initiate new permissions.
     * According to requirements, currently permissions are:
     * CRUD user, role, permission, department, category, template, article
     *
     */
    private function initiatePermission() {
        // CRUD user
        $permission = new Permission();
        $permission->name = 'create_user';
        $permission->display_name = 'Create User';
        $permission->description = 'Permit to create a user';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'read_user';
        $permission->display_name = 'View User';
        $permission->description = 'Permit to view a user';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'update_user';
        $permission->display_name = 'Edit User';
        $permission->description = 'Permit to edit a user';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'delete_user';
        $permission->display_name = 'Delete User';
        $permission->description = 'Permit to delete a user';
        $permission->save();

        // CRUD role
        $permission = new Permission();
        $permission->name = 'create_role';
        $permission->display_name = 'Create Role';
        $permission->description = 'Permit to create a role';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'read_role';
        $permission->display_name = 'View Role';
        $permission->description = 'Permit to view a role';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'update_role';
        $permission->display_name = 'Edit Role';
        $permission->description = 'Permit to edit a role';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'delete_role';
        $permission->display_name = 'Delete Role';
        $permission->description = 'Permit to delete a role';
        $permission->save();

        // CRUD permission
        $permission = new Permission();
        $permission->name = 'create_permission';
        $permission->display_name = 'Create Permission';
        $permission->description = 'Permit to create a permission';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'read_permission';
        $permission->display_name = 'View Permission';
        $permission->description = 'Permit to view a permission';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'update_permission';
        $permission->display_name = 'Edit Permission';
        $permission->description = 'Permit to edit a permission';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'delete_permission';
        $permission->display_name = 'Delete Permission';
        $permission->description = 'Permit to delete a permission';
        $permission->save();

        // CRUD department
        $permission = new Permission();
        $permission->name = 'create_department';
        $permission->display_name = 'Create Department';
        $permission->description = 'Permit to create a department';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'read_department';
        $permission->display_name = 'View Department';
        $permission->description = 'Permit to view a department';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'update_department';
        $permission->display_name = 'Edit Department';
        $permission->description = 'Permit to edit a department';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'delete_department';
        $permission->display_name = 'Delete Department';
        $permission->description = 'Permit to delete a department';
        $permission->save();

        // CRUD category
        $permission = new Permission();
        $permission->name = 'create_category';
        $permission->display_name = 'Create Category';
        $permission->description = 'Permit to create a category';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'read_category';
        $permission->display_name = 'View Category';
        $permission->description = 'Permit to view a category';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'update_category';
        $permission->display_name = 'Edit Category';
        $permission->description = 'Permit to edit a category';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'delete_category';
        $permission->display_name = 'Delete Category';
        $permission->description = 'Permit to delete a category';
        $permission->save();

        // CRUD template
        $permission = new Permission();
        $permission->name = 'create_template';
        $permission->display_name = 'Create Template';
        $permission->description = 'Permit to create a template';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'read_template';
        $permission->display_name = 'View Template';
        $permission->description = 'Permit to view a template';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'update_template';
        $permission->display_name = 'Edit Template';
        $permission->description = 'Permit to edit a template';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'delete_template';
        $permission->display_name = 'Delete Template';
        $permission->description = 'Permit to delete a template';
        $permission->save();

        // CRUD article
        $permission = new Permission();
        $permission->name = 'create_article';
        $permission->display_name = 'Create Article';
        $permission->description = 'Permit to create a article';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'read_article';
        $permission->display_name = 'View Article';
        $permission->description = 'Permit to view a article';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'update_article';
        $permission->display_name = 'Edit Article';
        $permission->description = 'Permit to edit a article';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'delete_article';
        $permission->display_name = 'Delete Article';
        $permission->description = 'Permit to delete a article';
        $permission->save();

        // CRUD resource
        $permission = new Permission();
        $permission->name = 'create_resource';
        $permission->display_name = 'Create Resource';
        $permission->description = 'Permit to create a resource';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'read_resource';
        $permission->display_name = 'View Resource';
        $permission->description = 'Permit to view a resource';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'update_resource';
        $permission->display_name = 'Edit Resource';
        $permission->description = 'Permit to edit a resource';
        $permission->save();

        $permission = new Permission();
        $permission->name = 'delete_resource';
        $permission->display_name = 'Delete Resource';
        $permission->description = 'Permit to delete a resource';
        $permission->save();
    }

    /**
     * Initiate current relationships between permissions and roles, to their pivot table.
     */
    private function initiatePermissionRole() {
        /*
         * Permissions
         */
        // CRUD user
        $permissionToUser['create_user'] = DB::table('permissions')->where('name', 'create_user')->pluck('id');
        $permissionToUser['read_user'] = DB::table('permissions')->where('name', 'read_user')->pluck('id');
        $permissionToUser['update_user'] = DB::table('permissions')->where('name', 'update_user')->pluck('id');
        $permissionToUser['delete_user'] = DB::table('permissions')->where('name', 'delete_user')->pluck('id');
        // CRUD role
        $permissionToRole['create_role'] = DB::table('permissions')->where('name', 'create_role')->pluck('id');
        $permissionToRole['read_role'] = DB::table('permissions')->where('name', 'read_role')->pluck('id');
        $permissionToRole['update_role'] = DB::table('permissions')->where('name', 'update_role')->pluck('id');
        $permissionToRole['delete_role'] = DB::table('permissions')->where('name', 'delete_role')->pluck('id');
        // CRUD permission
        $permissionToPermission['create_permission'] = DB::table('permissions')->where('name', 'create_permission')->pluck('id');
        $permissionToPermission['read_permission'] = DB::table('permissions')->where('name', 'read_permission')->pluck('id');
        $permissionToPermission['update_permission'] = DB::table('permissions')->where('name', 'update_permission')->pluck('id');
        $permissionToPermission['delete_permission'] = DB::table('permissions')->where('name', 'delete_permission')->pluck('id');
        // CRUD department
        $permissionToDepartment['create_department'] = DB::table('permissions')->where('name', 'create_department')->pluck('id');
        $permissionToDepartment['read_department'] = DB::table('permissions')->where('name', 'read_department')->pluck('id');
        $permissionToDepartment['update_department'] = DB::table('permissions')->where('name', 'update_department')->pluck('id');
        $permissionToDepartment['delete_department'] = DB::table('permissions')->where('name', 'delete_department')->pluck('id');
        // CRUD category
        $permissionToCategory['create_category'] = DB::table('permissions')->where('name', 'create_category')->pluck('id');
        $permissionToCategory['read_category'] = DB::table('permissions')->where('name', 'read_category')->pluck('id');
        $permissionToCategory['update_category'] = DB::table('permissions')->where('name', 'update_category')->pluck('id');
        $permissionToCategory['delete_category'] = DB::table('permissions')->where('name', 'delete_category')->pluck('id');
        // CRUD template
        $permissionToTemplate['create_template'] = DB::table('permissions')->where('name', 'create_template')->pluck('id');
        $permissionToTemplate['read_template'] = DB::table('permissions')->where('name', 'read_template')->pluck('id');
        $permissionToTemplate['update_template'] = DB::table('permissions')->where('name', 'update_template')->pluck('id');
        $permissionToTemplate['delete_template'] = DB::table('permissions')->where('name', 'delete_template')->pluck('id');
        // CRUD article
        $permissionToArticle['create_article'] = DB::table('permissions')->where('name', 'create_article')->pluck('id');
        $permissionToArticle['read_article'] = DB::table('permissions')->where('name', 'read_article')->pluck('id');
        $permissionToArticle['update_article'] = DB::table('permissions')->where('name', 'update_article')->pluck('id');
        $permissionToArticle['delete_article'] = DB::table('permissions')->where('name', 'delete_article')->pluck('id');
        // CRUD resource
        $permissionToResource['create_resource'] = DB::table('permissions')->where('name', 'create_resource')->pluck('id');
        $permissionToResource['read_resource'] = DB::table('permissions')->where('name', 'read_resource')->pluck('id');
        $permissionToResource['update_resource'] = DB::table('permissions')->where('name', 'update_resource')->pluck('id');
        $permissionToResource['delete_resource'] = DB::table('permissions')->where('name', 'delete_resource')->pluck('id');

        // Administrator: user, role, permission, department, category, template, article
        $role = Role::where('name', 'administrator')->first();
        $role->attachPermissions(array(
            $permissionToUser['create_user'],
            $permissionToUser['read_user'],
            $permissionToUser['update_user'],
            $permissionToUser['delete_user'],
            $permissionToRole['create_role'],
            $permissionToRole['read_role'],
            $permissionToRole['update_role'],
            $permissionToRole['delete_role'],
            $permissionToPermission['create_permission'],
            $permissionToPermission['read_permission'],
            $permissionToPermission['update_permission'],
            $permissionToPermission['delete_permission'],
            $permissionToDepartment['create_department'],
            $permissionToDepartment['read_department'],
            $permissionToDepartment['update_department'],
            $permissionToDepartment['delete_department'],
            $permissionToCategory['create_category'],
            $permissionToCategory['read_category'],
            $permissionToCategory['update_category'],
            $permissionToCategory['delete_category'],
            $permissionToTemplate['create_template'],
            $permissionToTemplate['read_template'],
            $permissionToTemplate['update_template'],
            $permissionToTemplate['delete_template'],
            $permissionToArticle['create_article'],
            $permissionToArticle['read_article'],
            $permissionToArticle['update_article'],
            $permissionToArticle['delete_article'],
            $permissionToResource['create_resource'],
            $permissionToResource['read_resource'],
            $permissionToResource['update_resource'],
            $permissionToResource['delete_resource'],
        ));

        // Department Manager: user(part), department, category, template, article
        $role = Role::where('name', 'department_manager')->first();
        $role->attachPermissions(array(
            $permissionToUser['create_user'],
            $permissionToUser['read_user'],
            $permissionToUser['update_user'],
            $permissionToUser['delete_user'],
            $permissionToDepartment['create_department'],
            $permissionToDepartment['read_department'],
            $permissionToDepartment['update_department'],
            $permissionToDepartment['delete_department'],
            $permissionToCategory['create_category'],
            $permissionToCategory['read_category'],
            $permissionToCategory['update_category'],
            $permissionToCategory['delete_category'],
            $permissionToTemplate['create_template'],
            $permissionToTemplate['read_template'],
            $permissionToTemplate['update_template'],
            $permissionToTemplate['delete_template'],
            $permissionToArticle['create_article'],
            $permissionToArticle['read_article'],
            $permissionToArticle['update_article'],
            $permissionToArticle['delete_article'],
            $permissionToResource['create_resource'],
            $permissionToResource['read_resource'],
            $permissionToResource['update_resource'],
            $permissionToResource['delete_resource'],
        ));

        // Category Manager: user(part), category, template, article
        $role = Role::where('name', 'category_manager')->first();
        $role->attachPermissions(array(
            $permissionToUser['create_user'],
            $permissionToUser['read_user'],
            $permissionToUser['update_user'],
            $permissionToUser['delete_user'],
            $permissionToCategory['create_category'],
            $permissionToCategory['read_category'],
            $permissionToCategory['update_category'],
            $permissionToCategory['delete_category'],
            $permissionToTemplate['create_template'],
            $permissionToTemplate['read_template'],
            $permissionToTemplate['update_template'],
            $permissionToTemplate['delete_template'],
            $permissionToArticle['create_article'],
            $permissionToArticle['read_article'],
            $permissionToArticle['update_article'],
            $permissionToArticle['delete_article'],
            $permissionToResource['create_resource'],
            $permissionToResource['read_resource'],
            $permissionToResource['update_resource'],
            $permissionToResource['delete_resource'],
        ));

        // Article Manager: article
        $role = Role::where('name', 'article_manager')->first();
        $role->attachPermissions(array(
            $permissionToArticle['create_article'],
            $permissionToArticle['read_article'],
            $permissionToArticle['update_article'],
            $permissionToArticle['delete_article'],
            $permissionToResource['create_resource'],
            $permissionToResource['read_resource'],
            $permissionToResource['update_resource'],
            $permissionToResource['delete_resource'],
        ));
    }
}
