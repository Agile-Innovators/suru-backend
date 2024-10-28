<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credenciales de Acceso</title>
</head>
<body>
    <h1>Hola, {{ $user->name }}</h1>
    <p>Te damos la bienvenida a nuestra plataforma. A continuación, encontrarás tus credenciales de acceso:</p>
    <p><strong>Usuario:</strong> {{ $user->email }}</p>
    <p><strong>Contraseña:</strong> {{ $password }}</p>
    <p>Por favor, cambia tu contraseña después de iniciar sesión por primera vez.</p>
    <p>Gracias por unirte a nosotros.</p>
    <p>Saludos,<br>El equipo de Suru</p>
</body>
</html>