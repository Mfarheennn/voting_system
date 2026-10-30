<?php
// vote.php
session_set_cookie_params(120); // 1. Set cookie lifetime to 2 minutes
session_start();
require 'db.php';

if (isset($_SESSION['last_click']) && (time() - $_SESSION['last_click'] > 120)) { session_unset(); session_destroy(); header("Location: login.php"); exit; }
$_SESSION['last_click'] = time(); // Updates the timer every time they open/refresh the page


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if this user ID already exists in the votes table
$stmt = $pdo->prepare("SELECT id FROM votes WHERE user_id = ?");
$stmt->execute([$user_id]);
$has_voted = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$has_voted) {
    if (isset($_POST['candidate_id'])) {
        $candidate_id = $_POST['candidate_id'];
        $stmt = $pdo->prepare("INSERT INTO votes (user_id, candidate_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $candidate_id]);
        header("Location: results.php");
        exit;
    }
}

$candidates = $pdo->query("SELECT * FROM candidates")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ballot Box</title>
    <style>
        body { font-family: sans-serif; background: #f4f6f9; display: flex; justify-content: center; padding-top: 50px; }
        .box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 400px; }
        h2 { color: #333; text-align: center; margin-bottom: 5px; }
        p { text-align: center; color: #666; margin-bottom: 25px; }
        .candidate-row { background: #f8f9fa; padding: 12px; margin-bottom: 10px; border-radius: 6px; display: flex; align-items: center; border: 1px solid #e9ecef; }
        .candidate-row input { margin-right: 15px; width: auto; }
        button { width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin-top: 15px; }
        button:hover { background: #218838; }
        .alert { padding: 12px; text-align: center; background: #fff3cd; color: #856404; border-radius: 4px; }
        .link { display: block; text-align: center; margin-top: 15px; color: #dc3545; text-decoration: none; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Official Ballot</h2>
        <p>Choose one option below</p>

        <?php if ($has_voted): ?>
            <div class="alert">You have already submitted your vote.</div>
            <a href="results.php" class="link" style="color:#007bff;">View Results</a>
        <?php else: ?>
            <form method="POST">
                <?php foreach ($candidates as $c): ?>
                    <label class="candidate-row">
                        <input type="radio" name="candidate_id" value="<?= $c['id'] ?>" required>
                        <div>
                            <strong><?= htmlspecialchars($c['name']) ?></strong><br>
                            <small style="color:#777;"><?= htmlspecialchars($c['position']) ?></small>
                        </div>
                    </label>
                <?php endforeach; ?>
                <button type="submit">Submit Vote</button>
            </form>
        <?php endif; ?>
        
        <a href="login.php" class="link">Logout</a>
    </div>
</body>
</html>
