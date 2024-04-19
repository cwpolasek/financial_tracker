<?php
session_start();

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit();
}

// Include the database connection file
require_once 'includes/database-connection.php';

$userID = $_SESSION['userid'];

// Fetch user details
$stmt = $pdo->prepare("SELECT userEmail FROM user WHERE userID = ?");
$stmt->execute([$userID]);
$user = $stmt->fetch();

// Fetch account summaries
$accountsStmt = $pdo->prepare("SELECT accountType, accountBalance FROM account WHERE userID = ?");
$accountsStmt->execute([$userID]);
$accounts = $accountsStmt->fetchAll();

// Fetch recent transactions
$transactionsStmt = $pdo->prepare("SELECT transactionDescription, transactionAmount, transactionDate FROM transactions WHERE userID = ? ORDER BY transactionDate DESC LIMIT 5");
$transactionsStmt->execute([$userID]);
$transactions = $transactionsStmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($user['userEmail']) ?>!</h1>
    <h2>Account Summary</h2>
    <ul>
        <?php foreach ($accounts as $account): ?>
            <li><?= htmlspecialchars($account['accountType']) ?>: $<?= htmlspecialchars($account['accountBalance']) ?></li>
        <?php endforeach; ?>
    </ul>

    <h2>Recent Transactions</h2>
    <ul>
        <?php foreach ($transactions as $transaction): ?>
            <li><?= htmlspecialchars($transaction['transactionDescription']) ?>: $<?= htmlspecialchars($transaction['transactionAmount']) ?> on <?= htmlspecialchars($transaction['transactionDate']) ?></li>
        <?php endforeach; ?>
    </ul>

    <nav>
        <ul>
            <li><a href="manage_accounts.php">Manage Accounts</a></li>
            <li><a href="manage_budgets.php">Manage Budgets</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</body>
</html>