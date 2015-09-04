<?php

namespace App\Http\Controllers\Home;

use App\Category;
use App\Http\Requests\TemplateRequest;
use App\Template;
use App\TemplateSection;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TemplateController extends AuthController
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
        $pageTitle = 'All Templates';

        $originalTemplates = Template::all();

        $i = 0;
        $templates = array();
        foreach($originalTemplates as $originalTemplate) {
            $category = Category::find($originalTemplate->category_id);

            $isManageable = 0;
            // Administrator
            if(Auth::user()->hasRole(['administrator'])) {
                $isManageable = 1;
            }
            // Department Manager
            if(Auth::user()->hasRole(['department_manager'])) {
                if($category->department_id == Auth::user()->departments->first()->id) {
                    $isManageable = 1;
                }
            }
            // Category Manager
            if(Auth::user()->hasRole(['category_manager'])) {
                if($category->manager == Auth::user()->id) {
                    $isManageable = 1;
                }
            }

            $templates[$i] = array(
                'number'        => $i+1,
                'id'            => $originalTemplate->id,
                'name'          => $originalTemplate->name,
                'display_name'  => $originalTemplate->display_name,
                'description'   => $originalTemplate->description,
                'category'      => $category->display_name,
                'is_manageable' => $isManageable
            );

            $i++;
        }

        return view('home.templates.index', compact('pageTitle', 'templates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $pageTitle = 'Add New Template';

        $categories = Category::all(); // 需要修改，仅提供用户可以负责管理的categories

        return view('home.templates.create', compact('pageTitle', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(TemplateRequest $request)
    {
        $input = $request->all();

        $sectionNameAmount = count($input['section_name']);
        $sectionDisplayNameAmount = count($input['section_display_name']);
        $sectionDescriptionAmount = count($input['section_description']);
        $sectionEditableAmount = count($input['section_editable']);
        $sectionContentAmount = count($input['section_content']);

        if($sectionNameAmount != $sectionDisplayNameAmount
            || $sectionNameAmount != $sectionDescriptionAmount
            || $sectionNameAmount != $sectionEditableAmount
            || $sectionNameAmount != $sectionContentAmount) {
            dd('error.');
        }

        $newTemplate = array(
            'name'          => $input['template_name'],
            'display_name'  => $input['template_display_name'],
            'description'   => $input['template_description'],
            'category_id'   => $input['template_category']
        );
        $template = Template::create($newTemplate);

        for($i = 0; $i < $sectionNameAmount; $i++) {
            if($input['section_name'][$i]) {
                $newTemplateSection  = array(
                    'name'  => $input['section_name'][$i],
                    'display_name'  => $input['section_display_name'][$i],
                    'description'   => $input['section_description'][$i],
                    'content'   => $input['section_content'][$i],
                    'is_editable'   => $input['section_editable'][$i],
                    'template_id'   => $template->id
                );
                TemplateSection::create($newTemplateSection);
            }
        }

        return redirect('home/template');
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
        $template = Template::find($id);

        $pageTitle = 'Update Department: '.$template->display_name;

        $template->category_name = Category::find($template->category_id)->display_name;
        $template->sections = TemplateSection::where('template_id', '=', $template->id)->get()->toArray();

        return view('home.templates.edit', compact('pageTitle', 'template'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(TemplateRequest $request, $id)
    {
        $input = $request->all();

        // Template update
        $template = Template::find($id);
        $updateTemplate = array(
            'name'          => $input['template_name'],
            'display_name'  => $input['template_display_name'],
            'description'   => $input['template_description'],
            'category_id'   => $input['template_category']
        );
        $template->update($updateTemplate);

        // Template sections update
        for($i = 0; $i < count($input['section_name']); $i++) {
            $oneSection = array(
                'name'  => $input['section_name'][$i],
                'display_name'  => $input['section_display_name'][$i],
                'description'   => $input['section_description'][$i],
                'content'   => $input['section_content'][$i],
                'is_editable'   => $input['section_editable'][$i],
                'template_id'   => $id
            );

            $existedSection = TemplateSection::where('name', '=', $oneSection['name'])->get()->toArray();

            if($existedSection) {
                $templateSection = TemplateSection::find($existedSection['id']);
                $templateSection->update($oneSection);
            }
            else {
                if($oneSection['name']) {
                    TemplateSection::create($oneSection);
                }
            }
        }

        return redirect('home/template');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        Template::destroy($id);

        return 'deleted successfully.';
    }

    public function getTemplate($templateId)
    {
        $template = Template::find($templateId)->toArray();

        $templateSections = DB::table('template_sections')->where('template_id', $templateId)->get();

        $template['sections'] = $templateSections;

        return $template;
    }

    public function deleteTemplateSection($sectionId)
    {
        TemplateSection::destroy($sectionId);

        return 'deleted successfully.';
    }
}
