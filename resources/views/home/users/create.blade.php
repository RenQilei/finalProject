@extends('frameworks.home-basic')

@section('head-extension')

@stop

@section('script-extension')
    <script type="text/javascript">
        $('#user-department').select2({
            minimumResultsForSearch: Infinity
        });
        $('#user-role').select2({
            minimumResultsForSearch: Infinity
        });
    </script>

    <script>
        $(document).ready(function() {
            var departmentSelector = $('select[name=department]');
            var roleSelector = $('select[name=role]');
            // Initiate
            var optionElements = '';
            // First load of page
            var selectedDepartment = departmentSelector.val();
            $.ajax({
                method: 'GET',
                url: '{{ url('home/user/get_available_roles') }}/' + selectedDepartment,
                async: false,
                success: function (result) {
                    for(var i = 0; i < result.length; i++) {
                        optionElements += '<option value="' + result[i].id + '">' + result[i].display_name + '</option>';
                    }
                    roleSelector.append(optionElements);
                }
            });
            // New selection
            departmentSelector.change(function() {
                selectedDepartment = $(this).val();
                var roleSelectorChildren = roleSelector.children();
                for(var i = 1; i < roleSelectorChildren.length; i++) {
                    roleSelectorChildren[i].remove();
                }
                optionElements = '';
                $.ajax({
                    method: 'GET',
                    url: '{{ url('home/user/get_available_roles') }}/' + selectedDepartment,
                    async: false,
                    success: function (result) {
                        for(var i = 0; i < result.length; i++) {
                            optionElements += '<option value="' + result[i].id + '">' + result[i].display_name + '</option>';
                        }
                        roleSelector.append(optionElements);
                    }
                });
            });
        });
    </script>
@stop

@section('content')
    <div id="user-create-form" class="content-block-wrapper content-block-text">
        <div class="content-block-form">
            {!!Form::open(['action' => 'Home\UserController@store'])!!}
                <div class="form-group">
                    <label for="user-name">Name</label>
                    <input type="text" name="name" class="form-control" id="user-name" placeholder="">
                </div>
                <div class="form-group">
                    <label for="user-email">Email</label>
                    <input type="email" name="email" class="form-control" id="user-email" placeholder="">
                </div>
                <div class="form-group">
                    <label for="user-password">Password</label>
                    <input type="password" name="password" class="form-control" id="user-password" placeholder="">
                </div>
                <div class="form-group">
                    <label for="user-password-again">Password Again</label>
                    <input type="password" name="password_again" class="form-control" id="user-password-again" placeholder="">
                </div>
                <!-- Information to alert users about selecting department and roles when creating new account. -->
                <div class="form-group">
                    <div id="user-create-form-alert" class="alert alert-info" role="alert">
                        <label>N.B.</label>
                        <ul>
                            <li>
                                Department is mandatory, while default is same department as current manager (you).
                            </li>
                            <li>
                                Role is optional, and the available options depend on department chosen above.
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="form-group">
                    <label for="user-department">Department</label>
                    <select name="department" class="form-control" id="user-department">
                    @foreach($departments as $department)
                        <option value="{{ $department['id'] }}">{{ $department['display_name'] }}</option>
                    @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <a href="">
                        + Add a new department (developing...)
                    </a>
                </div>
                <div class="form-group">
                    <label for="user-role">Role</label>
                    <select name="role" class="form-control" id="user-role">
                        <option value="0">Select a role...</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-default">Submit</button>
            {!!Form::close()!!}
        </div>
    </div>
@stop