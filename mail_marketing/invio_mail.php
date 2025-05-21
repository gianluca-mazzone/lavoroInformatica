<?php
// Include il file di connessione al database
include 'includes/db.php';

// Verifica se il form è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ottieni i dati dal form
    $nome = $_POST['nome'];
    $descrizione = $_POST['descrizione'];
    $destinatari = $_POST['destinatari'];

    // Impostazioni base per il messaggio
    $subject = "Campagna: $nome";
    $message = "<h2>$nome</h2><p>$descrizione</p>";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n"; // Corpo HTML
    $headers .= "From: mich@gmail.com" . "\r\n";

    // Decidi quali clienti riceveranno l'email
    if ($destinatari == 'tutti') {
        // Recupera tutti i clienti
        $sql = "SELE CT email FROM Cliente";
    } else {
        // Filtra i clienti per stato
        $sql = "SELECT email FROM Cliente WHERE stato_attuale = '$destinatari'";
    }

    // Esegui la query
    $result = $conn->query($sql);

    // Verifica se ci sono risultati
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $to = $row['email'];

            // Invia l'email
            if (mail($to, $subject, $message, $headers)) {
                echo "Campagna inviata a $to<br>";
            } else {
                echo "Errore nell'invio a $to<br>";
            }
        }
    } else {
        echo "Nessun destinatario trovato.";
    }
}
?>
