/* ============================================
   animation.js — Animasi & Micro-interactions
   Nihon Studio

   Berisi:
   1. Scroll reveal — elemen muncul saat di-scroll
   2. Animasi tombol (sudah sebagian ditangani CSS,
      JS ini untuk efek tambahan)
   ============================================ */

document.addEventListener('DOMContentLoaded', function () {

  // ============================================
  // 1. SCROLL REVEAL
  //    Elemen dengan class .reveal akan muncul
  //    (fade + slide up) saat masuk viewport
  //
  //    Cara pakai: tambahkan class "reveal" ke
  //    elemen yang ingin muncul saat di-scroll.
  //    Contoh di HTML: <div class="portfolio-card reveal">
  // ============================================

  // Tambahkan class reveal secara otomatis ke elemen-elemen berikut
  const elementsToReveal = document.querySelectorAll(
    '.portfolio-card, .testi-card, .about-text, .location-info, .map-wrapper'
  );

  elementsToReveal.forEach(function (el) {
    el.classList.add('reveal');
  });

  // Tambahkan style CSS untuk animasi reveal langsung via JS
  // (agar tidak perlu tambahkan di CSS file terpisah)
  const revealStyle = document.createElement('style');
  revealStyle.textContent = `
    .reveal {
      opacity: 0;
      transform: translateY(32px);
      transition: opacity 0.6s ease, transform 0.6s ease;
    }
    .reveal.visible {
      opacity: 1;
      transform: translateY(0);
    }
  `;
  document.head.appendChild(revealStyle);

  // IntersectionObserver — deteksi elemen masuk viewport
  const observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');

        // Hentikan pengamatan setelah elemen terlihat
        // (agar animasi hanya terjadi sekali)
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.15,   // Trigger saat 15% elemen terlihat
    rootMargin: '0px 0px -40px 0px'  // Sedikit offset dari bawah
  });

  // Mulai amati semua elemen dengan class .reveal
  document.querySelectorAll('.reveal').forEach(function (el) {
    observer.observe(el);
  });


  // ============================================
  // 2. ANIMASI TOMBOL — Ripple Effect
  //    Saat tombol diklik, muncul efek gelombang
  // ============================================

  const buttons = document.querySelectorAll('.btn-hero, .btn-next, .btn-submit, .btn-book');

  buttons.forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      // Hapus ripple lama jika ada
      const existingRipple = btn.querySelector('.ripple');
      if (existingRipple) {
        existingRipple.remove();
      }

      // Buat elemen ripple baru
      const ripple = document.createElement('span');
      ripple.classList.add('ripple');

      // Hitung posisi klik relatif terhadap tombol
      const rect   = btn.getBoundingClientRect();
      const size   = Math.max(rect.width, rect.height);
      const x      = e.clientX - rect.left - size / 2;
      const y      = e.clientY - rect.top  - size / 2;

      ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
        background: rgba(255, 255, 255, 0.35);
        border-radius: 50%;
        transform: scale(0);
        animation: rippleAnim 0.5s ease-out forwards;
        pointer-events: none;
      `;

      // Tombol perlu position relative agar ripple terpositioning
      btn.style.position = 'relative';
      btn.style.overflow = 'hidden';

      btn.appendChild(ripple);

      // Hapus ripple setelah animasi selesai
      setTimeout(function () {
        ripple.remove();
      }, 600);
    });
  });

  // CSS animasi ripple
  const rippleStyle = document.createElement('style');
  rippleStyle.textContent = `
    @keyframes rippleAnim {
      to {
        transform: scale(2.5);
        opacity: 0;
      }
    }
  `;
  document.head.appendChild(rippleStyle);

});
