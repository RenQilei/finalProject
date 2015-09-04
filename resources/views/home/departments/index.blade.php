@extends('frameworks.home-basic')

@section('head-extension')

@stop

@section('script-extension')
    <script>
        function sweetAlertDelete(departmentId, departmentName) {
            swal({
                title: "Are you sure?",
                text: "You will permanently delete department " + departmentName + "!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false
            }, function(){
                $.ajax({
                    method: 'POST',
                    url: '{{ url('home/department') }}/' + departmentId,
                    data: {_method: 'delete', _token : '{{csrf_token()}}'},
                    async: false,
                    success: function (result) {
                        console.log(result);
                        swal("Deleted!", "Department has been deleted.", "success");
                        location.reload();
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
                            Display Name
                        </th>
                        <th>
                            Description
                        </th>
                        <th>
                            Manager
                        </th>
                        <th>
                            Belongs to
                        </th>
                        <th>
                            Management
                        </th>
                    </tr>
                </thead>
                <tbody>
                @foreach($departments as $department)
                    <tr>
                        <td>
                            {{ $department['number'] }}
                        </td>
                        <td>
                            {{ $department['name'] }}
                        </td>
                        <td>
                            {{ $department['display_name'] }}
                        </td>
                        <td>
                            {{ $department['description'] }}
                        </td>
                        <td>
                            {{ $department['manager'] }}
                        </td>
                        <td>
                            {{ $department['parent_department'] }}
                        </td>
                        <td>
                        @if($department['is_manageable'])
                            <ul class="list-inline content-table-list content-table-list-manage">
                                <li>
                                    <a href="{{ url('home/department/'.$department['id'].'/edit') }}">
                                        Edit
                                    </a>
                                </li>
                                <li>
                                    <a onclick="sweetAlertDelete('{{ $department['id'] }}', '{{ $department['display_name'] }}')">
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