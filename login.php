<?php
session_start();
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar los datos del formulario
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // 🔹 Consulta preparada para evitar inyección SQL
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // 🔹 Verificar si existe el usuario
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // 🔹 Verificar la contraseña
        if (password_verify($password, $user['password'])) {
            // Guardar datos del usuario en sesión
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['rol'] = $user['rol'];
            $_SESSION['sucursal'] = $user['sucursal'];
            $_SESSION['email'] = $user['email'];

            // Redirigir al panel principal
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "❌ Contraseña incorrecta.";
        }
    } else {
        $error = "⚠️ Usuario no encontrado con ese correo.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-card {
            width: 350px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            text-align: center;
            padding: 20px;
            position: relative;
        }
        .login-avatar {
            width: 80px;
            height: 80px;
            background: #4EC5B8;
            border-radius: 50%;
            margin: -60px auto 15px auto;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 40px;
            color: white;
        }
        .login-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #555;
        }
        .btn-login {
            background-color: #4EC5B8;
            border: none;
        }
        .btn-login:hover {
            background-color: #3aa99f;
        }
        .forgot-link {
            display: block;
            margin-top: 10px;
            font-size: 14px;
            color: #999;
            text-decoration: none;
        }
        .forgot-link:hover {
            color: #4EC5B8;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-avatar">
            <i class="bi bi-person"></i>
        </div>
        <div class="login-title">inicio de sesión</div>
        <form method="POST" action="">
            <div class="mb-3">
                <input type="text" class="form-control" name="email" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <?php if(isset($error)) echo "<div class='text-danger mb-2'>$error</div>"; ?>
            <button type="submit" class="btn btn-login text-white w-100">Iniciar sesión</button>
        </form>
        <a href="#" class="forgot-link">Forgot Password?</a>
    </div>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>
