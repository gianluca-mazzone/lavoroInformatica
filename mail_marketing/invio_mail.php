<?php
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $descrizione = filter_input(INPUT_POST, 'descrizione', FILTER_SANITIZE_STRING);
    $destinatari = filter_input(INPUT_POST, 'destinatari', FILTER_SANITIZE_STRING);
    $responsabile = 1; // ID responsabile (esempio: 1)
    $subject = "Campagna: $nome";
    $tracking_domain = "https://if0_39043708.infinityfreeapp.com";
    $template = file_get_contents('templates/mail_template.html');
    $headers = "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\nFrom: info@tuosito.it\r\n";

    if ($destinatari == 'tutti') {
        $sql = "SELECT ID_Cliente, email, nome, stato_classificazione FROM Cliente";
        $stmt = $conn->prepare($sql);
    } else {
        $sql = "SELECT ID_Cliente, email, nome, stato_classificazione FROM Cliente WHERE stato_classificazione = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $destinatari);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $clienti_raggiunti = 0;
    $ids_clienti = [];
    $data_invio = date('Y-m-d');

    while ($row = $result->fetch_assoc()) {
        $to = $row['email'];
        $personalized = str_replace(
            ['{{nome}}', '{{descrizione}}', '{{nome_cliente}}', '{{pixel}}'],
            [
                $nome,
                $descrizione,
                $row['nome'],
                '<img src="' . $tracking_domain . '/tracking_pixel.php?campagna=' . urlencode($nome) . '&email=' . urlencode($row['email']) . '" width="1" height="1" style="display:none;">'
            ],
            $template
        );
        if (mail($to, $subject, $personalized, $headers)) {
            $clienti_raggiunti++;
            $ids_clienti[] = $row['ID_Cliente'];
            // Log azione apertura mail (verrà aggiornata dal pixel)
        }
    }

    // Inserisci la campagna nel DB
    $sql_campagna = "INSERT INTO Campagna (nome_campagna, descrizione, data_invio, responsabile, numero_destinatari) VALUES (?, ?, ?, ?, ?)";
    $stmt_campagna = $conn->prepare($sql_campagna);
    $stmt_campagna->bind_param("sssii", $nome, $descrizione, $data_invio, $responsabile, $clienti_raggiunti);
    $stmt_campagna->execute();

    echo "<div class='container'><h2>Campagna inviata a $clienti_raggiunti clienti.</h2><a href='index.html' class='btn'>Torna alla Dashboard</a></div>";
}
?>
