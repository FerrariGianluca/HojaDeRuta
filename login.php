<?php

$info = "";
$user = "";
$password = "";
$database_file = 'database.json'; // Ruta al archivo JSON
$message = isset($_GET['register']) ? $_GET['register'] : "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["user"]);
    $password = trim($_POST["password"]);

    if (empty($username) || empty($password)) {
        $info = "Por favor, complete todos los campos.";
    } else {
        if (file_exists($database_file)) {
            $data = json_decode(file_get_contents($database_file), true);

            if (isset($data[$username]) && password_verify($password, $data[$username])) {
                // Éxito en el inicio de sesión
                header("Location: homepage.php"); // Redirige a una página de bienvenida o principal
                die();
            } else {
                $info = "Nombre de usuario o contraseña incorrectos.";
            }
        } else {
            $info = "No se puede acceder a la base de datos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1 class="title">Inicio de sesión</h1>
    <form action="login.php" method="POST" class="form">
        <p><?php echo $message; ?></p>
        <input type="text" name="user" class="form-user" value="<?php echo htmlspecialchars($user); ?>" ><br><br>
        <input type="password" name="password" class="form-pass"><br><br>
        <input type="submit" name="submit" class="form-btn" value="Ingresar">
        <button type="button" class="form-btn form-btn-back" onclick="window.location.href='index.php';">Volver</button>
        <div class="form-info"><?php echo $info; ?></div>
    </form>
</body>
</html>