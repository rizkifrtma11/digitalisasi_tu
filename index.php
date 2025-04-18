<?php
include 'auth.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (login($username, $password)) {
        $role = $_SESSION['role'];
        if ($role == 'admin' || $role == 'dosen' || $role == 'pegawai') {
            header('Location: admin_dashboard.php');
        } elseif ($role == 'mahasiswa') {
            header('Location: user_dashboard.php');
        } else {
            $error = "Role tidak dikenal.";
        }
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem Akademik</title>
    <style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        background: #f2f4f7;
        margin: 0;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
    }

    .login-container {
        background: #fff;
        padding: 40px 30px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 400px;
        box-sizing: border-box;
    }

    h2 {
        text-align: center;
        margin-bottom: 30px;
        color: #333;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    label {
        margin-bottom: 6px;
        color: #444;
        font-weight: 500;
    }

    input[type="text"],
    input[type="password"] {
        padding: 12px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 16px;
        width: 100%;
    }

    button[type="submit"] {
        width: 100%;
        padding: 12px;
        background-color: #1e90ff;
        border: none;
        border-radius: 6px;
        color: white;
        font-size: 16px;
        cursor: pointer;
    }

    button[type="submit"]:hover {
        background-color: #0d74d1;
    }

    .error-message {
        color: red;
        text-align: center;
        margin-bottom: 15px;
    }

    .register-link {
        text-align: center;
        margin-top: 15px;
    }

    .register-link a {
        color: #1e90ff;
        text-decoration: none;
    }

    .register-link a:hover {
        text-decoration: underline;
    }

    @media (max-width: 480px) {
        .login-container {
            padding: 30px 20px;
            border-radius: 10px;
            margin: 10px;
        }

        h2 {
            font-size: 20px;
        }

        input,
        button {
            font-size: 14px;
        }
    }
</style>


</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <button type="submit" name="login">Login</button>
        </form>

        <?php if (isset($error)) { echo "<p class='error-message'>$error</p>"; } ?>

        <div class="register-link">
            <p>Belum punya akun? Daftar <a href="register.php">di sini</a></p>
        </div>
    </div>
</body>

</html>
