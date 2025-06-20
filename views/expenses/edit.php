<?php
$page_title = 'Edit Expense';
require_once __DIR__ . '/../../includes/header.php';

if (!$is_logged_in) {
    header("Location: " . $base_url . "/auth/login.php");
    exit();
}

require_once __DIR__ . '/../../includes/db.php';

$expense_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($expense_id <= 0) {
    $_SESSION['error'] = "Invalid expense ID.";
    header("Location: manage_expenses.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM transactions WHERE id = ? AND user_id = ? AND type = 'expense'");
$stmt->bind_param("ii", $expense_id, $user_id);
$stmt->execute();
$expense = $stmt->get_result()->fetch_assoc();

if (!$expense) {
    $_SESSION['error'] = "Expense record not found.";
    header("Location: manage_expenses.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = (float) $_POST['amount'];
    $description = htmlspecialchars($_POST['description']);
    $category_id = (int) $_POST['category_id'];
    $wallet_id = (int) $_POST['wallet_id'];
    $transaction_date = $_POST['transaction_date'];

    if ($amount > 0 && !empty($category_id) && !empty($wallet_id) && !empty($transaction_date)) {
        
        $old_amount = (float)$expense['amount'];
        $old_wallet_id = (int)$expense['wallet_id'];

        $stmt_balance_check = $conn->prepare("SELECT balance FROM wallets WHERE id = ?");
        $stmt_balance_check->bind_param("i", $wallet_id);
        $stmt_balance_check->execute();
        $new_wallet_balance = $stmt_balance_check->get_result()->fetch_assoc()['balance'];
        
        $balance_is_sufficient = false;
        if ($wallet_id === $old_wallet_id) {
            if ($new_wallet_balance + $old_amount >= $amount) {
                $balance_is_sufficient = true;
            }
        } else {
            if ($new_wallet_balance >= $amount) {
                $balance_is_sufficient = true;
            }
        }
        
        if ($balance_is_sufficient) {
            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare("UPDATE transactions SET amount = ?, description = ?, category_id = ?, wallet_id = ?, transaction_date = ? WHERE id = ? AND user_id = ?");
                $stmt->bind_param("dsiissi", $amount, $description, $category_id, $wallet_id, $transaction_date, $expense_id, $user_id);
                $stmt->execute();

                $stmt_old = $conn->prepare("UPDATE wallets SET balance = balance + ? WHERE id = ?");
                $stmt_old->bind_param("di", $old_amount, $old_wallet_id);
                $stmt_old->execute();
                
                $stmt_new = $conn->prepare("UPDATE wallets SET balance = balance - ? WHERE id = ?");
                $stmt_new->bind_param("di", $amount, $wallet_id);
                $stmt_new->execute();

                $conn->commit();
                
                $_SESSION['success_message'] = "Expense updated successfully!";
                header("Location: manage_expenses.php");
                exit();

            } catch (mysqli_sql_exception $exception) {
                $conn->rollback();
                $error_message = "Failed to update expense: " . $exception->getMessage();
            }
        } else {
             $error_message = "Insufficient funds in the selected wallet for the new amount.";
        }
    } else {
        $error_message = "Please fill in all required fields.";
    }
}

$categories_result = $conn->query("SELECT id, name FROM categories WHERE user_id = $user_id AND type = 'expense'");
$wallets_result = $conn->query("SELECT id, name, balance FROM wallets WHERE user_id = $user_id");
?>

<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white p-8 rounded-xl shadow-lg">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Edit Expense</h2>
            <a href="manage_expenses.php" class="text-gray-600 hover:text-gray-800 flex items-center">
                <i data-lucide="arrow-left" class="h-4 w-4 mr-1"></i>
                Back to List
            </a>
        </div>
        
        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p class="font-bold">Error!</p>
                <p><?= $error_message; ?></p>
            </div>
        <?php endif; ?>

        <form method="POST">
             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="amount" class="block text-gray-700 font-semibold mb-2">Amount</label>
                    <input type="number" id="amount" name="amount" step="0.01" required 
                           value="<?= htmlspecialchars($expense['amount']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label for="wallet_id" class="block text-gray-700 font-semibold mb-2">Source of Funds (Wallet)</label>
                    <select id="wallet_id" name="wallet_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">-- Select Wallet --</option>
                        <?php while ($wallet = $wallets_result->fetch_assoc()): ?>
                            <option value="<?= $wallet['id'] ?>" <?= $wallet['id'] == $expense['wallet_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($wallet['name']) ?> (Rp <?= number_format($wallet['balance']) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <label for="category_id" class="block text-gray-700 font-semibold mb-2">Category</label>
                <select id="category_id" name="category_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">-- Select Category --</option>
                    <?php if ($categories_result) { while ($category = $categories_result->fetch_assoc()): ?>
                        <option value="<?= $category['id'] ?>" <?= $category['id'] == $expense['category_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endwhile; } ?>
                </select>
            </div>

            <div class="mt-4">
                <label for="description" class="block text-gray-700 font-semibold mb-2">Description</label>
                <input type="text" id="description" name="description" required 
                       value="<?= htmlspecialchars($expense['description']) ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="mt-4">
                <label for="transaction_date" class="block text-gray-700 font-semibold mb-2">Date</label>
                <input type="date" id="transaction_date" name="transaction_date" required
                       value="<?= htmlspecialchars($expense['transaction_date']) ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            
            <div class="mt-8 flex justify-between">
                <a href="manage_expenses.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300 flex items-center">
                    <i data-lucide="x" class="h-4 w-4 mr-2"></i>
                    Cancel
                </a>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300 flex items-center">
                    <i data-lucide="save" class="h-4 w-4 mr-2"></i>
                    Update Expense
                </button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
