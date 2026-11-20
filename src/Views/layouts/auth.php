<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="theme-color" content="#00324D">
    <title><?= htmlspecialchars($title ?? 'GymSENA - Iniciar Sesión') ?></title>
    
    <link rel="icon" type="image/svg+xml" href="/icons/logo-sena-oficial.svg">
    <link rel="stylesheet" href="/css/style.css?v=3">
</head>
<body class="split-auth-body">
    <?= $content ?>
</body>
</html>
