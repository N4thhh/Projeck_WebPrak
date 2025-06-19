<?php
$page_title = 'Dashboard';
require_once __DIR__. '/../includes/header.php';

if (!$is_logged_in) {
    header("Location: " . $base_url . "/auth/login.php");
    exit();
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../functions/wallet_functions.php';
require_once __DIR__ . '/../functions/transaction_functions.php';

$total_balance = getTotalBalance($conn, $user_id);
$monthly_income = getCurrentMonthTotal($conn, $user_id, 'income');
$monthly_expense = getCurrentMonthTotal($conn, $user_id, 'expense');
$chart_info = getExpenseChartData($conn, $user_id);
$recent_transactions = getRecentTransactions($conn, $user_id, 5);
?>

<div class="max-w-7xl mx-auto py-6">
    <div class="bg-gradient-to-r from-emerald-600 to-green-600 text-white p-8 rounded-xl shadow-lg mb-8">
        <h1 class="text-3xl font-bold mb-2">Welcome Back, <?= htmlspecialchars($username) ?>!</h1>
        <p class="text-emerald-100">Here is your financial summary for this month.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-sky-500">
            <div class="flex items-center justify-between">
                <div><p class="text-gray-600 text-sm font-medium">Total Balance</p><p class="text-2xl font-bold text-gray-800">Rp <?= number_format($total_balance, 0, ',', '.') ?></p></div>
                <div class="bg-sky-100 p-3 rounded-full"><i data-lucide="wallet" class="h-6 w-6 text-sky-600"></i></div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div><p class="text-gray-600 text-sm font-medium">This Month's Income</p><p class="text-2xl font-bold text-gray-800">Rp <?= number_format($monthly_income, 0, ',', '.') ?></p></div>
                <div class="bg-green-100 p-3 rounded-full"><i data-lucide="trending-up" class="h-6 w-6 text-green-600"></i></div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-red-500">
             <div class="flex items-center justify-between">
                <div><p class="text-gray-600 text-sm font-medium">This Month's Expense</p><p class="text-2xl font-bold text-gray-800">Rp <?= number_format($monthly_expense, 0, ',', '.') ?></p></div>
                <div class="bg-red-100 p-3 rounded-full"><i data-lucide="trending-down" class="h-6 w-6 text-red-600"></i></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-md">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Expense Chart (Last 6 Months)</h3>
            <div class="relative" style="height:250px">
                <canvas id="expenseChart"></canvas>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h3>
            
            <div class="grid grid-cols-2 gap-4">
                <a href="incomes/add.php" class="flex flex-col items-center justify-center bg-green-50 hover:bg-green-100 border border-green-200 p-4 rounded-lg text-center transition"><i data-lucide="plus-circle" class="h-8 w-8 text-green-600 mb-2"></i><p class="text-green-700 font-semibold">Add Income</p></a>
                <a href="<?= $base_url ?>/views/expenses.php" class="flex flex-col items-center justify-center bg-red-50 hover:bg-red-100 border border-red-200 p-4 rounded-lg text-center transition"><i data-lucide="minus-circle" class="h-8 w-8 text-red-600 mb-2"></i><p class="text-red-700 font-semibold">Add Expense</p></a>
                <a href="<?= $base_url ?>/views/wallets/manage_wallets.php" class="flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 border border-blue-200 p-4 rounded-lg text-center transition"><i data-lucide="credit-card" class="h-8 w-8 text-blue-600 mb-2"></i><p class="text-blue-700 font-semibold">Manage Wallets</p></a>
                <a href="<?= $base_url ?>/views/categories.php" class="flex flex-col items-center justify-center bg-purple-50 hover:bg-purple-100 border border-purple-200 p-4 rounded-lg text-center transition"><i data-lucide="tags" class="h-8 w-8 text-purple-600 mb-2"></i><p class="text-purple-700 font-semibold">Manage Categories</p></a>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-md">
        <div class="flex justify-between items-center mb-4"><h3 class="text-xl font-bold text-gray-800">Recent Transactions</h3><a href="<?= $base_url ?>/views/history.php" class="text-emerald-600 hover:text-emerald-700 font-semibold">View All →</a></div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Date</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Description</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Category</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Wallet</th>
                        <th class="px-4 py-2 text-right font-semibold text-gray-600">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (!empty($recent_transactions)): ?>
                        <?php foreach ($recent_transactions as $transaction): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600"><?= date('d M, Y', strtotime($transaction['transaction_date'])) ?></td>
                                <td class="px-4 py-3 font-medium text-gray-800"><?= htmlspecialchars($transaction['description']) ?></td>
                                <td class="px-4 py-3 text-gray-600"><?= htmlspecialchars($transaction['category_name']) ?></td>
                                <td class="px-4 py-3 text-gray-600"><?= htmlspecialchars($transaction['wallet_name']) ?></td>
                                <td class="px-4 py-3 text-right font-semibold <?= $transaction['type'] == 'income' ? 'text-green-600' : 'text-red-600' ?>">
                                    <?= $transaction['type'] == 'income' ? '+' : '-' ?>Rp <?= number_format($transaction['amount'], 0, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-6 text-gray-500 italic">No transactions recorded yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('expenseChart').getContext('2d');
    const expenseChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_info['labels']) ?>,
            datasets: [{
                label: 'Total Expenses',
                data: <?= json_encode($chart_info['data']) ?>,
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });
</script>

<?php require_once __DIR__ . '/../includes/footer.php';?>
