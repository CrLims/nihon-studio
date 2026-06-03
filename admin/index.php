<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login — Nihon Studio</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="admin.css" />
</head>
<body class="login-page">

  <div class="login-wrapper">
    <div class="login-card">

      <!-- Logo & Judul -->
      <div class="login-header">
        <img src="../assets/img/logo.png" alt="Nihon Studio" class="login-logo" />
        <h1>NIHON STUDIO</h1>
        <p>Admin Dashboard</p>
      </div>

      <!-- Pesan error (jika ada) -->
      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">
          Username atau password salah. Coba lagi.
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['logout'])): ?>
        <div class="alert alert-success">
          Kamu berhasil logout.
        </div>
      <?php endif; ?>

      <!-- Form Login -->
      <form action="auth.php" method="POST" class="login-form">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" placeholder="Masukkan username" required autofocus />
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Masukkan password" required />
        </div>
        <button type="submit" class="btn-login">Masuk</button>
      </form>

      <a href="../index.html" class="back-link">← Kembali ke Website</a>

    </div>
  </div>

</body>
</html>
