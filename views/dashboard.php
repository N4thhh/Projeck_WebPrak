<?php
$page_title = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';

// Security Check
if (!$is_logged_in) {
    header("Location: " . $base_url . "/auth/login.php");
    exit();
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../functions/wallet_functions.php';
require_once __DIR__ . '/../functions/transaction_functions.php';

$total_balance = getTotalBalance($conn, $user_id);
$total_income = getCurrentMonthTotal($conn, $user_id, 'income');
$total_expense = getCurrentMonthTotal($conn, $user_id, 'expense');
$recent_transactions = getRecentTransactions($conn, $user_id, 5);

?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-emerald-100 p-6 rounded-xl flex items-center space-x-4 shadow">
        <div class="bg-emerald-500 p-4 rounded-xl"><i data-lucide="arrow-up-circle" class="h-8 w-8 text-white"></i></div>
        <div>
            <p class="text-gray-500 text-sm">This Month's Income</p>
            <p class="text-3xl font-bold text-gray-800">Rp <?= number_format($total_income, 0, ',', '.'); ?></p>
        </div>
    </div>
    <div class="bg-red-100 p-6 rounded-xl flex items-center space-x-4 shadow">
        <div class="bg-red-500 p-4 rounded-xl"><i data-lucide="arrow-down-circle" class="h-8 w-8 text-white"></i></div>
        <div>
            <p class="text-gray-500 text-sm">This Month's Expense</p>
            <p class="text-3xl font-bold text-gray-800">Rp <?= number_format($total_expense, 0, ',', '.'); ?></p>
        </div>
    </div>
    <div class="bg-sky-100 p-6 rounded-xl flex items-center space-x-4 shadow">
        <div class="bg-sky-500 p-4 rounded-xl"><i data-lucide="wallet" class="h-8 w-8 text-white"></i></div>
        <div>
            <p class="text-gray-500 text-sm">Total Balance</p>
            <p class="text-3xl font-bold text-gray-800">Rp <?= number_format($total_balance, 0, ',', '.'); ?></p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-6 gap-8">

    <div class="lg:col-span-4">
        <h2 class="text-2xl font-bold text-gray-700 mb-4">Recent Transactions</h2>
        <div class="bg-white rounded-xl shadow-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wallet</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($recent_transactions)): ?>
                        <?php foreach ($recent_transactions as $transaction): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date("d M, Y", strtotime($transaction['transaction_date'])); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($transaction['description']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($transaction['wallet_name']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right <?= $transaction['type'] === 'income' ? 'text-emerald-600' : 'text-red-600'; ?>">
                                    <?= ($transaction['type'] === 'income' ? '+' : '-') . ' Rp ' . number_format($transaction['amount'], 0, ',', '.'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-10 text-gray-500">No recent transactions found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-lg p-6 space-y-8">
            <a href="<?= $base_url ?>/views/incomes.php" class="w-full flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-4 px-4 rounded-lg transition duration-300">
                <i data-lucide="plus-circle" class="h-6 w-6 mr-3"></i>
                Record New Income
            </a>
            
            <a href="<?= $base_url ?>/views/expenses.php" class="w-full flex items-center justify-center bg-red-500 hover:bg-red-600 text-white font-bold py-4 px-4 rounded-lg transition duration-300">
                <i data-lucide="minus-circle" class="h-6 w-6 mr-3"></i>
                Record New Expense
            </a>
        </div>
    </div>

</div>


<?php
require_once __DIR__ . '/../includes/footer.php';
?>