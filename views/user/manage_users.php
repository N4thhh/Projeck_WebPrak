<?php
$page_title = 'Manage Users';
require_once __DIR__ . '/../../includes/header.php';

if (!$is_logged_in) {
    header("Location: " . $base_url . "/auth/login.php");
    exit();
}

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../functions/user_functions.php';

$users = getAllUsers($conn);
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800 flex items-center">
            <i data-lucide="users" class="h-8 w-8 mr-3 text-gray-600"></i>
            User Management
        </h1>
        <a href="add.php" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg shadow flex items-center transition duration-200">
            <i data-lucide="plus" class="h-5 w-5 mr-2"></i>
            Add New User
        </a>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
    <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-4" role="alert">
        <p><?= $_SESSION['message']; ?></p>
    </div>
    <?php unset($_SESSION['message']); endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
        <p><?= $_SESSION['error']; ?></p>
    </div>
    <?php unset($_SESSION['error']); endif; ?>


    <div class="bg-white rounded-xl shadow-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-700 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Username</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Joined On</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($user['username']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($user['email']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date("d M Y", strtotime($user['created_at'])); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-4">
                                <a href="edit.php?id=<?= $user['id']; ?>" class="text-sky-600 hover:text-sky-800 font-semibold">Edit</a>
                                <a href="delete.php?id=<?= $user['id']; ?>" class="text-red-600 hover:text-red-800 font-semibold" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-10 text-gray-500">No users found. Try adding one!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>