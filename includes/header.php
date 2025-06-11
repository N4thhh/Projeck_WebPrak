<?php
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Dashboard'); ?> - MaWallet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide-static@0.395.0/dist/lucide.js"></script>
    <style>
        body { display: flex; flex-direction: column; min-height: 100vh; }
        main { flex-grow: 1; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">
    <header class="bg-emerald-600 text-white shadow-lg">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="<?= htmlspecialchars($base_url) ?>/views/dashboard.php" class="text-2xl font-bold tracking-wider">
                MaWallet
            </a>
            <div class="flex items-center space-x-4">
                <?php if ($is_logged_in): ?>
                    <span class="font-semibold hidden md:inline">Welcome, <?= htmlspecialchars($username); ?>!</span>
                    <a href="<?= htmlspecialchars($base_url) ?>/views/dashboard.php" class="hover:text-emerald-200">Dashboard</a>
                    <a href="<?= htmlspecialchars($base_url) ?>/views/accounts.php" class="hover:text-emerald-200">Accounts</a>
                    <a href="<?= htmlspecialchars($base_url) ?>/views/history.php" class="hover:text-emerald-200">History</a>
                    <a href="<?= htmlspecialchars($base_url) ?>/views/incomes.php" class="hover:text-emerald-200">Incomes</a> 
                    <a href="<?= htmlspecialchars($base_url) ?>/views/expenses.php" class="hover:text-emerald-200">Expenses</a> 
                    <a href="<?= htmlspecialchars($base_url) ?>/auth/logout.php"
                       class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 flex items-center space-x-2">
                        <i data-lucide="log-out" class="h-5 w-5"></i>
                        <span>Logout</span>
                    </a>
                <?php else: ?>
                    <a href="<?= htmlspecialchars($base_url) ?>/auth/login.php" class="bg-emerald-500 hover:bg-emerald-400 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">Login</a>
                    <a href="<?= htmlspecialchars($base_url) ?>/auth/register.php" class="bg-gray-100 hover:bg-gray-200 text-emerald-600 font-semibold py-2 px-4 rounded-lg transition duration-300">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main class="container mx-auto px-6 py-8">