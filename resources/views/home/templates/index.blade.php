@extends('frameworks.home-basic')

@section('head-extension')

@stop

@section('script-extension')
    <script>
        function sweetAlertDelete(templateId, templateName) {
            swal({
                title: "Are you sure?",
                text: "You will permanently delete template " + templateName + "!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false
            }, function(){
                $.ajax({
                    method: 'POST',
                    url: '{{ url('home/template') }}/' + templateId,
                    data: {_method: 'delete', _token : '{{csrf_token()}}'},
                    async: false,
                    success: function (result) {
                        console.log(result);
                        swal("Deleted!", "Template has been deleted.", "success");
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
                        Display Name
                    </th>
                    <th>
                        Description
                    </th>
                    <th>
                        Category
                    </th>
                    <th>
                        Management
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($templates as $template)
                    <tr>
                        <td>
                            {{ $template['number'] }}
                        </td>
                        <td>
                            {{ $template['name'] }}
                        </td>
                        <td>
                            {{ $template['display_name'] }}
                        </td>
                        <td>
                            {{ $template['description'] }}
                        </td>
                        <td>
                            {{ $template['category'] }}
                        </td>
                        <td>
                        @if($template['is_manageable'])
                            <ul class="list-inline content-table-list content-table-list-manage">
                                <li>
                                    <a href="{{ url('home/template/'.$template['id'].'/edit') }}">
                                        Edit
                                    </a>
                                </li>
                                <li>
                                    <a onclick="sweetAlertDelete('{{ $template['id'] }}', '{{ $template['name'] }}')">
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