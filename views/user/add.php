<?php
$page_title = 'Add New User';
require_once __DIR__ . '/../../includes/header.php';

if (!$is_logged_in) {
    header("Location: " . $base_url . "/auth/login.php");
    exit();
}

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../functions/user_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
    } else {
        if (createUser($conn, $username, $email, $password)) {
            $_SESSION['message'] = "User created successfully!";
            header("Location: manage_users.php");
            exit();
        } else {
            $_SESSION['error'] = "Error creating user. The username or email may already be taken.";
        }
    }
    header("Location: add.php");
    exit();
}
?>

<div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-lg">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Add New User</h1>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm" role="alert">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="add.php" class="space-y-5">
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <input type="text" name="username" id="username" required class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email" required class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" name="password" id="password" required class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
        </div>

        <div>
            <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" required class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
        </div>

        <div class="flex items-center justify-end space-x-4">
            <a href="manage_users.php" class="text-gray-600 hover:underline">Cancel</a>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                Create User
            </button>
        </div>
    </form>
</div>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>