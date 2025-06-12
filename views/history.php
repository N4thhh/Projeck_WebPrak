<?php
$page_title = 'Transaction History';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($page_title); ?> - MaWallet</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <?php require_once __DIR__ . '/../includes/header.php'; ?>

    <main class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold mb-6">Transaction History</h1>

        <div class="bg-white shadow-xl rounded-xl overflow-hidden">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-emerald-600 text-white">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Amount</th>
                        <th class="px-6 py-3">Description</th>
                        <th class="px-6 py-3">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white text-gray-700">
                <?php
                $query = "SELECT * FROM transactions";
                $result = $conn->query($query);

                if ($result && $result->num_rows > 0):
                    $no = 1;
                    while ($row = $result->fetch_assoc()):
                ?>
                    <tr class="border-t hover:bg-gray-100 transition">
                        <td class="px-6 py-4"><?= $no++; ?></td>
                        <td class="px-6 py-4 capitalize"><?= htmlspecialchars($row['type']); ?></td>
                        <td class="px-6 py-4">Rp <?= number_format($row['amount'], 0, ',', '.'); ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($row['description']); ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($row['transaction_date']); ?></td>
                    </tr>
                <?php
                    endwhile;
                else:
                ?>
                    <tr>
                        <td colspan="8" class="text-center px-6 py-10 text-gray-500 italic">No transactions yet.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
<?php
require_once __DIR__ . '/../includes/footer.php';
?>
