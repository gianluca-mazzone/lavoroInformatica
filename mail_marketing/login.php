<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    if ($user === 'admin' && $pass === 'admin') {
        $_SESSION['logged'] = true;
        header('Location: index.html');
        exit;
    } else {
        $errore = "Credenziali errate";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Login</h1>
    <?php if (!empty($errore)) echo "<p class='error'>$errore</p>"; ?>
    <form method="post" class="form-box">
        <label>Utente</label>
        <input type="text" name="user" required>
        <label>Password</label>
        <input type="password" name="pass" required>
        <button type="submit" class="btn">Login</button>
    </form>
</div>
</body>
</html>
