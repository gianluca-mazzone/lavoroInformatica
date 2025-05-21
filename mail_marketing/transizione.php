<?php
require_once 'includes/db.php';

// Ottieni l'ID del cliente dalla query string (o da POST se necessario)
$id_cliente = isset($_GET['id_cliente']) ? $_GET['id_cliente'] : null;
$errore = "";
$successo = "";

if ($id_cliente) {
    // Recupera i dati del cliente (email)
    $result = mysqli_query($conn, "SELECT email FROM Cliente WHERE ID_Cliente = '$id_cliente'");

    if ($result && mysqli_num_rows($result) > 0) {
        // Se il cliente esiste, recupera l'email
        $cliente = mysqli_fetch_assoc($result);

        // Verifica se il form è stato inviato
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Ottieni l'ID della transizione dal form
            $id_transizione = $_POST['id_transizione'];
            $id_campagna = $_POST['id_campagna'];
            $nuovo_stato = $_POST['nuovo_stato'];
            $data = date('Y-m-d');

            // Verifica se l'ID della transizione è valido (es. numerico)
            if (is_numeric($id_transizione)) {
                // Query per inserire la transizione
                $query = "INSERT INTO Transizione (ID_Transizione, id_cliente, id_campagna, nuova_situazione, data) 
                          VALUES ('$id_transizione', '$id_cliente', '$id_campagna', '$nuovo_stato', '$data')";

                if (mysqli_query($conn, $query)) {
                    // Aggiorna lo stato del cliente
                    $update_query = "UPDATE Cliente 
                                     SET stato_attuale = '$nuovo_stato', data_ultimo_aggiornamento = '$data' 
                                     WHERE ID_Cliente = '$id_cliente'";

                    if (mysqli_query($conn, $update_query)) {
                        $successo = "Transizione registrata con successo!";
                    } else {
                        $errore = "Errore nell'aggiornare lo stato del cliente.";
                    }
                } else {
                    $errore = "Errore durante la registrazione della transizione.";
                }
            } else {
                $errore = "ID Transizione non valido.";
            }
        }

        // Recupera le campagne disponibili
        $campagne = mysqli_query($conn, "SELECT ID_Campagna, nome FROM Campagna ORDER BY data_invio DESC");
    } else {
        $errore = "Cliente non trovato.";
    }
} else {
    $errore = "ID Cliente non valido.";
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Registra Transizione</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>📤 Registra Transizione per il Cliente</h1>

        <?php if ($errore): ?>
            <p class="error"><?= $errore ?></p>
        <?php endif; ?>

        <?php if ($cliente): ?>
            <p><strong>Email cliente:</strong> <?= htmlspecialchars($cliente['email']) ?></p>

            <?php if ($successo): ?>
                <p class="success"><?= $successo ?></p>
            <?php endif; ?>

            <form method="POST">
                <label for="id_transizione">ID Transizione:</label><br>
                <input type="text" name="id_transizione" required><br><br>

                <label for="id_campagna">Campagna:</label><br>
                <select name="id_campagna" required>
                    <option value="">-- Seleziona --</option>
                    <?php while ($row = mysqli_fetch_assoc($campagne)): ?>
                        <option value="<?= $row['ID_Campagna'] ?>"><?= htmlspecialchars($row['nome']) ?></option>
                    <?php endwhile; ?>
                </select><br><br>

                <label for="nuovo_stato">Nuovo stato:</label><br>
                <select name="nuovo_stato" required>
                    <option value="pigro">Pigro</option>
                    <option value="curioso">Curioso</option>
                    <option value="interessato">Interessato</option>
                    <option value="entusiasta">Entusiasta</option>
                </select><br><br>

                <button type="submit">✅ Salva Transizione</button>
            </form>

        <?php else: ?>
            <p>Cliente non trovato.</p>
        <?php endif; ?>
    </div>
</body>
</html>
