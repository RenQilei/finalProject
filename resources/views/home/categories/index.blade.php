@extends('frameworks.home-basic')

@section('head-extension')

@stop

@section('script-extension')
    <script>
        function sweetAlertDelete(categoryId, categoryName) {
            swal({
                title: "Are you sure?",
                text: "You will permanently delete category " + categoryName + "!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false
            }, function(){
                $.ajax({
                    method: 'POST',
                    url: '{{ url('home/category') }}/' + categoryId,
                    data: {_method: 'delete', _token : '{{csrf_token()}}'},
                    async: false,
                    success: function (result) {
                        console.log(result);
                        swal("Deleted!", "Category has been deleted.", "success");
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
    <div id="category-index-list" class="content-block-wrapper">
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
                        Department
                    </th>
                    <th>
                        Parent Category
                    </th>
                    <th>
                        Management
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($categories as $category)
                    <tr>
                        <td>
                            {{ $category['number'] }}
                        </td>
                        <td>
                            {{ $category['name'] }}
                        </td>
                        <td>
                            {{ $category['display_name'] }}
                        </td>
                        <td>
                            {{ $category['description'] }}
                        </td>
                        <td>
                        @if($category['manager'] != 0)
                            {{ $category['manager'] }}
                        @else
                            TBD
                        @endif
                        </td>
                        <td>
                            {{ $category['department'] }}
                        </td>
                        <td>
                            {{ $category['parent_category'] }}
                        </td>
                        <td>
                        @if($category['is_manageable'])
                            <ul class="list-inline content-table-list content-table-list-manage">
                                <li>
                                    <a href="{{ url('home/category/'.$category['id'].'/edit') }}">
                                        Edit
                                    </a>
                                </li>
                                <li>
                                    <a onclick="sweetAlertDelete('{{ $category['id'] }}', '{{ $category['name'] }}')">
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