<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <title>Document</title>

</head>
<body>
    <div class='container'>
        <div class="content">
            <div class='img-container'>
                <img src="{{asset('images/logo.png')}}"/>
            </div>
            <h1>{{$headerText}}</h1>
        </div>
        <p>Hi {{$user}},</p>
        <p>{{$text}}</p>
        <a href="{{$url}}">{{$buttonText}}</a>
    </div>
</body>
</html>