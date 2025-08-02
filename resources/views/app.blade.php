<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Application Vue</title>
    @vite(['resources/js/main.js'])
</head>

<body class="bg-gray-100">
    <div id="app"></div>
</body>

</html>
