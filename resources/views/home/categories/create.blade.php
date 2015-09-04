@extends('frameworks.home-basic')

@section('head-extension')

@stop

@section('script-extension')
    <script type="text/javascript">
        $('#user-department').select2({
            minimumResultsForSearch: Infinity
        });
        var category = $('#category-manager');
        if(category) {
            category.select2({
                minimumResultsForSearch: Infinity
            });
        }
        $('#category-parent').select2({
            minimumResultsForSearch: Infinity
        });
    </script>

    <script>
        $(document).ready(function() {
            var departmentSelector = $('select[name=department_id]');
            var managerSelector = $('select[name=manager]');
            var categorySelector = $('select[name=parent_category]');

            // Initiate
            var optionElements = '';
            var selectedDepartment = departmentSelector.val();

            // Manager
            if(managerSelector) {
                $.ajax({
                    method: 'GET',
                    url: '{{ url('home/user/get_category_managers') }}/' + selectedDepartment,
                    async: false,
                    success: function (result) {
                        console.log(result);
                        for(var i = 0; i < result.length; i++) {
                            optionElements += '<option value="' + result[i].id + '">' + result[i].display_name + '</option>';
                        }
                        managerSelector.append(optionElements);
                    }
                });
            }

            // Parent Category
            optionElements = '';
            $.ajax({
                method: 'GET',
                url: '{{ url('home/category/get_available_parent_categories') }}/' + selectedDepartment,
                async: false,
                success: function (result) {
                    for(var i = 0; i < result.length; i++) {
                        optionElements += '<option value="' + result[i].id + '">' + result[i].display_name + '</option>';
                    }
                    categorySelector.append(optionElements);
                }
            });

            // Select a new department
            departmentSelector.change(function() {
                selectedDepartment = $(this).val();

                //Manager
                if(managerSelector) {
                    var managerSelectorChildren = managerSelector.children();
                    for (var i = 1; i < managerSelectorChildren.length; i++) {
                        managerSelectorChildren[i].remove();
                    }
                    optionElements = '';
                    $.ajax({
                        method: 'GET',
                        url: '{{ url('home/user/get_category_managers') }}/' + selectedDepartment,
                        async: false,
                        success: function (result) {
                            console.log(result);
                            for (var i = 0; i < result.length; i++) {
                                optionElements += '<option value="' + result[i].id + '">' + result[i].name + '</option>';
                            }
                            managerSelector.append(optionElements);
                        }
                    });
                }

                // Parent Category
                var categorySelectorChildren = categorySelector.children();
                for(var i = 1; i < categorySelectorChildren.length; i++) {
                    categorySelectorChildren[i].remove();
                }
                optionElements = '';
                $.ajax({
                    method: 'GET',
                    url: '{{ url('home/category/get_available_parent_categories') }}/' + selectedDepartment,
                    async: false,
                    success: function (result) {
                        for(var i = 0; i < result.length; i++) {
                            optionElements += '<option value="' + result[i].id + '">' + result[i].display_name + '</option>';
                        }
                        categorySelector.append(optionElements);
                    }
                });
            });
        });
    </script>
@stop

@section('content')
    <div id="category-create-form" class="content-block-wrapper">
        <div class="content-block-form">
            {!!Form::open(['action' => 'Home\CategoryController@store'])!!}
            <div class="form-group">
                <label for="category-name">Name</label>
                <input type="text" name="name" class="form-control" id="category-name" placeholder="">
            </div>
            <div class="form-group">
                <label for="category-display-name">Display Name</label>
                <input type="text" name="display_name" class="form-control" id="category-display-name" placeholder="">
            </div>
            <div class="form-group">
                <label for="category-description">Description</label>
                <input type="text" name="description" class="form-control" id="category-description" placeholder="">
            </div>
            <div class="form-group">
                <label for="user-department">Department</label>
                <select name="department_id" class="form-control" id="user-department">
                    @foreach($departments as $department)
                        <option value="{{ $department['id'] }}">
                            {{ $department['display_name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="category-parent">Parent Category</label>
                <select name="parent_category" class="form-control" id="category-parent">
                    <option value="0">This is root.</option>
                </select>
            </div>
            @if(Auth::user()->hasRole(['administrator', 'department_manager']))
            <div class="form-group">
                <label for="category-manager">Manager</label>
                <select name="manager" class="form-control" id="category-manager">
                    <option value="0">TBD</option>
                </select>
            </div>
            @endif
            <button type="submit" class="btn btn-default">Submit</button>
            {!!Form::close()!!}
        </div>
    </div>
@stop