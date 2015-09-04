@extends('frameworks.home-basic')

@section('head-extension')

@stop

@section('script-extension')
    <script>
        function sweetAlertDelete(userId, userName) {
            swal({
                title: "Are you sure?",
                text: "You will permanently delete user " + userName + "!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false
            }, function(){
                $.ajax({
                    method: 'POST',
                    url: '{{ url('home/user') }}/' + userId,
                    data: {_method: 'delete', _token : '{{csrf_token()}}'},
                    async: false,
                    success: function (result) {
                        console.log(result);
                        swal("Deleted!", "User has been deleted.", "success");
                        setTimeout(function(){
                            location.reload();
                        }, 3000);
                    }
                });
            });
        }
    </script>
@stop

@section('content')
    <div id="department-index-list" class="content-block-wrapper">
        <div class="content-block-table">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>
                        #
                    </th>
                    <th>
                        Name
                    </th>
                    <th>
                        Email
                    </th>
                    <th>
                        Department
                    </th>
                    <th>
                        Role
                    </th>
                    <th>
                        Management
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>
                            {{ $user['number'] }}
                        </td>
                        <td>
                            {{ $user['name'] }}
                        </td>
                        <td>
                            {{ $user['email'] }}
                        </td>
                        <td>
                            {{ $user['department'] }}
                        </td>
                        <td>
                            {{ $user['role'] }}
                        </td>
                        <td>
                        @if($user['is_manageable'])
                            <ul class="list-inline content-table-list content-table-list-manage">
                                <li>
                                    <a href="{{ url('home/user/'.$user['id'].'/edit') }}">
                                        Edit
                                    </a>
                                </li>
                                <li>
                                    <a onclick="sweetAlertDelete('{{ $user['id'] }}', '{{ $user['name'] }}')">
                                        Delete
                                    </a>
                                </li>
                            </ul>
                        @else
                            --
                        @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop