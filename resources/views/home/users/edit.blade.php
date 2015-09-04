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
                    // Problem happen here with the if value.
                    if('{{ $currentRole }}' != 'null') {
                        optionElements += '<option value="{{ $currentRole['id'] }}" selected>{{ $currentRole['display_name'] }}</option>';
                    }
                    for(var i = 0; i < result.length; i++) {
                        optionElements += '<option value="' + result[i].id + '">' + result[i].display_name + '</option>';
                    }
                    roleSelector.append(optionElements);

                    $('#user-role').select2({
                        minimumResultsForSearch: Infinity
                    });
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
                        if(selectedDepartment == '{{ $currentDepartment['id'] }}') {
                            optionElements += '<option value="{{ $currentRole['id'] }}" selected>{{ $currentRole['display_name'] }}</option>';
                        }
                        for(var i = 0; i < result.length; i++) {
                            optionElements += '<option value="' + result[i].id + '">' + result[i].display_name + '</option>';
                        }
                        roleSelector.append(optionElements);

                        $('#user-role').select2({
                            minimumResultsForSearch: Infinity
                        });
                    }
                });
            });
        });
    </script>
@stop

@section('content')
    <div id="user-create-form" class="content-block-wrapper content-block-text">
        <div class="content-block-form">
            {!!Form::open(['action' => ['Home\UserController@update', $user->id], 'method' => 'PUT'])!!}
            <div class="form-group">
                <label for="user-name">Name</label>
                <input type="text" name="name" class="form-control" id="user-name" placeholder="" value="{{ $user->name }}">
            </div>
            <!-- Information to alert users cannot change email address any more. -->
            <div class="form-group">
                <div id="user-create-form-alert" class="alert alert-danger" role="alert">
                    <label>N.B.</label>
                    <p>
                        Currently, once user account is created, it is forbidden to change the register email address.
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label for="user-email">Email</label>
                <input type="email" name="email" class="form-control" id="user-email" placeholder="" value="{{ $user->email }}" readonly>
            </div>
            <div class="form-group">
                <label for="user-password">Password</label>
                <input type="password" name="password" class="form-control" id="user-password" placeholder="" value="{{ $user->password }}">
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
                        <li>
                            If you do not change anything below, it will be fully remained.
                        </li>
                    </ul>
                </div>
            </div>
            <div class="form-group">
                <label for="user-department">Department</label>
                <p>
                    <b>
                        Current Department:
                    </b>
                    {{ $currentDepartment['display_name'] }}
                </p>
                <select name="department" class="form-control" id="user-department">
                    @foreach($departments as $department)
                        @if($department['id'] == $currentDepartment['id'])
                            <option value="{{ $department['id'] }}" selected>{{ $department['display_name'] }}</option>
                        @else
                            <option value="{{ $department['id'] }}">{{ $department['display_name'] }}</option>
                        @endif
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
                <p>
                    <b>
                        Current Role:
                    </b>
                    @if($currentRole)
                        {{ $currentRole['display_name'] }}
                    @else
                        TBD
                    @endif
                </p>
                <select name="role" class="form-control" id="user-role">
                    <option value="0">Select a role...</option>
                </select>
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
            {!!Form::close()!!}
        </div>
    </div>
@stop