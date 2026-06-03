/* ============================================
   navbar.js — Navbar Behavior
   Nihon Studio

   Berisi:
   1. Hamburger menu toggle (mobile)
   2. Tambah shadow saat di-scroll (sticky effect)
   3. Tutup menu saat klik link (mobile)
   ============================================ */

document.addEventListener('DOMContentLoaded', function () {

  const navbar      = document.getElementById('navbar');
  const hamburger   = document.getElementById('hamburger');
  const navLinks    = document.getElementById('nav-links');

  // ============================================
  // 1. HAMBURGER MENU TOGGLE
  //    Klik tombol hamburger → buka/tutup menu
  // ============================================

  hamburger.addEventListener('click', function () {
    // Toggle class 'open' pada hamburger (animasi → X)
    hamburger.classList.toggle('open');

    // Toggle class 'open' pada nav-links (tampilkan/sembunyikan menu)
    navLinks.classList.toggle('open');
  });


  // ============================================
  // 2. TUTUP MENU SAAT KLIK SALAH SATU LINK
  //    Berguna di mobile setelah memilih menu
  // ============================================

  const allNavLinks = navLinks.querySelectorAll('a');

  allNavLinks.forEach(function (link) {
    link.addEventListener('click', function () {
      // Tutup hamburger menu
      hamburger.classList.remove('open');
      navLinks.classList.remove('open');
    });
  });


  // ============================================
  // 3. STICKY NAVBAR — tambah shadow saat scroll
  //    Saat user scroll ke bawah, navbar dapat shadow
  // ============================================

  window.addEventListener('scroll', function () {
    if (window.scrollY > 10) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });


  // ============================================
  // 4. TUTUP MENU SAAT KLIK DI LUAR MENU (mobile)
  // ============================================

  document.addEventListener('click', function (e) {
    const isClickInsideNav = navbar.contains(e.target);

    if (!isClickInsideNav && navLinks.classList.contains('open')) {
      hamburger.classList.remove('open');
      navLinks.classList.remove('open');
    }
  });

});
