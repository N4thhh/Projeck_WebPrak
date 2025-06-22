<?php
$page_title = 'Edit Income';
require_once __DIR__ . '/../../includes/header.php';

if (!$is_logged_in) {
    header("Location: " . $base_url . "/auth/login.php");
    exit();
}

require_once __DIR__ . '/../../includes/db.php';

$income_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($income_id <= 0) {
    $_SESSION['error'] = "Invalid income ID.";
    header("Location: manage_incomes.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM transactions WHERE id = ? AND user_id = ? AND type = 'income'");
$stmt->bind_param("ii", $income_id, $user_id);
$stmt->execute();
$income = $stmt->get_result()->fetch_assoc();

if (!$income) {
    $_SESSION['error'] = "Income record not found.";
    header("Location: manage_incomes.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = (float) $_POST['amount'];
    $description = htmlspecialchars($_POST['description']);
    $category_id = (int) $_POST['category_id'];
    $wallet_id = (int) $_POST['wallet_id'];
    $transaction_date = $_POST['transaction_date'];

    // Debug: Let's see what we're getting
    error_log("Debug - transaction_date received: " . $transaction_date);

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
            $old_amount = $income['amount'];
            $amount_difference = $amount - $old_amount;
            
            // DEBUG: Log what we're about to insert
            error_log("Debug - About to update with date: " . $transaction_date);
            
            // FIXED: Correct bind_param format string
            // d=double, s=string, i=integer, i=integer, s=string, i=integer, i=integer
            $stmt = $conn->prepare("UPDATE transactions SET amount = ?, description = ?, category_id = ?, wallet_id = ?, transaction_date = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("dsiisii", $amount, $description, $category_id, $wallet_id, $transaction_date, $income_id, $user_id);
            $stmt->execute();

            // Handle wallet balance updates
            if ($wallet_id != $income['wallet_id']) {
                // Moving to different wallet
                $stmt_old = $conn->prepare("UPDATE wallets SET balance = balance - ? WHERE id = ?");
                $stmt_old->bind_param("di", $old_amount, $income['wallet_id']);
                $stmt_old->execute();

                $stmt_new = $conn->prepare("UPDATE wallets SET balance = balance + ? WHERE id = ?");
                $stmt_new->bind_param("di", $amount, $wallet_id);
                $stmt_new->execute();
            } else {
                // Same wallet, just update the difference
                $stmt_update = $conn->prepare("UPDATE wallets SET balance = balance + ? WHERE id = ?");
                $stmt_update->bind_param("di", $amount_difference, $wallet_id);
                $stmt_update->execute();
            }

            $conn->commit();
            
            $_SESSION['success_message'] = "Income updated successfully!";
            header("Location: manage_incomes.php");
            exit();

        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            $error_message = "Failed to update income: " . $exception->getMessage();
            error_log("Database error: " . $exception->getMessage());
        }
    } else {
        if (!isset($error_message)) {
            $error_message = "Please fill in all required fields with valid values.";
        }
    }
}

// Handle date display
$dateValue = date('Y-m-d'); // Default to today
if (!empty($income['transaction_date'])) {
    $transaction_date = $income['transaction_date'];
    
    // If it's already in Y-m-d format
    if (preg_match('/^\d{4}-\d{2}-\d{2}/', $transaction_date)) {
        $dateValue = substr($transaction_date, 0, 10); // Take only date part (in case of datetime)
    } else {
        // Try to parse other formats
        $formats = ['d/m/Y', 'd-m-Y', 'Y/m/d'];
        foreach ($formats as $format) {
            $date_obj = DateTime::createFromFormat($format, $transaction_date);
            if ($date_obj) {
                $dateValue = $date_obj->format('Y-m-d');
                break;
            }
        }
    }
}

$categories_result = $conn->query("SELECT id, name FROM categories WHERE user_id = $user_id AND type = 'income'");
$wallets_result = $conn->query("SELECT id, name, balance FROM wallets WHERE user_id = $user_id");
?>

<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white p-8 rounded-xl shadow-lg">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Edit Income</h2>
            <a href="manage_incomes.php" class="text-gray-600 hover:text-gray-800 flex items-center">
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
                           value="<?= htmlspecialchars($income['amount']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label for="wallet_id" class="block text-gray-700 font-semibold mb-2">Deposit to (Wallet)</label>
                    <select id="wallet_id" name="wallet_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Select Wallet --</option>
                        <?php while ($wallet = $wallets_result->fetch_assoc()): ?>
                            <option value="<?= $wallet['id'] ?>" <?= $wallet['id'] == $income['wallet_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($wallet['name']) ?> (Rp <?= number_format($wallet['balance']) ?>)
                            </option>
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
                        <option value="<?= $category['id'] ?>" <?= $category['id'] == $income['category_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endwhile; } ?>
                </select>
            </div>

            <div class="mt-4">
                <label for="description" class="block text-gray-700 font-semibold mb-2">Description</label>
                <input type="text" id="description" name="description" required 
                       value="<?= htmlspecialchars($income['description']) ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="mt-4">
                <label for="transaction_date" class="block text-gray-700 font-semibold mb-2">Date</label>
                <input type="date" id="transaction_date" name="transaction_date" required
                       value="<?= htmlspecialchars($dateValue) ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            
            <div class="mt-8 flex justify-between">
                <a href="manage_incomes.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300 flex items-center">
                    <i data-lucide="x" class="h-4 w-4 mr-2"></i>
                    Cancel
                </a>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300 flex items-center">
                    <i data-lucide="save" class="h-4 w-4 mr-2"></i>
                    Update Income
                </button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>