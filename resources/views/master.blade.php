<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title> <?= env("APP_NAME") ?> </title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="https://codemirror.net/lib/codemirror.css">

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://use.fontawesome.com/93cf33b3bf.js"></script>
        <script src="https://hammerjs.github.io/dist/hammer.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://unpkg.com/chartjs-plugin-annotation@1.0.2/dist/chartjs-plugin-annotation.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@1.0.1/dist/chartjs-plugin-zoom.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.61.1/codemirror.min.js"></script>
    </head>
    <body>
        <div id="app">
            <Master></Master>
        </div>
        <div id="background"></div>
        
        <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
