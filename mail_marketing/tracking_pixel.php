<?php
include 'includes/db.php';

if (isset($_GET['campagna']) && isset($_GET['email'])) {
    $campagna = $_GET['campagna'];
    $email = $_GET['email'];
    $data_azione = date('Y-m-d');

    // Recupera ID cliente e campagna
    $stmt = $conn->prepare("SELECT ID_Cliente FROM Cliente WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $id_cliente = null;
    if ($row = $res->fetch_assoc()) {
        $id_cliente = $row['ID_Cliente'];
    }

    $stmt = $conn->prepare("SELECT ID_Campagna FROM Campagna WHERE nome_campagna = ?");
    $stmt->bind_param("s", $campagna);
    $stmt->execute();
    $res = $stmt->get_result();
    $id_campagna = null;
    if ($row = $res->fetch_assoc()) {
        $id_campagna = $row['ID_Campagna'];
    }

    if ($id_cliente && $id_campagna) {
        // Registra apertura email in Azioni solo se non già registrata per oggi
        $check = $conn->prepare("SELECT id FROM Azioni WHERE id_cliente=? AND id_campagna=? AND apertura_email=1 AND data_azione=?");
        $check->bind_param("iis", $id_cliente, $id_campagna, $data_azione);
        $check->execute();
        $check->store_result();
        if ($check->num_rows == 0) {
            $insert = $conn->prepare("INSERT INTO Azioni (id_cliente, id_campagna, apertura_email, data_azione) VALUES (?, ?, 1, ?)");
            $insert->bind_param("iis", $id_cliente, $id_campagna, $data_azione);
            $insert->execute();

            // Aggiorna stato cliente se necessario (dormiente -> pigro)
            $stati = ['dormiente', 'pigro', 'curioso', 'interessato', 'entusiasta'];
            $stmt = $conn->prepare("SELECT stato_classificazione FROM Cliente WHERE ID_Cliente=?");
            $stmt->bind_param("i", $id_cliente);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                $attuale = $row['stato_classificazione'];
                $pos = array_search($attuale, $stati);
                if ($pos !== false && $pos < count($stati) - 1 && $attuale === 'dormiente') {
                    $nuovo_stato = $stati[$pos + 1];
                    // Registra transizione
                    $trans = $conn->prepare("INSERT INTO Transizioni (id_cliente, stato_precedente, stato_successivo, data_transizione, id_campagna) VALUES (?, ?, ?, ?, ?)");
                    $trans->bind_param("isssi", $id_cliente, $attuale, $nuovo_stato, $data_azione, $id_campagna);
                    $trans->execute();
                    // Aggiorna cliente
                    $upd = $conn->prepare("UPDATE Cliente SET stato_classificazione=?, data_ultima_classificazione=? WHERE ID_Cliente=?");
                    $upd->bind_param("ssi", $nuovo_stato, $data_azione, $id_cliente);
                    $upd->execute();
                }
            }
        }
    }
}

header('Content-Type: image/gif');
echo base64_decode('R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==');
exit;
?>
