<?php
// ============================================
// save_booking.php — Simpan Data Booking
// Nihon Studio
//
// Menerima data JSON dari booking.js
// lalu menyimpannya ke tabel MySQL
// ============================================

// Izinkan request dari localhost (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Hanya terima method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(["success" => false, "message" => "Method tidak diizinkan."]);
  exit;
}

// Ambil data JSON dari request
$raw  = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data) {
  http_response_code(400);
  echo json_encode(["success" => false, "message" => "Data tidak valid."]);
  exit;
}

// Koneksi database
require_once "db.php";

// ============================================
// 1. Simpan atau cari Customer
// ============================================
try {
  // Cek apakah email sudah terdaftar
  $stmt = $pdo->prepare("SELECT customer_id FROM customer WHERE email = ?");
  $stmt->execute([$data['email']]);
  $customer = $stmt->fetch();

  if ($customer) {
    // Sudah ada — pakai customer_id yang ada
    $customerId = $customer['customer_id'];
  } else {
    // Belum ada — insert customer baru
    $stmt = $pdo->prepare("
      INSERT INTO customer (full_name, email, phone)
      VALUES (?, ?, ?)
    ");
    $stmt->execute([
      $data['name'],
      $data['email'],
      $data['phone']
    ]);
    $customerId = $pdo->lastInsertId();
  }
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Gagal menyimpan data pelanggan: " . $e->getMessage()]);
  exit;
}

// ============================================
// 2. Cari service_id berdasarkan nama service
// ============================================
try {
  $stmt = $pdo->prepare("SELECT service_id FROM service WHERE service_name = ?");
  $stmt->execute([$data['service']]);
  $service = $stmt->fetch();

  if (!$service) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Layanan tidak ditemukan."]);
    exit;
  }
  $serviceId = $service['service_id'];
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Gagal mencari layanan: " . $e->getMessage()]);
  exit;
}

// ============================================
// 3. Simpan atau cari Location
// ============================================
try {
  $locType    = $data['location'];           // 'Studio', 'Outdoor', 'Home Service'
  $locAddress = $data['locAddress'] ?? null;
  $locMaps    = $data['locMaps']    ?? null;

  if ($locType === 'Studio') {
    // Studio selalu sama, ambil yang sudah ada
    $stmt = $pdo->prepare("SELECT location_id FROM location WHERE location_type = 'Studio' LIMIT 1");
    $stmt->execute();
    $loc = $stmt->fetch();
    $locationId = $loc ? $loc['location_id'] : null;
  } else {
    // Outdoor / Home Service — insert baru karena alamat bisa berbeda
    $stmt = $pdo->prepare("
      INSERT INTO location (location_type, address, maps_link)
      VALUES (?, ?, ?)
    ");
    $stmt->execute([$locType, $locAddress, $locMaps]);
    $locationId = $pdo->lastInsertId();
  }

  if (!$locationId) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Gagal menentukan lokasi."]);
    exit;
  }
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Gagal menyimpan lokasi: " . $e->getMessage()]);
  exit;
}

// ============================================
// 4. Cari addon_id (opsional)
// ============================================
$addonId = null;
if (!empty($data['addons'])) {
  try {
    // Ambil nama addon saja (hapus bagian harga "+Rp...")
    $addonName = explode(' (+', $data['addons'])[0];
    $stmt = $pdo->prepare("SELECT addon_id FROM addon WHERE addon_name = ?");
    $stmt->execute([$addonName]);
    $addon   = $stmt->fetch();
    $addonId = $addon ? $addon['addon_id'] : null;
  } catch (PDOException $e) {
    $addonId = null; // Addon opsional, tidak perlu error
  }
}

// ============================================
// 5. Simpan Booking
// ============================================
try {
  // Format tanggal dari "Monday, 15-06-2025" → "2025-06-15"
  $dateParts   = explode(', ', $data['date']);
  $dateFormatted = null;
  if (count($dateParts) === 2) {
    $dmY = explode('-', $dateParts[1]);
    if (count($dmY) === 3) {
      $dateFormatted = $dmY[2] . '-' . $dmY[1] . '-' . $dmY[0];
    }
  }

  if (!$dateFormatted) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Format tanggal tidak valid."]);
    exit;
  }

  $stmt = $pdo->prepare("
    INSERT INTO booking
      (customer_id, service_id, location_id, addon_id,
       booking_date, booking_time, group_size, notes, total_price)
    VALUES
      (?, ?, ?, ?, ?, ?, ?, ?, ?)
  ");

  $stmt->execute([
    $customerId,
    $serviceId,
    $locationId,
    $addonId,
    $dateFormatted,
    $data['time'],
    (int) $data['people'],
    $data['notes'] ?? null,
    (float) str_replace(['Rp ', '.'], ['', ''], $data['totalPrice'])
  ]);

  $bookingId = $pdo->lastInsertId();

  // Berhasil!
  echo json_encode([
    "success"    => true,
    "message"    => "Pemesanan berhasil disimpan!",
    "booking_id" => $bookingId
  ]);

} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Gagal menyimpan pemesanan: " . $e->getMessage()]);
}
?>
