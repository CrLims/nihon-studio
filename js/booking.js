/* ============================================
   booking.js — Booking Page Logic
   Nihon Studio

   Berisi:
   1. Navigasi antar step (Next / Back)
   2. Validasi tiap step
   3. Kalender interaktif
   4. Isi otomatis Booking Summary
   5. Handle Confirm
   ============================================ */

document.addEventListener('DOMContentLoaded', function () {

  // ============================================
  // STATE — Simpan data booking user
  // ============================================
  const bookingData = {
    name: '', email: '', phone: '', people: '', notes: '',
    location: '', locAddress: '', locMaps: '',
    service: '',
    date: '', time: '',
    addons: ''
  };

  // Harga dasar per service (placeholder)
  const servicePrices = {
    'Selfie':     150000,
    'Photopass':  250000,
    'Event':      500000
  };

  // Harga add-ons
  const addonPrices = {
    '': 0,
    'Printed Photos (+Rp 50.000)':  50000,
    'Extra Editing (+Rp 75.000)':   75000,
    'Photo Album (+Rp 120.000)':   120000
  };

  // ============================================
  // HELPER: Format Rupiah
  // ============================================
  function formatRupiah(amount) {
    return 'Rp ' + amount.toLocaleString('id-ID');
  }

  // ============================================
  // HELPER: Pindah step
  // ============================================
  let currentStep = 1;
  const totalSteps = 5; // Tab hanya sampai 5, step 6 adalah thank you

  function goToStep(stepNum) {
    // Sembunyikan semua step
    document.querySelectorAll('.booking-step').forEach(function (s) {
      s.classList.remove('active');
    });

    // Tampilkan step yang dipilih
    document.getElementById('booking-step-' + stepNum).classList.add('active');

    // Update tab indicator
    document.querySelectorAll('.booking-tab').forEach(function (tab, i) {
      tab.classList.remove('active', 'done');
      const tabNum = i + 1;
      if (tabNum === stepNum) {
        tab.classList.add('active');
      } else if (tabNum < stepNum) {
        tab.classList.add('done');
      }
    });

    currentStep = stepNum;

    // Scroll ke atas booking card
    document.querySelector('.booking-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  // ============================================
  // HELPER: Validasi input tidak kosong
  // ============================================
  function isEmpty(val) {
    return !val || !val.trim();
  }

  function highlightError(el) {
    el.style.borderColor = '#e74c3c';
    el.focus();
    setTimeout(function () { el.style.borderColor = ''; }, 2000);
  }

  // ============================================
  // STEP 1 → 2: Validasi informasi
  // ============================================
  document.getElementById('step1-next').addEventListener('click', function () {
    const name  = document.getElementById('b-name');
    const email = document.getElementById('b-email');
    const phone = document.getElementById('b-phone');
    const ppl   = document.getElementById('b-people');

    if (isEmpty(name.value))  { highlightError(name);  return; }
    if (isEmpty(email.value)) { highlightError(email); return; }
    if (isEmpty(phone.value)) { highlightError(phone); return; }
    if (isEmpty(ppl.value))   { highlightError(ppl);   return; }

    // Simpan data
    bookingData.name   = name.value.trim();
    bookingData.email  = email.value.trim();
    bookingData.phone  = phone.value.trim();
    bookingData.people = ppl.value.trim();
    bookingData.notes  = document.getElementById('b-notes').value.trim();

    goToStep(2);
  });

  // ============================================
  // STEP 2: Back & Next (Location)
  // ============================================
  document.getElementById('step2-back').addEventListener('click', function () {
    goToStep(1);
  });

document.getElementById('step2-next').addEventListener('click', function () {
    const selected = document.querySelector('input[name="location"]:checked');

    if (!selected) {
      alert('Silakan pilih lokasi terlebih dahulu.');
      return;
    }

    bookingData.location = selected.value;

    if (selected.value === 'Outdoor') {
      const addr = document.getElementById('outdoor-address').value.trim();
      const maps = document.getElementById('outdoor-maps').value.trim();
      if (!addr) { alert('Silakan isi alamat lokasi Outdoor.'); return; }
      if (!maps) { alert('Silakan isi Google Maps link Outdoor.'); return; }
      bookingData.locAddress = addr;
      bookingData.locMaps    = maps;
    } else if (selected.value === 'Home Service') {
      const addr = document.getElementById('home-address').value.trim();
      const maps = document.getElementById('home-maps').value.trim();
      if (!addr) { alert('Silakan isi alamat lokasi Home Service.'); return; }
      if (!maps) { alert('Silakan isi Google Maps link Home Service.'); return; }
      bookingData.locAddress = addr;
      bookingData.locMaps    = maps;
    } else {
      bookingData.locAddress = '';
      bookingData.locMaps    = '';
    }

    goToStep(3);
  });

  // ============================================
  // STEP 3: Back & Next (Service)
  // ============================================
  document.getElementById('step3-back').addEventListener('click', function () {
    goToStep(2);
  });

  document.getElementById('step3-next').addEventListener('click', function () {
    const selected = document.querySelector('input[name="service"]:checked');

    if (!selected) {
      alert('Silakan pilih service terlebih dahulu.');
      return;
    }

    bookingData.service = selected.value;
    goToStep(4);
  });

  // ============================================
  // STEP 4: Kalender & Waktu
  // ============================================

  // --- State kalender ---
  const today = new Date();
  let calYear  = today.getFullYear();
  let calMonth = today.getMonth(); // 0-indexed
  let selectedDate = null;

  const monthNames = [
    'January','February','March','April','May','June',
    'July','August','September','October','November','December'
  ];

  function renderCalendar() {
    const grid = document.querySelector('.calendar-grid');

    // Hapus semua hari (tapi biarkan header nama hari)
    const dayNames = grid.querySelectorAll('.cal-day-name');
    grid.innerHTML = '';
    dayNames.forEach(function (d) { grid.appendChild(d); });

    // Update teks bulan-tahun
    document.getElementById('cal-month-year').textContent =
      monthNames[calMonth] + ' ' + calYear;

    // Hari pertama bulan ini (0=Sun, 1=Mon, ...)
    const firstDay = new Date(calYear, calMonth, 1).getDay();
    // Konversi ke Monday-first (0=Mon ... 6=Sun)
    const startOffset = (firstDay === 0) ? 6 : firstDay - 1;

    // Jumlah hari dalam bulan
    const daysInMonth = new Date(calYear, calMonth + 1, 0).getDate();

    // Isi empty cell sebelum hari pertama
    for (let i = 0; i < startOffset; i++) {
      const empty = document.createElement('div');
      empty.classList.add('cal-day', 'empty');
      grid.appendChild(empty);
    }

    // Isi tanggal
    for (let d = 1; d <= daysInMonth; d++) {
      const cell = document.createElement('div');
      cell.classList.add('cal-day');
      cell.textContent = d;

      // Tandai hari ini
      if (d === today.getDate() && calMonth === today.getMonth() && calYear === today.getFullYear()) {
        cell.classList.add('today');
      }

      // Tandai tanggal yang dipilih
      if (selectedDate &&
          selectedDate.d === d &&
          selectedDate.m === calMonth &&
          selectedDate.y === calYear) {
        cell.classList.add('selected');
      }

      // Klik tanggal
      cell.addEventListener('click', function () {
        selectedDate = { d: d, m: calMonth, y: calYear };
        renderCalendar(); // Re-render untuk update selected
      });

      grid.appendChild(cell);
    }
  }

  // Tombol prev/next bulan
  document.getElementById('cal-prev').addEventListener('click', function () {
    calMonth--;
    if (calMonth < 0) { calMonth = 11; calYear--; }
    renderCalendar();
  });

  document.getElementById('cal-next').addEventListener('click', function () {
    calMonth++;
    if (calMonth > 11) { calMonth = 0; calYear++; }
    renderCalendar();
  });

  // Render pertama kali
  renderCalendar();

  // --- Back & Next step 4 ---
  document.getElementById('step4-back').addEventListener('click', function () {
    goToStep(3);
  });

  document.getElementById('step4-next').addEventListener('click', function () {
    if (!selectedDate) {
      alert('Silakan pilih tanggal terlebih dahulu.');
      return;
    }

    const timeInput = document.getElementById('b-time');
    if (!timeInput.value) {
      highlightError(timeInput);
      alert('Silakan pilih waktu sesi.');
      return;
    }

    // Format tanggal: "Monday, 15-01-2025"
    const dateObj  = new Date(selectedDate.y, selectedDate.m, selectedDate.d);
    const dayNames2 = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    const dayName  = dayNames2[dateObj.getDay()];
    const dd = String(selectedDate.d).padStart(2, '0');
    const mm = String(selectedDate.m + 1).padStart(2, '0');
    const yyyy = selectedDate.y;

    bookingData.date = dayName + ', ' + dd + '-' + mm + '-' + yyyy;
    bookingData.time = timeInput.value;

    fillSummary();
    goToStep(5);
  });

  // ============================================
  // STEP 5: Isi Summary & Hitung Harga
  // ============================================
  function fillSummary() {
    document.getElementById('sum-name').textContent   = bookingData.name;
    document.getElementById('sum-email').textContent  = bookingData.email;
    document.getElementById('sum-phone').textContent  = bookingData.phone;
    document.getElementById('sum-people').textContent = bookingData.people;

    document.getElementById('sum-location').textContent   = bookingData.location;
    document.getElementById('sum-loc-address').textContent = bookingData.locAddress || '-';
    document.getElementById('sum-loc-maps').textContent   = bookingData.locMaps    || '-';

    document.getElementById('sum-service').textContent = bookingData.service;
    document.getElementById('sum-date').textContent    = bookingData.date;
    document.getElementById('sum-time').textContent    = bookingData.time;

    // Pre-fill notes
    document.getElementById('sum-notes-edit').value = bookingData.notes;

    updateTotal();
  }

  function updateTotal() {
    const basePrice  = servicePrices[bookingData.service] || 0;
    const addonVal   = document.getElementById('sum-addons').value;
    const addonPrice = addonPrices[addonVal] || 0;
    const total      = basePrice + addonPrice;
    document.getElementById('sum-total').textContent = formatRupiah(total);
  }

  // Update harga saat add-on berubah
  document.getElementById('sum-addons').addEventListener('change', updateTotal);

  // Back step 5
  document.getElementById('step5-back').addEventListener('click', function () {
    goToStep(4);
  });

  // ============================================
  // STEP 5 → 6: Confirm
  // ============================================
  document.getElementById('step5-confirm').addEventListener('click', function () {
    // Simpan add-ons & notes final
    bookingData.addons = document.getElementById('sum-addons').value;
    bookingData.notes  = document.getElementById('sum-notes-edit').value;

    // Sembunyikan semua tab (step 6 = thank you, tidak punya tab aktif)
    document.querySelectorAll('.booking-tab').forEach(function (tab) {
      tab.classList.remove('active');
      tab.classList.add('done');
    });

    // Tampilkan step thank you
    document.querySelectorAll('.booking-step').forEach(function (s) {
      s.classList.remove('active');
    });
    document.getElementById('booking-step-6').classList.add('active');

    currentStep = 6;
  });

  // ============================================
  // STEP 6: View Booking (tampilkan ulang summary)
  // ============================================
  document.getElementById('view-booking').addEventListener('click', function () {
    goToStep(5);
  });

});
