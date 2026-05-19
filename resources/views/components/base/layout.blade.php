<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Google Font with weight variants -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400..600&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Font Awesome from unpkg -->
    <link rel="stylesheet" href="https://unpkg.com/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    @stack('styles')
    <title>{{ $title ?? 'EduConnect' }}</title>
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    {{ $slot }}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
@stack('scripts')
</body>
</html>