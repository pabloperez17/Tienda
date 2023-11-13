<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <?php require "BaseDatos/conexion.php" ?>
</head>

<body>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $temp_usuario = $_POST["usuario"];
        $temp_contrasena = $_POST["contrasena"];
        $temp_fecha_nacimiento = $_POST["fecha_nacimiento"];

        #   Validación de usuario
        if (strlen($temp_usuario) == 0) {
            $err_usuario = "Campo obligatorio";
        } else {
            $patron = "/^[a-zA-Z_]{4,12}$/";
            if (!preg_match($patron, $temp_usuario)) {
                $err_usuario = "El usuario debe tener entre 4 y 12 caracteres y contener solamente letras o barra baja";
            } else {
                $usuario = $temp_usuario;
            }
        }

        #   Validación de contrasena
        if (strlen($temp_contrasena) == 0) {
            $err_contrasena = "Campo obligatorio";
        } else {
            $patron = "/^[a-zA-Z0-9]{0,255}$/";
            if (!preg_match($patron, $temp_contrasena)) {
                $err_contrasena = "El contraseña debe tener máximo 255 caracteres y contener solamente letras o números";
            } else {
                $contrasena_cifrada = password_hash($temp_contrasena, PASSWORD_DEFAULT);
            }
        }

        #   Validación fecha de nacimiento
        if (strlen($temp_fecha_nacimiento) == 0) {
            $err_fecha_nacimiento = "La fecha de nacimiento es obligatoria";
        } else {
            $fecha_actual = date("Y-m-d");
            list($anyo_actual, $mes_actual, $dia_actual) = explode('-', $fecha_actual);
            list($anyo, $mes, $dia) = explode('-', $temp_fecha_nacimiento);
            if (($anyo_actual - $anyo > 12) || ($anyo_actual - $anyo < 120)) {
                $fecha_nacimiento = $temp_fecha_nacimiento;
            } else if (($anyo_actual - $anyo < 12)||($anyo_actual - $anyo > 120)) {
                $err_fecha_nacimiento = "No puedes ser menor de 12 años o mayor de 120 años";
            } else {
                if ($mes_actual - $mes > 0) {
                    $fecha_nacimiento = $temp_fecha_nacimiento;
                } else if ($mes_actual - $mes < 0) {
                    $err_fecha_nacimiento = "No puedes ser menor de 12 años o mayor de 120 años";
                } else {
                    if ($dia_actual - $dia >= 0) {
                        $fecha_nacimiento = $temp_fecha_nacimiento;
                    } else {
                        $err_fecha_nacimiento = "No puedes ser menor de 12 años o mayor de 120 años";
                    }
                }
            }
        }
    }
    ?>
    <div class="container">
        <h1>Registrarse</h1>
        <form action="" method="post">
            <div class="mb-3">
                <label class="form-label">Usuario: </label>
                <input class="form-control" type="text" name="usuario">
                <?php if (isset ($err_usuario)) echo '<label class=text-danger>'.$err_usuario. '</label>' ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña: </label>
                <input class="form-control" type="password" name="contrasena">
                <?php if (isset ($err_contrasena)) echo '<label class=text-danger>'.$err_contrasena. '</label>' ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Fecha de nacimiento: </label>
                <input class="form-control" type="date" name="fecha_nacimiento">
                <?php if (isset ($err_fecha_nacimiento)) echo '<label class=text-danger>'.$err_fecha_nacimiento. '</label>' ?>
            </div>
            <input class="btn btn-primary" type="submit" value="Registrarse">
        </form>
    </div>
    <?php
         if(isset($usuario) && isset($contrasena_cifrada) && isset($fecha_nacimiento)) {
            $sql = "INSERT INTO usuarios (usuario, contrasena, fechaNacimiento) VALUES ('$usuario', '$contrasena_cifrada', '$fecha_nacimiento')";
            $sql2 = "INSERT INTO cestas (usuario, precioTotal) VALUES ('$usuario', '0')";
   
            $conexion->query($sql);
            $conexion->query($sql2);

            echo "<div class='alert alert-success' role='alert'>";
            echo  $usuario . " registrado correctamente</centre>";
            echo "</div>";
        }
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>