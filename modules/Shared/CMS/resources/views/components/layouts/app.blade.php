<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ $title ?? env('APP_TITLE') }}</title>
    @livewireStyles
    <link rel="stylesheet" href="{{ asset('css/cms-module-assets/form-styles.css') }}">
</head>
<body>

{{$slot}}

</body>
</html>
