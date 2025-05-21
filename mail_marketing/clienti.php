<?php
include 'includes/db.php';

$data_attuale = date('Y-m-d');
$reset_query = "UPDATE Cliente 
                SET stato_classificazione = 'dormiente', data_ultima_classificazione = ?
                WHERE DATEDIFF(?, data_ultima_classificazione) > 365";
$stmt = $conn->prepare($reset_query);
$stmt->bind_param("ss", $data_attuale, $data_attuale);
$stmt->execute();

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search) {
    $sql = "SELECT ID_Cliente, nome, cognome, email, stato_classificazione, data_ultima_classificazione FROM Cliente WHERE email LIKE ? ORDER BY ID_Cliente";
    $param = "%$search%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT ID_Cliente, nome, cognome, email, stato_classificazione, data_ultima_classificazione FROM Cliente ORDER BY ID_Cliente";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Elenco Clienti</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>📋 Elenco Clienti</h1>
  <form method="get" class="form-box" style="max-width:300px;margin-bottom:20px;">
    <input type="text" name="search" placeholder="Cerca per email..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit" class="btn">🔍 Cerca</button>
  </form>
  <div class="table-box">
    <table>
      <thead>
        <tr>
          <th>Nome</th>
          <th>Cognome</th>
          <th>Email</th>
          <th>Stato</th>
          <th>Ultima Classificazione</th>
          <th>Azioni</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
            echo "<td>" . htmlspecialchars($row['cognome']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . ucfirst($row['stato_classificazione']) . "</td>";
            echo "<td>" . htmlspecialchars($row['data_ultima_classificazione']) . "</td>";
            echo "<td><a href='transizione.php?id_cliente=" . $row['ID_Cliente'] . "' class='btn'>➕ Transizione</a></td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='6'>Nessun cliente trovato.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
  <a href="index.html" class="btn">⬅️ Torna alla Dashboard</a>
</body>
</html>
