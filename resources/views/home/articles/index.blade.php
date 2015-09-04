@extends('frameworks.home-basic')

@section('head-extension')

@stop

@section('script-extension')

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
                        Title
                    </th>
                    <th>
                        Author
                    </th>
                    <th>
                        Category
                    </th>
                    <th>
                        Last Modified
                    </th>
                    <th>
                        Management
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($articles as $article)
                    <tr>
                        <td>
                            {{ $article['id'] }}
                        </td>
                        <td>
                            {{ $article['title'] }}
                        </td>
                        <td>
                            {{ $article['author'] }}
                        </td>
                        <td>
                            {{ $article['category'] }}
                        </td>
                        <td>
                            {{ $article['updated_at'] }}
                        </td>
                        <td>
                        @if($article['is_manageable'])
                            <ul class="list-inline content-table-list content-table-list-manage">
                                <li>
                                    <a href="{{ url('home/article/'.$article['id']) }}">
                                        Preview
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('home/article/'.$article['id'].'/edit') }}">
                                        Edit
                                    </a>
                                </li>
                                <li>
                                    <a onclick="sweetAlertDelete('{{ $article['id'] }}', '{{ $article['title'] }}')">
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