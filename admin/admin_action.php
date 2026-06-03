<?php
// ============================================
// admin_action.php — Aksi Admin
// Nihon Studio
// Menangani: update status & hapus booking
// ============================================

session_start();

if (!isset($_SESSION['admin_id'])) {
  header("Location: index.php");
  exit;
}

require_once "../php/db.php";

$action    = $_POST['action']    ?? '';
$bookingId = (int)($_POST['booking_id'] ?? 0);

if (!$bookingId) {
  header("Location: dashboard.php?error=ID+booking+tidak+valid.");
  exit;
}

// ── UPDATE STATUS ────────────────────────────
if ($action === 'update_status') {
  $status = $_POST['status'] ?? '';
  $allowed = ['Pending', 'Confirmed', 'Cancelled'];

  if (!in_array($status, $allowed)) {
    header("Location: dashboard.php?error=Status+tidak+valid.");
    exit;
  }

  try {
    $stmt = $pdo->prepare("UPDATE booking SET status = ? WHERE booking_id = ?");
    $stmt->execute([$status, $bookingId]);
    header("Location: dashboard.php?success=Status+booking+%23{$bookingId}+berhasil+diubah+menjadi+{$status}.");
  } catch (PDOException $e) {
    header("Location: dashboard.php?error=Gagal+mengubah+status.");
  }
  exit;
}

// ── DELETE BOOKING ───────────────────────────
if ($action === 'delete') {
  try {
    $stmt = $pdo->prepare("DELETE FROM booking WHERE booking_id = ?");
    $stmt->execute([$bookingId]);
    header("Location: dashboard.php?success=Booking+%23{$bookingId}+berhasil+dihapus.");
  } catch (PDOException $e) {
    header("Location: dashboard.php?error=Gagal+menghapus+booking.");
  }
  exit;
}

header("Location: dashboard.php");
exit;
?>
