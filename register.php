<?php
$info = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recolecta y sanitiza los datos del formulario
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Verifica que ambos campos estén llenos
    if (empty($username) || empty($password)) {
        $info = "Por favor, complete todos los campos.";
    } else {
        // Ruta al archivo JSON
        $file = 'database.json';

        // Carga el contenido del archivo JSON
        // Carga el contenido del archivo JSON
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
        } else {
            $data = []; // Inicializa como array vacío si el archivo no existe
        }
        // Verifica si el nombre de usuario ya existe
        if (isset($data[$username])) {
            $info = "El nombre de usuario ya está registrado.";
        } else {
            // Agrega el nuevo usuario al array de datos
            $data[$username] = password_hash($password, PASSWORD_DEFAULT);

            // Guarda los datos en el archivo JSON
            if (file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT))) {
                $message = "Registro exitoso, por favor inicia sesión.";
                // Redirige a la página de inicio de sesión o a otra página
                header("Location: login.php?register=$message");
                die();
            } else {
                $info = "Hubo un error al guardar los datos.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Registrarse</title>
</head>
<body>
    <h1>Registrarse</h1>
    <form class="form" action="register.php" method="POST">
        <input type="text" name="username" placeholder="Nombre de usuario" required><br><br>
        <input type="password" name="password" placeholder="Contraseña" required><br><br>
        <button type="submit" class="form-btn">Registrarse</button>
        <div><?php echo $info ?></div>
    </form>
</body>
</html>