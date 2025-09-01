@php
<!DOCTYPE html>
<html style="display:table;width:100%;">
<head>
    <meta charset="utf-8">
    <title>InvoicePlane - {{ $heading }}</title>
    <style>
        html,
        html * {
            box-sizing: border-box;
        }

        body {
            font-family: sans-serif;
            background: #B94A48;
            color: #fff;
            height: 100vh;
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            padding: 2vh 2vw;
        }

        h4 {
            font-size: 30px;
            text-align: center;
            width: 100%;
        }

        p {
            font-size: 16px;
            width: 100%;
        }
    </style>
</head>
<body>
<h4>{{ $heading }}</h4>
<p>{{ $message }}</p>
</body>
</html>
