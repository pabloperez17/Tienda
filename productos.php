<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="estilo.css" type="text/css" rel="stylesheet">
    <?php require "./BaseDatos/conexion.php" ?>
</head>

<body>

    <?php
    function depurar($entrada)
    {
        $salida = htmlspecialchars($entrada);
        $salida = trim($salida);
        return $salida;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $temp_nombreProducto = depurar($_POST["nombreProducto"]);
        $temp_precio = depurar($_POST["precio"]);
        $temp_descripcion = depurar($_POST["descripcion"]);
        $temp_cantidad = depurar($_POST["cantidad"]);

        //  $_FILES["nombreCampo"]["queQueremosCoger"] -> TYPE, NAME, SIZE, TMP_NAME
        $nombre_imagen = $_FILES["imagen"]["name"];
        $tipo_imagen = $_FILES["imagen"]["type"];
        $tamano_imagen = $_FILES["imagen"]["size"];
        $ruta_temporal = $_FILES["imagen"]["tmp_name"];
        //echo $nombre_imagen . " " . $tipo_imagen . " " . $tamano_imagen . " " . $ruta_temporal;
        $ruta_final = "imagenes/" . $nombre_imagen;
        move_uploaded_file($ruta_temporal, $ruta_final);

        #   Validación de nombreProducto
        if (strlen($temp_nombreProducto) == 0) {
            $err_nombreProducto = "Campo obligatorio";
        } else {
            $patron = "/^[a-zA-Z0-9]{1,40}$/";
            if (!preg_match($patron, $temp_nombreProducto)) {
                $err_nombreProducto = "El nombre debe tener entre 1 y 40 caracteres y contener solamente letras o números";
            } else {
                $nombreProducto = $temp_nombreProducto;
            }
        }

        #   Validación de precio
        if (strlen($temp_precio) == 0) {
            $err_precio = "El precio es obligatorio";
        } elseif (!is_numeric($temp_precio)) {
            $err_precio = "El precio debe ser un número";
        } elseif ($temp_precio < 0) {
            $err_precio = "El precio no puede ser negativo";
        } elseif ($temp_precio > 99999.99) {
            $err_precio = "El precio no puede ser mayor de 99999.99";
        } else {
            $precio = $temp_precio;
        }

        #   Validación de descripcion
        if (strlen($temp_descripcion) == 0) {
            $err_descripcion = "Campo obligatorio";
        } else {
            $patron2 = "/^[a-zA-Z0-9 ]{1,255}$/";
            if (!preg_match($patron2, $temp_descripcion)) {
                $err_descripcion = "La descripción debe tener entre 1 y 255 caracteres y contener solamente letras o números";
            } else {
                $descripcion = $temp_descripcion;
            }
        }

        #   Validación de cantidad
        if (strlen($temp_cantidad) == 0) {
            $err_cantidad = "La cantidad es obligatoria";
        } elseif (filter_var($temp_cantidad, FILTER_VALIDATE_INT) === false) { 
            $err_cantidad = "La cantidad debe ser un número entero";
        } elseif ($temp_cantidad < 0) {
            $err_cantidad = "La cantidad no puede ser negativa";
        } elseif ($temp_cantidad > 99999) {
            $err_cantidad = "La cantidad no puede ser mayor de 99999";
        } else {
            $cantidad = $temp_cantidad;
        }

        #   Validación de imagen
        if (strlen($nombre_imagen) > 1) {
            if ($_FILES["imagen"]["error"] != 0) {
                $err_imagen= "Error al subir la imagen";
            } else {
                $permitidos = ["image/jpeg", "image/png", "image/gif", "image/webp"];
                if (!in_array($_FILES["imagen"]["type"], $permitidos)) {
                    $err_imagen= "Error al subir la imagen";
                }else{
                    $ruta_final = "imagenes/" . $nombre_imagen;
            move_uploaded_file($ruta_temporal, $ruta_final);
                }
            }
        } else {
            $err_imagen = "La imagen es obligatoria";
        }
    }
    ?>
    <div class="container">
        <h1>Insertar producto</h1>
        <div>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Nombre producto: </label>
                    <input class="form-control" type="text" name="nombreProducto">
                    <?php if (isset ($err_nombreProducto)) echo '<label class=text-danger>'.$err_nombreProducto. '</label>' ?>
                </div>
                <div class="mb-3">
                    <label class="form-label">Precio: </label>
                    <input class="form-control" type="text" name="precio">
                    <?php if (isset ($err_precio)) echo '<label class=text-danger>'.$err_precio. '</label>' ?>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción: </label>
                    <input class="form-control" type="text" name="descripcion">
                    <?php if (isset ($err_descripcion)) echo '<label class=text-danger>'.$err_descripcion. '</label>' ?>
                </div>
                <div class="mb-3">
                    <label class="form-label">Cantidad: </label>
                    <input class="form-control" type="text" name="cantidad">
                    <?php if (isset ($err_cantidad)) echo '<label class=text-danger>'.$err_cantidad. '</label>' ?>
                </div>
                <div class="mb-3">
                    <label class="form-label">Imagen: </label>
                    <input class="form-control" type="file" name="imagen">
                </div>
                <button class="btn btn-primary" type="submit">Enviar</button>
                <?php
                    if (isset($nombreProducto) && isset($precio) && isset($descripcion) && isset($cantidad)) {
                        $sql = "INSERT INTO productos (nombreProducto, precio, descripcion, cantidad, imagen)
                        VALUES ('$nombreProducto',
                        '$precio',
                        '$descripcion',
                        '$cantidad',
                        '$ruta_final')";
                        $conexion->query($sql);
                        echo "<div class='container alert alert-success'><h4>Producto insertado con éxito<h4><div>";
                    }
                ?>
            </form>
        </div>
    </div>
</body>

</html>