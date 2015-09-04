<?php

namespace App\Http\Controllers\Home;

use App\Article;
use App\ArticleSection;
use App\Category;
use App\Http\Requests\ArticleRequest;
use App\Template;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ArticleController extends AuthController
{
    /**
     * Allow administrator, department manager, category manager, article manager to access this class and its functions.
     */
    public function __construct()
    {
        parent::__construct();

        if(Auth::user() && !Auth::user()->hasRole(['administrator', 'department_manager', 'category_manager', 'article_manager'])) {
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
        $pageTitle = 'All Articles';

        $originalArticles = Article::all();

        $i = 0;
        $articles = array();
        foreach($originalArticles as $originalArticle) {
            $author = User::find($originalArticle->author);
            $category = Category::find($originalArticle->category_id);

            $isManageable = 0;
            // administrator
            if(Auth::user()->hasRole(['administrator'])) {
                $isManageable = 1;
            }
            // department manager
            if(Auth::user()->hasRole(['department_manager'])) {
                if(Auth::user()->departments->first()->id == $author->departments->first()->id) {
                    $isManageable = 1;
                }
            }
            // category manager
            if(Auth::user()->hasRole(['category_manager'])) {
                if(Auth::user()->id == $category->manager) {
                    $isManageable = 1;
                }
            }
            // article manager
            if(Auth::user()->hasRole(['article_manager'])) {
                if(Auth::user()->id == $author->id) {
                    $isManageable = 1;
                }
            }

            $articles[$i] = array(
                'id'            => $i+1,
                'title'         => $originalArticle->title,
                'author'        => $author->name,
                'category'      => $category->display_name,
                'updated_at'    => $originalArticle->updated_at,
                'is_manageable' => $isManageable
            );
            $i++;
        }

        return view('home.articles.index', compact('pageTitle', 'articles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $pageTitle = 'Add New Article';

        // Administrator
        if(Auth::user()->hasRole(['administrator'])) {
            $categories = Category::all();
        }
        // Department Manager
        if(Auth::user()->hasRole(['department_manager'])) {
            $department = Auth::user()->departments->first();
            $categories = Category::where('department_id', '=', $department->id)->get();
        }
        // Category Manager
        if(Auth::user()->hasRole(['category_manager'])) {
            $categories = Category::where('manager', '=', Auth::user()->id);
        }
        // Article Manager
        // Who can write for all categories of department which is allocated.
        // Currently, article managers are separately with category managers
        if(Auth::user()->hasRole(['article_manager'])) {
            $department = Auth::user()->departments->first();
            $categories = Category::where('department_id', '=', $department->id)->get();
        }

        return view('home.articles.create', compact('pageTitle', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(ArticleRequest $request)
    {
        $input = $request->all();

        $sectionContentAmount = count($input['section_content']);
        $templateSectionAmount = count($input['template_section']);

        if($sectionContentAmount != $templateSectionAmount) {
            dd("error.");
        }

        $newArticle = array(
            'title'         => $input['article_title'],
            'author'        => Auth::user()->id,
            'category_id'   => $input['article_category_id'],
            'template_id'   => $input['article_template_id']
        );
        $article = Article::create($newArticle);

        for($i = 0; $i < $templateSectionAmount; $i++) {
            $newArticleSection = array(
                'content'               => $input['section_content'][$i],
                'template_section_id'   => $input['template_section'][$i],
                'article_id'            => $article->id
            );
            ArticleSection::create($newArticleSection);
        }

        return redirect('home/article');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $article = Article::find($id);

        $pageTitle = 'Update Department: '.$article->display_name;

        $article->category_name = Category::find($article->category_id)->display_name;
        $article->template = ($article->template_id != 0) ? Template::find($article->template_id)->toArray() : null;
        $article->sections = $article->articleSections()->get()->toArray();

        return view('home.articles.edit', compact('pageTitle', 'article'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(ArticleRequest $request, $id)
    {
        $input = $request->all();

        $article = Article::find($id);
        $article->title = $input['article_title'];
        $article->save();

        for($i = 0; $i < count($input['section_content']); $i++) {
            $articleSection = ArticleSection::find($input['section_id'][$i]);
            $articleSection->content = $input['section_content'][$i];
            $articleSection->save();
        }

        return redirect('home/article');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
