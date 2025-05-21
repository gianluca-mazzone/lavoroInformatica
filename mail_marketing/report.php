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
        <th>Campagna</th>
        <th>Data Invio</th>
        <th>Responsabile</th>
        <th>Destinatari</th>
        <th>Transizioni</th>
        <th>% Transizioni</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sql = "SELECT c.ID_Campagna, c.nome_campagna, c.data_invio, r.nome AS resp_nome, r.cognome AS resp_cognome, c.numero_destinatari
              FROM Campagna c
              JOIN Responsabili r ON c.responsabile = r.id
              ORDER BY c.data_invio DESC";
      $result = $conn->query($sql);

      if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $id_campagna = $row['ID_Campagna'];
          $trans_sql = "SELECT stato_precedente, stato_successivo, COUNT(*) as tot FROM Transizioni WHERE id_campagna=? GROUP BY stato_precedente, stato_successivo";
          $stmt = $conn->prepare($trans_sql);
          $stmt->bind_param("i", $id_campagna);
          $stmt->execute();
          $trans_res = $stmt->get_result();
          $transizioni = [];
          $tot_trans = 0;
          while ($tr = $trans_res->fetch_assoc()) {
            $transizioni[] = $tr['stato_precedente'] . "→" . $tr['stato_successivo'] . ": " . $tr['tot'];
            $tot_trans += $tr['tot'];
          }
          $percent = $row['numero_destinatari'] > 0 ? round(($tot_trans / $row['numero_destinatari']) * 100, 1) : 0;
          echo "<tr>";
          echo "<td>" . htmlspecialchars($row['nome_campagna']) . "</td>";
          echo "<td>" . date('d/m/Y', strtotime($row['data_invio'])) . "</td>";
          echo "<td>" . htmlspecialchars($row['resp_nome']) . " " . htmlspecialchars($row['resp_cognome']) . "</td>";
          echo "<td>" . $row['numero_destinatari'] . "</td>";
          echo "<td>" . implode("<br>", $transizioni) . "</td>";
          echo "<td>" . $percent . "%</td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='6'>Nessuna campagna trovata.</td></tr>";
      }
      $conn->close();
      ?>
    </tbody>
  </table>
  </div>
  <a href="index.html" class="btn">⬅️ Torna alla Dashboard</a>
</body>
</html>
