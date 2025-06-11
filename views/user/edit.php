<?php
$page_title = 'Edit User';
require_once __DIR__ . '/../../includes/header.php';

if (!$is_logged_in) {
    header("Location: " . $base_url . "/auth/login.php");
    exit();
}

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../functions/user_functions.php';

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($user_id <= 0) {
    $_SESSION['error'] = "Invalid user ID.";
    header("Location: manage_users.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    
    if (updateUser($conn, $user_id, $username)) {
        $_SESSION['message'] = "Username updated successfully!";
        header("Location: manage_users.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to update username. It may already be in use.";
        header("Location: edit.php?id=" . $user_id);
        exit();
    }
}

$user = getUserById($conn, $user_id);
if (!$user) {
    $_SESSION['error'] = "User not found.";
    header("Location: manage_users.php");
    exit();
}
?>

<div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-lg">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit User</h1>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm" role="alert">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="edit.php?id=<?= htmlspecialchars($user['id']); ?>" class="space-y-5">
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <input type="text" name="username" id="username" required 
                   value="<?= htmlspecialchars($user['username']); ?>"
                   class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email (Cannot be changed)</label>
            <input type="email" name="email" id="email" 
                   value="<?= htmlspecialchars($user['email']); ?>"
                   class="w-full mt-1 px-4 py-2 border border-gray-200 rounded-lg bg-gray-100 cursor-not-allowed"
                   disabled>
        </div>

        <div class="flex items-center justify-end space-x-4 pt-4">
            <a href="manage_users.php" class="text-gray-600 hover:underline">Cancel</a>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                Save Changes
            </button>
        </div>
    </form>
</div>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>