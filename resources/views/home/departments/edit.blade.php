@extends('frameworks.home-basic')

@section('head-extension')

@stop

@section('script-extension')
    <script type="text/javascript">
        $('#department-parent').select2({
            minimumResultsForSearch: Infinity
        });
    </script>
@stop

@section('content')
    <div id="department-create-form" class="content-block-wrapper">
        <div class="content-block-form">
            {!!Form::open(['action' => ['Home\DepartmentController@update', $department->id], 'method' => 'PUT'])!!}
            <div class="form-group">
                <label for="department-name">Name</label>
                <input type="text" name="name" class="form-control" id="department-name" placeholder="" value="{{ $department->name }}">
            </div>
            <div class="form-group">
                <label for="department-display-name">Display Name</label>
                <input type="text" name="display_name" class="form-control" id="department-display-name" placeholder="" value="{{ $department->display_name }}">
            </div>
            <div class="form-group">
                <label for="department-description">Description</label>
                <input type="text" name="description" class="form-control" id="department-description" placeholder="" value="{{ $department->description }}">
            </div>
            <div class="form-group">
                <label for="department-parent">Parent Department</label>
                <select name="parent_department" class="form-control" id="department-parent">
                    {{--@foreach($existedDepartments as $existedDepartment)--}}
                        {{--<option value="{{ $existedDepartment->id }}">--}}
                            {{--{{ $existedDepartment->display_name }}--}}
                        {{--</option>--}}
                    {{--@endforeach--}}
                    <option value="{{ $department->parent_department }}">
                        {{ $department->parent_department_name }}
                    </option>
                </select>
            </div>
            <div class="form-group">
                    <span>
                        <a href="">
                            + Add a new user as manager (developing...)
                        </a>
                    </span>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span>
                        <a href="">
                            + Select a manager (developing...)
                        </a>
                    </span>
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
            {!!Form::close()!!}
        </div>
    </div>
@stop