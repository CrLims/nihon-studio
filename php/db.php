<?php
// ============================================
// db.php — Koneksi ke Database MySQL
// Nihon Studio
// ============================================

$host     = "127.0.0.1";
$port     = "3307";        // Port MySQL XAMPP kamu (3307)
$dbname   = "nihon_studio";
$username = "root";
$password = "";            // Default XAMPP tidak ada password

try {
  $pdo = new PDO(
    "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
    $username,
    $password,
    [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
  );
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode([
    "success" => false,
    "message" => "Koneksi database gagal: " . $e->getMessage()
  ]);
  exit;
}
?>
