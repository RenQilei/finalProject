@extends('frameworks.home-basic')

@section('head-extension')

@stop

@section('script-extension')
    <script type="text/javascript">
        $('#template-category').select2({
            minimumResultsForSearch: Infinity
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {

            loadTinyMCE();

            var element =
                    '<li>\
                        <div class="template-section-block-wrapper">\
                            <div class="template-section-block-delete">\
                                <i class="zmdi zmdi-minus-circle-outline zmdi-hc-lg zmdi-hc-fw"></i>\
                            </div>\
                            <div class="form-group">\
                                <label>Section Name</label>\
                                <input type="text" name="section_name[]" class="form-control" placeholder="">\
                            </div>\
                            <div class="form-group">\
                                <label>Section Display Name</label>\
                                <input type="text" name="section_display_name[]" class="form-control" placeholder="">\
                            </div>\
                            <div class="form-group">\
                                <label>Section Description</label>\
                                <input type="text" name="section_description[]" class="form-control" placeholder="">\
                            </div>\
                            <div class="form-group">\
                                <label>Is editable</label>\
                                <select name="section_editable[]" class="form-control" id="template-category"><!-- id并不对 -->\
                                    <option value="1" selected>Yes</option>\
                                    <option value="0">No</option>\
                                </select>\
                            </div>\
                            <div class="form-group">\
                                <label>Section Content</label>\
                                <textarea name="section_content[]" class="form-control" placeholder=""></textarea>\
                            </div>\
                        </div>\
                    </li>';

            $('#template-section-block-append').click(function() {
                $('#template-section-block-list').append(element);

                loadTinyMCE();
            });

            $('#template-section-block-list').delegate('.template-section-block-delete', 'click', function() {
                $(this).parents('li').remove();
            });
        });
    </script>
@stop

@section('content')
    <div id="template-create-form">
    {!!Form::open(['action' => 'Home\TemplateController@store'])!!}
        <!-- START: template section block -->
        <div id="template-section-block" class="content-block-wrapper">
            <div class="content-block-form">
                <ul id="template-section-block-list" class="list-unstyled">
                    <li>
                        <div class="template-section-block-wrapper">
                            <div class="form-group">
                                <label>Section Name</label>
                                <input type="text" name="section_name[]" class="form-control" placeholder="">
                            </div>
                            <div class="form-group">
                                <label>Section Display Name</label>
                                <input type="text" name="section_display_name[]" class="form-control" placeholder="">
                            </div>
                            <div class="form-group">
                                <label>Section Description</label>
                                <input type="text" name="section_description[]" class="form-control" placeholder="">
                            </div>
                            <div class="form-group">
                                <label>Is editable</label>
                                <select name="section_editable[]" class="form-control" id="template-category"><!-- id并不对 -->
                                    <option value="1" selected>Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Section Content</label>
                                <textarea name="section_content[]" class="form-control" placeholder=""></textarea>
                            </div>
                        </div>
                    </li>
                </ul>
                <div id="template-section-block-append" class="template-section-block-wrapper">
                    <i class="zmdi zmdi-plus-circle-o zmdi-hc-lg zmdi-hc-fw"></i>
                </div>
            </div>
        </div>
        <!-- END: template section block -->
        <!-- START: template information block -->
        <div id="template-information-block" class="content-block-wrapper">
            <div class="content-block-form">
                <div class="form-group">
                    <label for="template-name">Name</label>
                    <input type="text" name="template_name" class="form-control" id="template-name" placeholder="">
                </div>
                <div class="form-group">
                    <label for="template-display-name">Display Name</label>
                    <input type="text" name="template_display_name" class="form-control" id="template-display-name" placeholder="">
                </div>
                <div class="form-group">
                    <label for="template-description">Description</label>
                    <input type="text" name="template_description" class="form-control" id="template-description" placeholder="">
                </div>
                <div class="form-group">
                    <label for="template-category">Category</label>
                    <select name="template_category" class="form-control" id="template-category">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-default">Submit</button>
            </div>
        </div>
        <!-- END: template information block -->
        <div class="clearfix"></div>
    {!!Form::close()!!}
    </div>
@stop