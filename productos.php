<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="estilo.css" type="text/css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1>Insertar producto</h1>
        <div>
            <form action="" method="post">
                <div class="mb-3">
                    <label class="form-label">Nombre producto: </label>
                    <input class="form-control" type="text" name="nombreProducto">
                </div>
                <div class="mb-3">
                    <label class="form-label">Precio: </label>
                    <input class="form-control" type="number" name="Precio">
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción: </label>
                    <input class="form-control" type="text" name="descripcion">
                </div>
                <div class="mb-3">
                    <label class="form-label">Cantidad: </label>
                    <input class="form-control" type="int" name="cantidad">
                </div>
                <button class="btn btn-primary" type="submit">Enviar</button>
            </form>
        </div>
    </div>
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

        #   Validación de nombreProducto
        if (strlen($temp_nombreProducto) == 0) {
            $err_nombreProducto = "Campo obligatorio";
        } else {
            $patron = "/^[a-zA-Z0-9]{1,40}$/";
            if(!preg_match($patron, $temp_nombreProducto)) {
                $err_nombreProducto = "El nombre debe tener entre 1 y 40 caracteres y contener solamente letras o números";
            } else {
                $nombreProducto = $temp_nombreProducto;
                echo $nombreProducto;
            }
        }

        #   Validación de precio
        if (strlen($temp_precio) == 0) {
            $err_precio = "Campo obligatorio";
        } else {
            $patron1 = "/^[0-9]{1,99999.99}$/";
            if(!preg_match($patron1, $temp_precio)) {
                $err_precio = "El precio debe tener entre 1 y 99999.99 caracteres y contener solamente números";
            } else {
                $precio = $temp_precio;
                echo $precio;
            }
        }
    }
    ?>
</body>

</html>