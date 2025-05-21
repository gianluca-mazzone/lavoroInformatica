<?php
require_once 'includes/db.php';

$id_cliente = isset($_GET['id_cliente']) ? intval($_GET['id_cliente']) : null;
$errore = "";
$successo = "";
$cliente = null;

if ($id_cliente) {
    $stmt = $conn->prepare("SELECT nome, cognome, email, stato_classificazione FROM Cliente WHERE ID_Cliente = ?");
    $stmt->bind_param("i", $id_cliente);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $cliente = $result->fetch_assoc();
        $stato_attuale = $cliente['stato_classificazione'];
        $stati = ['dormiente', 'pigro', 'curioso', 'interessato', 'entusiasta'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_campagna = intval($_POST['id_campagna']);
            $nuovo_stato = $_POST['nuovo_stato'];
            $data = date('Y-m-d');

            $pos_attuale = array_search($stato_attuale, $stati);
            $pos_nuovo = array_search($nuovo_stato, $stati);
            if ($pos_nuovo === false || $pos_nuovo <= $pos_attuale) {
                $errore = "Non puoi retrocedere di stato!";
            } else {
                // Registra transizione
                $query = "INSERT INTO Transizioni (id_cliente, stato_precedente, stato_successivo, data_transizione, id_campagna) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("isssi", $id_cliente, $stato_attuale, $nuovo_stato, $data, $id_campagna);
                if ($stmt->execute()) {
                    // Aggiorna stato e data
                    $update_query = "UPDATE Cliente SET stato_classificazione = ?, data_ultima_classificazione = ? WHERE ID_Cliente = ?";
                    $stmt2 = $conn->prepare($update_query);
                    $stmt2->bind_param("ssi", $nuovo_stato, $data, $id_cliente);
                    $stmt2->execute();
                    $successo = "Transizione registrata con successo!";
                } else {
                    $errore = "Errore durante la registrazione della transizione.";
                }
            }
        }
        $campagne = mysqli_query($conn, "SELECT ID_Campagna, nome_campagna FROM Campagna ORDER BY data_invio DESC");
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
            <p class="error"><?= htmlspecialchars($errore) ?></p>
        <?php endif; ?>
        <?php if ($cliente): ?>
            <p><strong>Nome:</strong> <?= htmlspecialchars($cliente['nome']) ?> <?= htmlspecialchars($cliente['cognome']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($cliente['email']) ?></p>
            <p><strong>Stato attuale:</strong> <?= htmlspecialchars($cliente['stato_classificazione']) ?></p>
            <?php if ($successo): ?>
                <p class="success"><?= htmlspecialchars($successo) ?></p>
            <?php endif; ?>
            <form method="POST" class="form-box">
                <label for="id_campagna">Campagna:</label>
                <select name="id_campagna" required>
                    <option value="">-- Seleziona --</option>
                    <?php while ($row = mysqli_fetch_assoc($campagne)): ?>
                        <option value="<?= $row['ID_Campagna'] ?>"><?= htmlspecialchars($row['nome_campagna']) ?></option>
                    <?php endwhile; ?>
                </select><br>
                <label for="nuovo_stato">Nuovo stato:</label>
                <select name="nuovo_stato" required>
                    <?php
                    $stati_avanzamento = array_slice($stati, array_search($cliente['stato_classificazione'], $stati) + 1);
                    foreach ($stati_avanzamento as $stato) {
                        echo "<option value=\"$stato\">" . ucfirst($stato) . "</option>";
                    }
                    ?>
                </select><br>
                <button type="submit" class="btn">✅ Salva Transizione</button>
            </form>
            <a href="clienti.php" class="btn">⬅️ Torna ai clienti</a>
        <?php else: ?>
            <p>Cliente non trovato.</p>
        <?php endif; ?>
    </div>
</body>
</html>
