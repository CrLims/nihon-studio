<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(["success" => false, "message" => "Method tidak diizinkan."]);
  exit;
}

$raw  = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data) {
  http_response_code(400);
  echo json_encode(["success" => false, "message" => "Data tidak valid.", "raw" => $raw]);
  exit;
}

require_once "db.php";

// 1. Simpan atau cari Customer
try {
  $stmt = $pdo->prepare("SELECT customer_id FROM customer WHERE email = ?");
  $stmt->execute([$data['email']]);
  $customer = $stmt->fetch();

  if ($customer) {
    $customerId = $customer['customer_id'];
  } else {
    $stmt = $pdo->prepare("INSERT INTO customer (full_name, email, phone) VALUES (?, ?, ?)");
    $stmt->execute([$data['name'], $data['email'], $data['phone']]);
    $customerId = $pdo->lastInsertId();
  }
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Gagal simpan customer: " . $e->getMessage()]);
  exit;
}

// 2. Cari service_id
try {
  $stmt = $pdo->prepare("SELECT service_id FROM service WHERE service_name = ?");
  $stmt->execute([$data['service']]);
  $service = $stmt->fetch();

  if (!$service) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Layanan tidak ditemukan: " . $data['service']]);
    exit;
  }
  $serviceId = $service['service_id'];
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Gagal cari layanan: " . $e->getMessage()]);
  exit;
}

// 3. Simpan atau cari Location
try {
  $locType    = $data['location'] ?? 'Studio';
  $locAddress = $data['locAddress'] ?? null;
  $locMaps    = $data['locMaps'] ?? null;

  if ($locType === 'Studio') {
    $stmt = $pdo->prepare("SELECT location_id FROM location WHERE location_type = 'Studio' LIMIT 1");
    $stmt->execute();
    $loc = $stmt->fetch();
    $locationId = $loc ? $loc['location_id'] : null;

    // Kalau Studio belum ada, buat baru
    if (!$locationId) {
      $stmt = $pdo->prepare("INSERT INTO location (location_type) VALUES ('Studio')");
      $stmt->execute();
      $locationId = $pdo->lastInsertId();
    }
  } else {
    $stmt = $pdo->prepare("INSERT INTO location (location_type, address, maps_link) VALUES (?, ?, ?)");
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
  echo json_encode(["success" => false, "message" => "Gagal simpan lokasi: " . $e->getMessage()]);
  exit;
}

// 4. Cari addon_id (opsional)
$addonId = null;
if (!empty($data['addons'])) {
  try {
    $addonName = explode(' (+', $data['addons'])[0];
    $stmt = $pdo->prepare("SELECT addon_id FROM addon WHERE addon_name = ?");
    $stmt->execute([$addonName]);
    $addon   = $stmt->fetch();
    $addonId = $addon ? $addon['addon_id'] : null;
  } catch (PDOException $e) {
    $addonId = null;
  }
}

// 5. Parse tanggal — fleksibel
$dateFormatted = null;
$rawDate = $data['date'] ?? '';

// Hapus nama hari jika ada: "Monday, 15-06-2025" → "15-06-2025"
if (strpos($rawDate, ', ') !== false) {
  $parts   = explode(', ', $rawDate);
  $rawDate = $parts[count($parts) - 1];
}
$rawDate = trim($rawDate);

// dd-mm-yyyy → yyyy-mm-dd
if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $rawDate, $m)) {
  $dateFormatted = $m[3] . '-' . $m[2] . '-' . $m[1];
}
// yyyy-mm-dd sudah benar
elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawDate)) {
  $dateFormatted = $rawDate;
}
// Fallback strtotime
else {
  $ts = strtotime($rawDate);
  if ($ts) $dateFormatted = date('Y-m-d', $ts);
}

if (!$dateFormatted) {
  http_response_code(400);
  echo json_encode(["success" => false, "message" => "Format tanggal tidak valid: " . $data['date']]);
  exit;
}

// 6. Simpan Booking
try {
  // Parse total price — hapus "Rp " dan titik pemisah ribuan
  $totalRaw   = $data['totalPrice'] ?? '0';
  $totalClean = preg_replace('/[^0-9]/', '', $totalRaw);
  $totalPrice = (float) $totalClean;

  $stmt = $pdo->prepare("
    INSERT INTO booking
      (customer_id, service_id, location_id, addon_id,
       booking_date, booking_time, group_size, notes, total_price)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
  ");

  $stmt->execute([
    $customerId,
    $serviceId,
    $locationId,
    $addonId,
    $dateFormatted,
    $data['time'],
    (int) ($data['people'] ?? 1),
    $data['notes'] ?? null,
    $totalPrice
  ]);

  $bookingId = $pdo->lastInsertId();

  echo json_encode([
    "success"    => true,
    "message"    => "Pemesanan berhasil disimpan!",
    "booking_id" => $bookingId
  ]);

} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Gagal simpan booking: " . $e->getMessage()]);
}
?>
