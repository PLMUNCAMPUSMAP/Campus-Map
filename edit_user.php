<?php
session_start();
include 'db.php';

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo "Access denied.";
    exit();
}

if (!isset($_GET['edit'])) {
    echo "Invalid request.";
    exit();
}

$user_id = intval($_GET['edit']);

// Handle update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE user_id = ?");
    $stmt->bind_param("ssi", $username, $role, $user_id);
    $stmt->execute();
    echo "User updated.<br><a href='admin.php'></a>";
    exit();
}

// Fetch user data
$stmt = $conn->prepare("SELECT username, role FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $role);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: monospace;
            background-color: #f4f4f4;
            color: #333;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        form {
            max-width: 500px;
            margin: 0 auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        input, select, button {
            width: calc(100% - 24px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: monospace;
        }
        button {
            background: #333;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #555;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #dc3545;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        label {
            display: block;
            margin-top: 10px;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>

<h2>Edit User</h2>
<form method="POST">
    <label>Username:</label>
    <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>

    <label>Role:</label>
    <select name="role">
        <option value="user" <?php if ($role == 'user') echo 'selected'; ?>>User</option>
        <option value="admin" <?php if ($role == 'admin') echo 'selected'; ?>>Admin</option>
    </select>

    <button type="submit">Save Changes</button>
</form>

<a href="admin.php">Cancel</a>

</body>
</html>
