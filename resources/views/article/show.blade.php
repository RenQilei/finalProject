<!DOCTYPE html>
<html>
<body>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{$article->title}}</title>
</body>
<head>
    <h1>
        {{$article->title}}
    </h1>
    <h3>
        Author:{{$user->name}}
    </h3>
    <h3>
        Last Modified Time:{{$article->updated_at}}
    </h3>
    {!!$articleContents!!}
</head>
</html>