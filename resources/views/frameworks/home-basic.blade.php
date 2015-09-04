<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Welcome</title>

    @include('partials.common-assets-local')

    <!-- Customised -->
    <link href="{{ URL::asset('css/admin.css') }}" rel="stylesheet" type="text/css">

    @yield('head-extension')
</head>
<body>

<header>
    <div id="header-brand">
        BP ADMIN
    </div>
    <div id="header-user" class="dropdown"><!-- Dropdown 需要根据material design进行修改 -->
        <a id="dLabel" type="button" class="btn btn-default" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            {{ Auth::user()->name }}
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu" aria-labelledby="dLabel">
            <li>
                <a href="{{ url('auth/logout') }}">
                    Logout
                </a>
            </li>
        </ul>
    </div>
</header>

<section id="main">
    @include('partials.home-sidebar')

    <section id="content">
        <div id="content-container">
            <div id="content-header">
                <div id="content-header-title">
                    @if(isset($pageTitle))
                        {{ $pageTitle }}
                    @else
                        Page Title (TBD)
                    @endif
                </div>
            </div>

            @yield('content')

        </div>
    </section>
</section>

<footer>

</footer>

@include('partials.common-scripts-local')

<script type="text/javascript">
    function loadTinyMCE() {
        tinymce.remove();
        tinymce.init({
            selector: "textarea",
            theme: "modern",
            plugins: [
                "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                "save table contextmenu directionality emoticons template paste textcolor"
            ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor emoticons",
            style_formats: [
                {title: 'Bold text', inline: 'b'},
                {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                {title: 'Example 1', inline: 'span', classes: 'example1'},
                {title: 'Example 2', inline: 'span', classes: 'example2'},
                {title: 'Table styles'},
                {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
            ]
        });
    }
</script>

@yield('script-extension')
</body>
</html>