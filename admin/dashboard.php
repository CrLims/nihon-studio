<?php
// ============================================
// dashboard.php — Halaman Utama Admin
// Nihon Studio
// ============================================

session_start();

// Cek apakah sudah login
if (!isset($_SESSION['admin_id'])) {
  header("Location: index.php");
  exit;
}

require_once "../php/db.php";

// Ambil pesan sukses jika ada
$successMsg = $_GET['success'] ?? '';
$errorMsg   = $_GET['error']   ?? '';

// Ambil semua booking dengan JOIN
try {
  $stmt = $pdo->query("
    SELECT
      b.booking_id,
      c.full_name     AS nama_pelanggan,
      c.email,
      c.phone,
      s.service_name  AS layanan,
      l.location_type AS lokasi,
      l.address       AS alamat,
      a.addon_name    AS tambahan,
      b.booking_date,
      b.booking_time,
      b.group_size,
      b.notes,
      b.status,
      b.total_price,
      b.created_at
    FROM booking b
    JOIN customer  c ON b.customer_id  = c.customer_id
    JOIN service   s ON b.service_id   = s.service_id
    JOIN location  l ON b.location_id  = l.location_id
    LEFT JOIN addon a ON b.addon_id    = a.addon_id
    ORDER BY b.created_at DESC
  ");
  $bookings = $stmt->fetchAll();
} catch (PDOException $e) {
  $bookings = [];
}

// Hitung statistik
$totalBooking    = count($bookings);
$totalPending    = count(array_filter($bookings, fn($b) => $b['status'] === 'Pending'));
$totalConfirmed  = count(array_filter($bookings, fn($b) => $b['status'] === 'Confirmed'));
$totalCancelled  = count(array_filter($bookings, fn($b) => $b['status'] === 'Cancelled'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Admin — Nihon Studio</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="admin.css" />
</head>
<body class="dashboard-page">

  <!-- ========== SIDEBAR ========== -->
  <aside class="sidebar">
    <div class="sidebar-header">
      <img src="../assets/img/logo.png" alt="Logo" class="sidebar-logo" />
      <div>
        <p class="sidebar-title">NIHON STUDIO</p>
        <p class="sidebar-sub">Admin Panel</p>
      </div>
    </div>
    <nav class="sidebar-nav">
      <a href="dashboard.php" class="sidebar-link active">📋 Semua Booking</a>
      <a href="../index.html" class="sidebar-link" target="_blank">🌐 Lihat Website</a>
      <a href="logout.php" class="sidebar-link logout">🚪 Logout</a>
    </nav>
    <div class="sidebar-user">
      Logged in as: <strong><?= htmlspecialchars($_SESSION['admin_user']) ?></strong>
    </div>
  </aside>

  <!-- ========== MAIN CONTENT ========== -->
  <main class="main-content">

    <!-- Header -->
    <div class="dashboard-header">
      <h1>Dashboard Admin</h1>
      <p>Kelola semua pemesanan Nihon Studio</p>
    </div>

    <!-- Alert -->
    <?php if ($successMsg): ?>
      <div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div>
    <?php endif; ?>
    <?php if ($errorMsg): ?>
      <div class="alert alert-error"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>

    <!-- Statistik Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <p class="stat-num"><?= $totalBooking ?></p>
        <p class="stat-label">Total Booking</p>
      </div>
      <div class="stat-card pending">
        <p class="stat-num"><?= $totalPending ?></p>
        <p class="stat-label">Pending</p>
      </div>
      <div class="stat-card confirmed">
        <p class="stat-num"><?= $totalConfirmed ?></p>
        <p class="stat-label">Confirmed</p>
      </div>
      <div class="stat-card cancelled">
        <p class="stat-num"><?= $totalCancelled ?></p>
        <p class="stat-label">Cancelled</p>
      </div>
    </div>

    <!-- Tabel Booking -->
    <div class="table-wrapper">
      <div class="table-header">
        <h2>Daftar Pemesanan</h2>
        <!-- Filter Status -->
        <div class="filter-group">
          <label>Filter:</label>
          <select id="filterStatus" onchange="filterTable()">
            <option value="all">Semua</option>
            <option value="Pending">Pending</option>
            <option value="Confirmed">Confirmed</option>
            <option value="Cancelled">Cancelled</option>
          </select>
        </div>
      </div>

      <?php if (empty($bookings)): ?>
        <div class="empty-state">
          <p>📭 Belum ada data pemesanan.</p>
        </div>
      <?php else: ?>
      <div class="table-scroll">
        <table class="booking-table" id="bookingTable">
          <thead>
            <tr>
              <th>#</th>
              <th>Pelanggan</th>
              <th>Kontak</th>
              <th>Layanan</th>
              <th>Lokasi</th>
              <th>Tanggal & Jam</th>
              <th>Orang</th>
              <th>Tambahan</th>
              <th>Total</th>
              <th>Catatan</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($bookings as $b): ?>
            <tr data-status="<?= $b['status'] ?>">
              <td><?= $b['booking_id'] ?></td>
              <td>
                <strong><?= htmlspecialchars($b['nama_pelanggan']) ?></strong>
              </td>
              <td>
                <small><?= htmlspecialchars($b['email']) ?></small><br/>
                <small><?= htmlspecialchars($b['phone']) ?></small>
              </td>
              <td><?= htmlspecialchars($b['layanan']) ?></td>
              <td>
                <?= htmlspecialchars($b['lokasi']) ?>
                <?php if ($b['alamat']): ?>
                  <br/><small><?= htmlspecialchars($b['alamat']) ?></small>
                <?php endif; ?>
              </td>
              <td>
                <?= date('d M Y', strtotime($b['booking_date'])) ?>
                <br/><small><?= substr($b['booking_time'], 0, 5) ?> WIB</small>
              </td>
              <td><?= $b['group_size'] ?> orang</td>
              <td><?= $b['tambahan'] ? htmlspecialchars($b['tambahan']) : '-' ?></td>
              <td>Rp <?= number_format($b['total_price'], 0, ',', '.') ?></td>
              <td><small><?= $b['notes'] ? htmlspecialchars($b['notes']) : '-' ?></small></td>
              <td>
                <span class="badge badge-<?= strtolower($b['status']) ?>">
                  <?= $b['status'] ?>
                </span>
              </td>
              <td class="action-cell">
                <!-- Ubah Status -->
                <form action="admin_action.php" method="POST" style="display:inline">
                  <input type="hidden" name="action"     value="update_status" />
                  <input type="hidden" name="booking_id" value="<?= $b['booking_id'] ?>" />
                  <select name="status" onchange="this.form.submit()" class="status-select">
                    <option value="Pending"   <?= $b['status']==='Pending'   ? 'selected':'' ?>>Pending</option>
                    <option value="Confirmed" <?= $b['status']==='Confirmed' ? 'selected':'' ?>>Confirmed</option>
                    <option value="Cancelled" <?= $b['status']==='Cancelled' ? 'selected':'' ?>>Cancelled</option>
                  </select>
                </form>
                <!-- Hapus -->
                <form action="admin_action.php" method="POST" style="display:inline"
                      onsubmit="return confirm('Yakin ingin menghapus booking #<?= $b['booking_id'] ?>?')">
                  <input type="hidden" name="action"     value="delete" />
                  <input type="hidden" name="booking_id" value="<?= $b['booking_id'] ?>" />
                  <button type="submit" class="btn-delete">🗑️</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>

  </main>

  <script>
    // Filter tabel berdasarkan status
    function filterTable() {
      const filter = document.getElementById('filterStatus').value;
      const rows   = document.querySelectorAll('#bookingTable tbody tr');
      rows.forEach(function(row) {
        if (filter === 'all' || row.dataset.status === filter) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    // Auto-hide alert setelah 3 detik
    setTimeout(function() {
      document.querySelectorAll('.alert').forEach(function(el) {
        el.style.opacity = '0';
        setTimeout(function() { el.remove(); }, 400);
      });
    }, 3000);
  </script>

</body>
</html>
