<?php
function getCurrentMonthTotal($conn, $userId, $type) {
    $total = 0;
    $query = "SELECT SUM(amount) as total FROM transactions 
              WHERE user_id = ? AND type = ? AND MONTH(transaction_date) = MONTH(CURDATE()) AND YEAR(transaction_date) = YEAR(CURDATE())";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $userId, $type);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total = $row['total'] ?? 0;
    }
    return $total;
}


function getRecentTransactions($conn, $userId, $limit = 5) {
    $transactions = [];
    $query = "SELECT 
                t.transaction_date, t.description, t.amount, t.type, 
                w.name as wallet_name, 
                c.name as category_name
              FROM transactions t
              LEFT JOIN wallets w ON t.wallet_id = w.id
              LEFT JOIN categories c ON t.category_id = c.id
              WHERE t.user_id = ?
              ORDER BY t.transaction_date DESC, t.id DESC
              LIMIT ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $userId, $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }
    }
    return $transactions;
}

function getExpenseChartData($conn, $userId) {
    $chart_data = [];
    $labels = [];
    for ($i = 5; $i >= 0; $i--) {
        $month = date('m', strtotime("-$i months"));
        $year = date('Y', strtotime("-$i months"));
        
        $query = "SELECT SUM(amount) as total FROM transactions 
                  WHERE user_id = ? AND type = 'expense' AND MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $userId, $month, $year);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        $chart_data[] = $result['total'] ?? 0;
        $labels[] = date('M Y', strtotime("-$i months"));
    }
    return ['labels' => $labels, 'data' => $chart_data];
}