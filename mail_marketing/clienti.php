<?php
include 'includes/db.php';

$data_attuale = date('Y-m-d'); //prendiamo la data dal server
$reset_query = "UPDATE Cliente 
                SET stato_attuale = 'dormiente', data_ultimo_aggiornamento = '$data_attuale' 
                WHERE data_ultimo_aggiornamento < 365 ";

$sql = "SELECT ID_Cliente, email, stato_attuale FROM Cliente ORDER BY ID_Cliente";
$result = $conn->query($sql);
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
  <div class="table-box">
    <table>
      <thead>
        <tr>
          <th>Email</th>
          <th>Stato Attuale</th>
          <th>Azioni</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . $row['stato_attuale'] . "</td>";
            echo "<td><a href='transizione.php?id_cliente=" . $row['ID_Cliente'] . "' class='btn'>➕ Registra Transizione</a></td>";
            echo "</tr>";
          }
        }
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>
