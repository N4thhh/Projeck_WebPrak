<?php
$page_title = 'Add Income';
require_once __DIR__ . '/../../includes/header.php';

if (!$is_logged_in) {
    header("Location: " . $base_url . "/auth/login.php");
    exit();
}

require_once __DIR__ . '/../../includes/db.php';

$debug_categories = $conn->query("SELECT COUNT(*) as count FROM categories WHERE user_id = $user_id AND type = 'income'")->fetch_assoc();
$debug_wallets = $conn->query("SELECT COUNT(*) as count FROM wallets WHERE user_id = $user_id")->fetch_assoc();

if ($debug_categories['count'] == 0) {
    $warning_message = "You need to create income categories first. <a href='../categories.php' class='underline'>Create categories here</a>";
}
if ($debug_wallets['count'] == 0) {
    $warning_message = "You need to create wallets first. <a href='../wallets.php' class='underline'>Create wallets here</a>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = (float) $_POST['amount'];
    $description = htmlspecialchars($_POST['description']);
    $category_id = (int) $_POST['category_id'];
    $wallet_id = (int) $_POST['wallet_id'];
    $transaction_date = $_POST['transaction_date'];

    // Debug: Log what we're receiving
    error_log("Debug - Add Income - transaction_date received: " . $transaction_date);

    // Validate date format
    if (empty($transaction_date)) {
        $error_message = "Transaction date is required.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $transaction_date)) {
        $error_message = "Invalid date format. Expected YYYY-MM-DD, got: " . $transaction_date;
    } else {
        // Validate it's a real date
        $date_parts = explode('-', $transaction_date);
        if (!checkdate($date_parts[1], $date_parts[2], $date_parts[0])) {
            $error_message = "Invalid date. Please enter a valid date.";
        }
    }

    if (!isset($error_message) && $amount > 0 && !empty($category_id) && !empty($wallet_id)) {
        $conn->begin_transaction();
        try {
            // Debug: Log what we're about to insert
            error_log("Debug - Add Income - About to insert with date: " . $transaction_date);
            
            // FIXED: Correct bind_param format string
            // i=integer, i=integer, i=integer, d=double, s=string, s=string
            $stmt = $conn->prepare("INSERT INTO transactions (user_id, wallet_id, category_id, type, amount, description, transaction_date) VALUES (?, ?, ?, 'income', ?, ?, ?)");
            $stmt->bind_param("iiidss", $user_id, $wallet_id, $category_id, $amount, $description, $transaction_date);
            $stmt->execute();

            // Update wallet balance
            $stmt_update = $conn->prepare("UPDATE wallets SET balance = balance + ? WHERE id = ? AND user_id = ?");
            $stmt_update->bind_param("dii", $amount, $wallet_id, $user_id);
            $stmt_update->execute();

            $conn->commit();
            
            $_SESSION['success_message'] = "Income for '" . htmlspecialchars($description) . "' has been recorded successfully!";
            
            header("Location: " . $base_url . "/views/incomes/add.php");
            exit();

        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            $error_message = "Failed to add income: " . $exception->getMessage();
            error_log("Database error in add income: " . $exception->getMessage());
        }
    } else {
        if (!isset($error_message)) {
            $error_message = "Please fill in all required fields with valid values.";
        }
    }
}

$categories_result = $conn->query("SELECT id, name FROM categories WHERE user_id = $user_id AND type = 'income'");
$wallets_result = $conn->query("SELECT id, name, balance FROM wallets WHERE user_id = $user_id");
?>

<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white p-8 rounded-xl shadow-lg">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Record New Income</h2>
        
        <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">';
            echo '<p class="font-bold">Success!</p>';
            echo '<p>' . $_SESSION['success_message'] . '</p>';
            echo '</div>';
            unset($_SESSION['success_message']);
        }
        ?>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p class="font-bold">Error!</p>
                <p><?= $error_message; ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($warning_message)): ?>
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                <p class="font-bold">Warning!</p>
                <p><?= $warning_message; ?></p>
            </div>
        <?php endif; ?>

        <form method="POST">
             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="amount" class="block text-gray-700 font-semibold mb-2">Amount</label>
                    <input type="number" id="amount" name="amount" step="0.01" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label for="wallet_id" class="block text-gray-700 font-semibold mb-2">Deposit to (Wallet)</label>
                    <select id="wallet_id" name="wallet_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Select Wallet --</option>
                        <?php while ($wallet = $wallets_result->fetch_assoc()): ?>
                            <option value="<?= $wallet['id'] ?>"><?= htmlspecialchars($wallet['name']) ?> (Rp <?= number_format($wallet['balance']) ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <label for="category_id" class="block text-gray-700 font-semibold mb-2">Category</label>
                <select id="category_id" name="category_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Select Category --</option>
                    <?php if ($categories_result) { while ($category = $categories_result->fetch_assoc()): ?>
                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                    <?php endwhile; } ?>
                </select>
            </div>

            <div class="mt-4">
                <label for="description" class="block text-gray-700 font-semibold mb-2">Description</label>
                <input type="text" id="description" name="description" required placeholder="e.g., Monthly Salary"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="mt-4">
                <label for="transaction_date" class="block text-gray-700 font-semibold mb-2">Date</label>
                <input type="date" id="transaction_date" name="transaction_date" value="<?= date('Y-m-d') ?>" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div class="mt-6 text-right">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-lg transition duration-300">
                    Save Income
                </button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>