<?php
$page_title = 'Manage Wallets';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

if (!$is_logged_in) {
    header("Location: " . $base_url . "/auth/login.php");
    exit();
}

require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_wallet'])) {
    $type = htmlspecialchars($_POST['type']);
    $name = htmlspecialchars($_POST['name']);
    $initial_balance = (float) str_replace(['Rp', '.', ','], ['', '', '.'], $_POST['initial_balance']);

    if (!empty($name) && !empty($type)) {
        $stmt = $conn->prepare("INSERT INTO wallets (user_id, type, name, balance) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issd", $user_id, $type, $name, $initial_balance);
        
        if ($stmt->execute()) {
            header("Location: " . $base_url . "/views/wallets.php?status=success");
            exit();
        } else {
            $error_message = "Failed to add wallet. Please try again.";
        }
    } else {
        $error_message = "Account Type and Account Name cannot be empty.";
    }
}

$stmt_get_wallets = $conn->prepare("SELECT id, name, type, balance FROM wallets WHERE user_id = ? ORDER BY name ASC");
$stmt_get_wallets->bind_param("i", $user_id);
$stmt_get_wallets->execute();
$wallets_result = $stmt_get_wallets->get_result();
?>

<div class="max-w-4xl mx-auto py-6">

    <div class="bg-white p-8 rounded-xl shadow-lg mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Add New Wallet</h2>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?= $error_message; ?></span>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">New wallet added successfully!</span>
            </div>
        <?php endif; ?>

        <form action="wallets.php" method="POST" class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="type" class="block text-gray-700 font-semibold mb-2">Wallet Type</label>
                    <select id="type" name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Select a Type --</option>
                        <option value="Tunai">Cash</option>
                        <option value="Rekening Bank">Bank Account</option>
                        <option value="Dompet Digital">Digital Wallet</option>
                    </select>
                </div>
                <div>
                    <label for="name" class="block text-gray-700 font-semibold mb-2">Wallet Name</label>
                    <input type="text" id="name" name="name" placeholder="e.g., Bank BNI, GoPay" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div class="md:col-span-2">
                    <label for="initial_balance" class="block text-gray-700 font-semibold mb-2">Initial Balance</label>
                    <input type="number" id="initial_balance" name="initial_balance" placeholder="0" value="0" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>
            <div class="mt-6 text-right">
                <button type="submit" name="add_wallet"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-lg transition duration-300">
                    Add Wallet
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white p-8 rounded-xl shadow-lg">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Your Wallets</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold text-gray-600">Wallet Name</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-600">Type</th>
                        <th class="py-3 px-4 text-right font-semibold text-gray-600">Current Balance</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 divide-y">
                    <?php if ($wallets_result && $wallets_result->num_rows > 0): ?>
                        <?php while($row = $wallets_result->fetch_assoc()): ?>
                            <tr>
                                <td class="py-4 px-4 font-medium"><?= htmlspecialchars($row['name']) ?></td>
                                <td class="py-4 px-4"><?= htmlspecialchars($row['type']) ?></td>
                                <td class="py-4 px-4 text-right text-lg font-semibold text-emerald-700">
                                    Rp <?= number_format($row['balance'], 0, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-6 text-gray-500">You haven't added any wallets yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>