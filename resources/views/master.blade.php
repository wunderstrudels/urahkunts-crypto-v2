<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title> <?= env("APP_NAME") ?> </title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://use.fontawesome.com/93cf33b3bf.js"></script>
    </head>
    <body>
        <div id="app">
            <Master></Master>
        </div>
        <div id="background"></div>
        
        <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
