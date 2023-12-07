//php artisan make:view emails.activate_user

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Correo y Cambio de Contraseña</title>
</head>
<body style="text-align: center; font-family: Arial, sans-serif;">

    <h1 style="color: #000; font-size: 24px; font-weight: bold;">¡Gracias por verificar tu correo y cambiar tu contraseña!</h1>
    
    <p style="color: #333; font-size: 16px;">Haz clic en el siguiente enlace para activar tu cuenta:</p>
    
    <a href="{{ $signedUrl }}" style="color: #007BFF; text-decoration: none; font-size: 18px;">Activar cuenta</a>

    <p style="color: #333; font-size: 16px;">Equipo PewSecure</p>

</body>
</html>
