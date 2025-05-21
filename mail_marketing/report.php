<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Report Campagne</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1 class="title">📊 Report Campagne Email</h1>

  <div class="table-box">
  <table>
    <thead>
      <tr>
        <th>Nome</th>
        <th>Data Invio</th>
        <th>Descrizione</th>
        <th>Responsabile</th>
        <th>Clienti Raggiunti</th>
      </tr>
    </thead>
    <tbody>

        <?php
        $sql = "SELECT nome, data_invio,descrizione,responsabile,n_clienti_raggiunti FROM campagna ORDER BY data_invio DESC";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
            echo "<td>" . date('d/m/Y', strtotime($row['data_invio'])) . "</td>";
            echo "<td>" . htmlspecialchars($row['descrizione']) . "</td>";
            echo "<td>" . htmlspecialchars($row['responsabile']) . "</td>";
            echo "<td>" . $row['n_clienti_raggiunti'] . "</td>";
            echo "</tr>";
          }
        }
        
         else {
          echo "<tr><td colspan='7'>Nessuna campagna trovata.</td></tr>";
        }

        $conn->close();
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>
