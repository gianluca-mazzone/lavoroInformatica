<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'if0_39043708_mail_marketing';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Errore di connessione: " . $conn->connect_error);
}
?>
