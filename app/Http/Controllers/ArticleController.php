<?php

namespace App\Http\Controllers;

use App\Article;
use App\ArticleSection;
use App\Template;
use App\TemplateSection;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function staticShow($id)
    {
        $article = Article::find($id);

        $articleArchiveName = $article->id.'_'.str_replace(' ', '_', $article->title).'_'.str_replace(array(' ', '-', ':'), '_', $article->updated_at).'.html';

        $disk = Storage::disk('local');

        if($disk->exists('articles/'.$articleArchiveName)) {
            // already have static archive

//            $timeBeforeRetrieving = microtime(TRUE);

//            $archiveArticle = $disk->get('articles/'.$articleArchiveName);

//            $timeAfterRetrieving = microtime(TRUE);

//            $timeIntervalForRetrieving = $timeAfterRetrieving - $timeBeforeRetrieving;

//            echo $archiveArticle;

//            echo '<br/><br/><hr/><b>Efficiency Analysis:</b>';
//            echo '<br/>Time of retrieving article from archive: ';
//            echo '<b>'.number_format($timeIntervalForRetrieving, 8).' seconds</b>';

//            $timeBeforeRequestingDB = microtime(TRUE);
//            $this->getMainArticles($article);
//            $timeAfterRequestingDB = microtime(TRUE);

//            $timeIntervalForRequestingDB = $timeAfterRequestingDB - $timeBeforeRequestingDB;

//            echo '<br/>Time of requesting article from Database:';
//            echo '<b>'.number_format($timeIntervalForRequestingDB, 8).' seconds</b>';

//            echo '<br/><br/>Currently, <br/>\'Database Requesting Time Interval\' - \'Archive Retrieving Time Interval\' = ';
//            echo '<b>'.number_format(($timeIntervalForRequestingDB - $timeIntervalForRetrieving) * 1000, 2).'</b> milliseconds';

            return $disk->get('articles/'.$articleArchiveName);

            // Only send back the message, instead of the whole file.
//            return 'Static Article Existed!';
        }
        else {
            // no static archive

            $articleArchiveName = $this->generateStaticPage($article);

            return redirect('static/article/'.$article->id);

        }
    }

    public function dynamicShow($id) {

        $article = Article::find($id);
        $user = User::find($article->author)->first();

        $articleContents = $this->getMainArticles($article);

        return view('article.show', compact('article', 'user', 'articleContents'));
    }

    public function generateStaticUrls() {
        $disk = Storage::disk('local');

        if(!$disk->exists('statictesturls')) {

            $article = Article::all();

            $contents = "";
            for($i = 1; $i <= count($article); $i++) {
                $contents = $contents."http://localhost:8000/static/article/".$i."\n";
            }

            Storage::put('statictesturls', $contents);
        }
    }

    public function generateDynamicUrls() {
        $disk = Storage::disk('local');

        if(!$disk->exists('dynamictesturls')) {

            $article = Article::all();

            $contents = "";
            for($i = 1; $i <= count($article); $i++) {
                $contents = $contents."http://localhost:8000/dynamic/article/".$i."\n";
            }

            Storage::put('dynamictesturls', $contents);
        }
    }

    public function generateStaticFiles() {
//        for($i = 1; $i <= count(Article::all()); $i++) {
            // request for all articles
            $url = url('static/article/1');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $output = curl_exec($ch);
            curl_close($ch);
            print_r($output);
//        }

        return "successfully generated!";
    }

    private function generateStaticPage($article)
    {
        $mainArticles = $this->getMainArticles($article);

        $user = User::find($article->author)->first();

        $contents = '
            <!DOCTYPE html>
            <html>
            <body>
                <meta charset="utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>'.$article->title.'</title>
            </body>
            <head>
                <h1>
                    '.$article->title.'
                </h1>
                <h3>
                    Author:'.$user->name.'
                </h3>
                <h3>
                    Last Modified Time:'.$article->updated_at.'
                </h3>
                '.$mainArticles.'
            </head>
            </html>';

        $articleArchiveName = $article->id.'_'.
            str_replace(' ', '_', $article->title).'_'.
            str_replace(array(' ', '-', ':'), '_', $article->updated_at).'.html';

        Storage::put('articles/'.$articleArchiveName, $contents);

        return $articleArchiveName;
    }

    private function getMainArticles($article)
    {
        $mainArticles = '';

        if($article->template_id == 0) {
            // without template

            $articleSection = DB::table('article_sections')->where('article_id', $article->id)->first()->content;

            $mainArticles = $mainArticles.$articleSection;

        }
        else {
            // with template

            $template = Template::find($article->template_id);

            $templateSections = $template->templateSections()->get();

            foreach($templateSections as $templateSection) {
                if($templateSection->is_editable == 0) {
                    $mainArticles = $mainArticles.$templateSection->content;
                }
                else {
                    $articleSection = DB::table('article_sections')
                        ->where(array(
                            'template_section_id' => $templateSection->id,
                            'article_id' => $article->id
                        ))->first()->content;
                    $mainArticles = $mainArticles.$articleSection;
                }
            }
        }

        return $mainArticles;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
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
