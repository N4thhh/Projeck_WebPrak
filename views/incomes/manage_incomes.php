<?php
$page_title = 'Income Management';
require_once __DIR__ . '/../../includes/header.php';

if (!$is_logged_in) {
    header("Location: " . $base_url . "/auth/login.php");
    exit();
}

require_once __DIR__ . '/../../includes/db.php';

$query = "SELECT t.id, t.amount, t.description, t.transaction_date, 
                 COALESCE(c.name, 'No Category') as category_name, 
                 COALESCE(w.name, 'No Wallet') as wallet_name
          FROM transactions t 
          LEFT JOIN categories c ON t.category_id = c.id 
          LEFT JOIN wallets w ON t.wallet_id = w.id 
          WHERE t.user_id = ? AND t.type = 'income'
          ORDER BY t.transaction_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$incomes = $stmt->get_result();
?>

<div class="max-w-7xl mx-auto px-4 py-6">
  <!-- Header -->
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold flex items-center">
      <i data-lucide="trending-up" class="h-6 w-6 mr-2 text-green-600"></i>
      Income Management
    </h2>
    <a href="add.php" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold inline-flex items-center transition duration-300">
      <i data-lucide="plus" class="h-4 w-4 mr-1"></i>
      Add New Income
    </a>
  </div>

  <?php if (isset($_SESSION['success_message'])): ?>
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-r-lg" role="alert">
    <div class="flex items-center">
        <i data-lucide="check-circle" class="h-5 w-5 mr-2"></i>
        <p><?= $_SESSION['success_message']; ?></p>
    </div>
</div>
<?php unset($_SESSION['success_message']); endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-r-lg" role="alert">
    <div class="flex items-center">
        <i data-lucide="alert-circle" class="h-5 w-5 mr-2"></i>
        <p><?= $_SESSION['error']; ?></p>
    </div>
</div>
<?php unset($_SESSION['error']); endif; ?>

  <div class="bg-white shadow-lg rounded-lg overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-700 text-white">
        <tr>
          <th class="text-left px-6 py-4 font-semibold uppercase tracking-wider">Description</th>
          <th class="text-left px-6 py-4 font-semibold uppercase tracking-wider">Category</th>
          <th class="text-left px-6 py-4 font-semibold uppercase tracking-wider">Wallet</th>
          <th class="text-left px-6 py-4 font-semibold uppercase tracking-wider">Amount</th>
          <th class="text-left px-6 py-4 font-semibold uppercase tracking-wider">Date</th>
          <th class="text-center px-6 py-4 font-semibold uppercase tracking-wider">Actions</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <?php if ($incomes->num_rows > 0): ?>
          <?php while ($income = $incomes->fetch_assoc()): ?>
            <tr class="hover:bg-gray-50 transition-colors duration-200">
              <td class="px-6 py-4 font-medium text-gray-900"><?= htmlspecialchars($income['description']) ?></td>
              <td class="px-6 py-4 text-gray-600">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  <?= htmlspecialchars($income['category_name']) ?>
                </span>
              </td>
              <td class="px-6 py-4 text-gray-600">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                  <?= htmlspecialchars($income['wallet_name']) ?>
                </span>
              </td>
              <td class="px-6 py-4 font-bold text-green-600 text-lg">
                +Rp <?= number_format($income['amount'], 0, ',', '.') ?>
              </td>
              <td class="px-6 py-4 text-gray-600">
                <div class="flex items-center">
                  <i data-lucide="calendar" class="h-4 w-4 mr-1 text-gray-400"></i>
                  <?= date('d M Y', strtotime($income['transaction_date'])) ?>
                </div>
              </td>
              <td class="px-6 py-4 text-center">
                <a href="edit.php?id=<?= $income['id'] ?>" 
                   class="inline-flex items-center px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 text-sm font-medium rounded-lg transition duration-200">
                  <i data-lucide="edit" class="h-4 w-4 mr-1"></i>
                  Edit
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center px-6 py-12 text-gray-500">
              <div class="flex flex-col items-center">
                <i data-lucide="inbox" class="h-12 w-12 mb-4 text-gray-300"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No income records found</h3>
                <p class="text-gray-500 mb-4">Get started by adding your first income record.</p>
                <a href="add.php" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold inline-flex items-center transition duration-300">
                  <i data-lucide="plus" class="h-4 w-4 mr-1"></i>
                  Add Income
                </a>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($incomes->num_rows > 0): ?>
    <?php 
    $stmt->execute();
    $incomes_for_total = $stmt->get_result();
    $total_income = 0;
    while ($income = $incomes_for_total->fetch_assoc()) {
        $total_income += $income['amount'];
    }
    ?>
    <div class="mt-6 bg-gradient-to-r from-green-500 to-emerald-600 text-white p-6 rounded-lg shadow-lg">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-lg font-semibold mb-1">Total Income</h3>
          <p class="text-green-100 text-sm">All recorded income transactions</p>
        </div>
        <div class="text-right">
          <p class="text-3xl font-bold">Rp <?= number_format($total_income, 0, ',', '.') ?></p>
          <p class="text-green-100 text-sm"><?= $incomes->num_rows ?> transactions</p>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
