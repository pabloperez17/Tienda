<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <?php require "../util/conexion.php" ?>
    <link rel="stylesheet" href="./css/inicio.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
    <?php
    // Procesamiento del formulario cuando se envía
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $usuario = $_POST["usuario"];
        $contrasena = $_POST["contrasena"];

        // Consulta SQL para obtener el usuario de la base de datos
        $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
        $resultado = $conexion->query($sql);

        // Verifica si el usuario existe
        if ($resultado->num_rows == 0) {
    ?>
        <!-- Mensaje de error si el usuario no existe -->
        <div class="alert alert-danger" role="alert">
            EL USUARIO NO EXISTE
        </div>
    <?php
        } else {
            // Recupera la contraseña cifrada y el rol del usuario
            while ($fila = $resultado->fetch_assoc()) {
                $contrasena_cifrada = $fila["contrasena"];
                $rol = $fila["rol"];
            }

            // Verifica la contraseña
            $acceso_valido = password_verify($contrasena, $contrasena_cifrada);

            if ($acceso_valido) {
                // Si la contraseña es válida, se inicia la sesión
                echo "NOS HEMOS LOGUEADO CON ÉXITO";
                session_start();
                $_SESSION["usuario"] = $usuario;
                $_SESSION["rol"] = $rol;
                header('location: principal.php');
            } else {
                // Mensaje de error si la contraseña es incorrecta
                echo "LA CONTRASEÑA ESTÁ MAL";
            }
        }
    }
    ?>

    <!-- Formulario de inicio de sesión -->
    <div class="container">
        <h1>Iniciar sesión</h1>
        <form action="" method="post">
            <div class="mb-3">
                <label class="form-label">Usuario: </label>
                <input class="form-control" type="text" name="usuario">
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña: </label>
                <input class="form-control" type="password" name="contrasena">
            </div>
            <input class="btn btn-primary" type="submit" value="Iniciar sesión">
            <div class="mb-3">
                <p class="mt-3">¿No tienes cuenta? <a href="./registro.php">Registrarse</a></p>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>
