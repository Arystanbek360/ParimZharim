<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Парим Жарим</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @livewireStyles
    <link rel="stylesheet" href="{{ asset('css/air-datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

@if (session()->has('success'))
    <div class="alert alert-success" role="alert">
        {{ session()->get('success') }}
    </div>
@endif

@if (session()->has('error'))
    <div class="alert alert-danger" role="alert">
        {{ session()->get('error') }}
    </div>
@endif

{{ $slot }}

@livewireScripts

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="{{ asset('/js/slick.min.js') }}"></script>
<script src="{{ asset('/js/app.js') }}" defer></script>
<script src="{{ asset('/js/air-datepicker.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/air-datepicker/dist/js/datepicker.min.js"></script>
</body>
</html>
