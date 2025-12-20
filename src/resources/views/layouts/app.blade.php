<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TicketCRM')</title>
    <link rel="icon" type="image/x-icon" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAFDSURBVDhPpZMxTgNBEEXvnkASVCAhJUgBUoAUhBJyAkqQAjlACpACpACJQwUpQAqQAKQAKUDGYsWOd22t9crbOYmQaFczb96b+W8MMEryiZmLJOsE3wleElxOMgK8Jbkq6arT7wHuMt8BrpLsM98BbjPfA24yfwS8ZH4NeM78CfCS+TXgOfNXwHPmr4DXzG8Ab5k/A94zfwe8Z/4B+Mj8E/CZ+SfgK/NvwHfmP4DvzH8BP5n/AX5Lui7pOsltkvOSLpL8TPKR5CPJd5LfAE8jcRjgJckqwXeClwSXk4wA/wGOuAGvWUfpEAAAAABJRU5ErkJggg==">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        @yield('content')
    </div>
    @vite(['resources/js/app.js'])
    @yield('scripts')
</body>
</html>
