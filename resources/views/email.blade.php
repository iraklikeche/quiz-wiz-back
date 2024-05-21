<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <title>Document</title>
    <style>
        *{
            margin:0;
            padding:0;
            box-sizing: border-box
        }
        .container {
            height:fit-content;
            width:fit-content;
            margin:auto;
        }
        .content {
          margin:0 auto
        }
        .img-container {
            width:fit-content
            text-align: center;
            margin-bottom: 20px;
        }
        .img-container img {
            max-width: 100px;
        }
        h1 {
            font-size: 24px;
            color: #333333;
            margin: 0 0 10px;
        }
        p {
            font-size: 16px;
            color: #555555;
            margin: 10px 0;
        }
        a {
            background-color: #4b69fd;
            color: #ffffff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class="content">
            <div class='img-container'>
                <img src="{{asset('images/logo.png')}}"/>
            </div>
            <h1>{{$headerText}}</h1> </br >
            <h1>{{$subHeader}}</h1>
        </div>
        <p>Hi {{$user}},</p>
        <p>{{$text}}</p>
        <a href="{{$url}}">{{$buttonText}}</a>
    </div>
</body>
</html>