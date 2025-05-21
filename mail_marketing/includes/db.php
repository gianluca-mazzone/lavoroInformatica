<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'mail_marketing';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Errore di connessione: " . $conn->connect_error);
}
?>
