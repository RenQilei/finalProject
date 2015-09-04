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

            var categorySelector = $('select[name=article_category_id]');
            var templateSelector = $('select[name=article_template_id]');

            categorySelector.change(function() {
                var categoryId = $(this).val();
                var optionElements = '';
                if(categoryId == '0') {
                    /* 提示必选，删除已加载option */
                    var templateSelectorChildren = templateSelector.children();
                    for (var i = 1; i < templateSelectorChildren.length; i++) {
                        templateSelectorChildren[i].remove();
                    }
                }
                else {
                    $.ajax({
                        method: 'GET',
                        url: '{{ url('home/category/get_category_template_list') }}/' + categoryId,
                        async: false,
                        success: function (result) {
                            console.log(result);
                            for(var i = 0; i < result.length; i++) {
                                optionElements += '<option value="' + result[i].id + '">' + result[i].display_name + '</option>';
                            }

                            templateSelector.append(optionElements);
                        }
                    });
                }
            });

            var contentBlockMain = $('#content-block-main');
            templateSelector.change(function() {
                var templateId = $(this).val();
                var childrenElements = contentBlockMain.children();
                var i = 0;
                var contentElement = '';
                if(templateId == '0') {
                    for(i = 0; i < childrenElements.length; i++) {
                        childrenElements[i].remove();
                    }
                    contentElement = '<textarea name="section_content[]" class="form-control" rows="10"></textarea>' +
                        '<input name="template_section[]" value="0" type="hidden">';
                    contentBlockMain.append(contentElement);
                }
                else {
                    for(i = 0; i < childrenElements.length; i++) {
                        childrenElements[i].remove();
                    }
                    contentElement = '';
                    $.ajax({
                        method: 'GET',
                        url: '{{ url('home/template/get_template') }}/' + templateId,
                        async: false,
                        success: function (result) {
                            contentElement +=
                                    '<div class="form-group">\
                                        <p><b>Template Description:</b>&nbsp;' + result.description + '</p>\
                                    </div>';
                            var sections = result.sections;
                            for(i = 0; i < sections.length; i++) {
                                if(sections[i].is_editable) {
                                    contentElement +=
                                            '<div class="template-section-block-wrapper">\
                                                <div class="form-group">\
                                                    <label>Section Name:&nbsp;' + sections[i].display_name + '</label>\
                                                    <p><b>Section Description:</b>&nbsp;' + sections[i].description + '</p>\
                                                    <textarea name="section_content[]" class="form-control">' + sections[i].content + '</textarea>\
                                                    <input name="template_section[]" value="' + sections[i].id + '" type="hidden">\
                                                </div>\
                                            </div>';
                                }
                                else {
                                    contentElement +=
                                            '<div class="template-section-block-wrapper">\
                                                <div class="form-group">\
                                                    <label>' + sections[i].display_name + '</label>\
                                                    <p>' + sections[i].description + '</p>\
                                                    <p>' + sections[i].content + '</p>\
                                                </div>\
                                            </div>';
                                }
                            }
                        }
                    });
                    contentBlockMain.append(contentElement);
                }

                loadTinyMCE();
            });
        });
    </script>
@stop

@section('content')
    <div id="article-create-form" class="content-block-wrapper">
        <div class="content-block-form">
            {!!Form::open(['action' => 'Home\ArticleController@store'])!!}
            <div class="form-group">
                <label for="article-title">Title</label>
                <input type="text" name="article_title" class="form-control" id="article-title" placeholder="">
            </div>
            <div class="form-group">
                <label for="article-category">Category</label>
                <select name="article_category_id" class="form-control" id="article-category">
                    <option value="">Select Category...</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">
                            {{ $category->display_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="article-template">Template</label>
                <select name="article_template_id" class="form-control" id="article-template">
                    <option value="0">No Template</option>
                </select>
            </div>
            <div id="content-block-main" class="form-group">
                <textarea name="section_content[]" class="form-control" rows="10"></textarea>
                <input name="template_section[]" value="0" type="hidden">
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
            {!!Form::close()!!}
        </div>
    </div>
@stop