<?php
// results.php
require 'db.php';

$query = "SELECT c.name, c.position, COUNT(v.id) as total_votes 
          FROM candidates c 
          LEFT JOIN votes v ON c.id = v.candidate_id 
          GROUP BY c.id ORDER BY total_votes DESC";
$results = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Results</title>
    <style>
        body { font-family: sans-serif; background: #f4f6f9; display: flex; justify-content: center; padding-top: 50px; }
        .box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 500px; }
        h2 { color: #333; text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #f8f9fa; }
        .link { display: inline-block; margin-top: 20px; color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Election Results</h2>
        <table>
            <tr>
                <th>Candidate</th>
                <th>Position</th>
                <th>Votes</th>
            </tr>
            <?php foreach ($results as $row): ?>
            <tr>
                <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                <td><?= htmlspecialchars($row['position']) ?></td>
                <td><?= $row['total_votes'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <a href="vote.php" class="link">&larr; Back to Ballot</a>
    </div>
</body>
</html>
