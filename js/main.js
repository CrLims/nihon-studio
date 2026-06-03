/* ============================================
   main.js — Global Initialization
   Nihon Studio

   File ini dijalankan pertama.
   Berisi: inisialisasi awal, active nav link saat scroll
   ============================================ */

document.addEventListener('DOMContentLoaded', function () {

  // ============================================
  // 1. UPDATE NAV LINK AKTIF SAAT SCROLL
  //    Saat user scroll ke section tertentu,
  //    link navbar yang sesuai akan di-highlight
  // ============================================

  const sections = document.querySelectorAll('section[id]');
  const navLinks = document.querySelectorAll('.nav-link');

  function updateActiveNav() {
    // Posisi scroll saat ini (+ offset navbar)
    const scrollY = window.scrollY + 100;

    sections.forEach(function (section) {
      const sectionTop    = section.offsetTop;
      const sectionHeight = section.offsetHeight;
      const sectionId     = section.getAttribute('id');

      // Cek apakah scroll berada di dalam section ini
      if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
        // Hapus class active dari semua nav link
        navLinks.forEach(function (link) {
          link.classList.remove('active');
        });

        // Tambah class active ke link yang sesuai
        const matchingLink = document.querySelector('.nav-link[href="#' + sectionId + '"]');
        if (matchingLink) {
          matchingLink.classList.add('active');
        }
      }
    });
  }

  // Jalankan saat scroll
  window.addEventListener('scroll', updateActiveNav);

  // Jalankan sekali saat halaman pertama dibuka
  updateActiveNav();


  // ============================================
  // 2. SMOOTH SCROLL untuk nav link
  //    (backup jika CSS scroll-behavior tidak bekerja)
  // ============================================

  navLinks.forEach(function (link) {
    link.addEventListener('click', function (e) {
      const href = this.getAttribute('href');

      // Hanya proses link internal (#section)
      if (href && href.startsWith('#')) {
        e.preventDefault();
        const targetId = href.substring(1);
        const targetEl = document.getElementById(targetId);

        if (targetEl) {
          const navbarHeight = document.getElementById('navbar').offsetHeight;
          const targetTop = targetEl.offsetTop - navbarHeight;

          window.scrollTo({
            top: targetTop,
            behavior: 'smooth'
          });
        }
      }
    });
  });

});
