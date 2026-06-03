<?php
// ============================================
// auth.php — Proses Login Admin
// Nihon Studio
// ============================================

session_start();

require_once "../php/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: index.php");
  exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($password)) {
  header("Location: index.php?error=1");
  exit;
}

try {
  $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
  $stmt->execute([$username]);
  $admin = $stmt->fetch();

  if ($admin && password_verify($password, $admin['password'])) {
    // Login berhasil
    $_SESSION['admin_id']   = $admin['admin_id'];
    $_SESSION['admin_user'] = $admin['username'];
    header("Location: dashboard.php");
    exit;
  } else {
    // Login gagal
    header("Location: index.php?error=1");
    exit;
  }
} catch (PDOException $e) {
  header("Location: index.php?error=1");
  exit;
}
?>
