<?php
function getTotalBalance($conn, $userId) {
    $total_balance = 0;
    $query = "SELECT SUM(balance) as total FROM wallets WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_balance = $row['total'] ?? 0;
    }
    return $total_balance;
}