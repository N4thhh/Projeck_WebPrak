<?php

function addExpense(mysqli $conn, int $user_id, int $account_id, int $category_id, float $amount, string $description, string $transaction_date): bool
{
    $conn->begin_transaction();

    try {
        $stmt_insert = $conn->prepare(
            "INSERT INTO transactions (user_id, account_id, category_id, type, amount, description, transaction_date) 
             VALUES (?, ?, ?, 'expense', ?, ?, ?)"
        );
        $stmt_insert->bind_param("iiisds", $user_id, $account_id, $category_id, $amount, $description, $transaction_date);
        $stmt_insert->execute();

        $stmt_update = $conn->prepare("UPDATE accounts SET balance = balance - ? WHERE id = ? AND user_id = ?");
        $stmt_update->bind_param("dii", $amount, $account_id, $user_id);
        $stmt_update->execute();
        
        $conn->commit();
        return true;

    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        return false;
    }
}
