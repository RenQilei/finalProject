@extends('frameworks.home-basic')

@section('head-extension')

@stop

@section('script-extension')
    <script type="text/javascript">
        $('#article-category').select2({
            minimumResultsForSearch: Infinity
        });
        $('#article-template').select2({
            minimumResultsForSearch: Infinity
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            loadTinyMCE();
        });
    </script>
@stop

@section('content')
    <div id="article-create-form" class="content-block-wrapper">
        <div class="content-block-form">
            {!!Form::open(['action' => ['Home\ArticleController@update', $article->id], 'method' => 'PUT'])!!}
            <div class="form-group">
                <label for="article-title">Title</label>
                <input type="text" name="article_title" class="form-control" id="article-title" placeholder="" value="{{ $article->title }}">
            </div>
            <div class="form-group">
                <label for="article-category">Category</label>
                <select name="article_category_id" class="form-control" id="article-category">
                    <option value="{{ $article->category_id }}">{{ $article->category_name }}</option>
                </select>
            </div>
            <div class="form-group">
                <label for="article-template">Template</label>
                <select name="article_template_id" class="form-control" id="article-template">
                    <option value="{{ $article->template_id }}">
                        @if($article->template_id == 0)
                            No template
                        @else
                            {{ $article->template['display_name'] }}
                        @endif
                    </option>
                </select>
            </div>
            <div id="content-block-main" class="form-group">
                @if($article->template_id == 0)
                    <textarea name="section_content[]" class="form-control" rows="10">
                    {{ $article->sections[0]['content'] }}
                </textarea>
                    <input name="template_section[]" value="0" type="hidden">
                    <input name="section_id[]" value="{{ $article->sections[0]['id'] }}" type="hidden">
                @else
                    @foreach($article->sections as $section)
                        <textarea name="section_content[]" class="form-control" rows="10">
                        {{ $section['content'] }}
                    </textarea>
                        <input name="template_section[]" value="{{ $section['template_section_id'] }}" type="hidden">
                        <input name="section_id[]" value="{{ $section['id'] }}" type="hidden">
                    @endforeach
                @endif
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
            {!!Form::close()!!}
        </div>
    </div>
@stop