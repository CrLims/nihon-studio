/* ============================================
   contact.js — Multi-Step Contact Form
   Nihon Studio

   Berisi:
   1. Logic navigasi antar step (Next / Back)
   2. Validasi input sederhana
   3. Tampilkan ringkasan di step terakhir
   4. Handle submit
   ============================================ */

document.addEventListener('DOMContentLoaded', function () {

  // ============================================
  // AMBIL ELEMEN-ELEMEN YANG DIBUTUHKAN
  // ============================================

  // Step panels
  const step1 = document.getElementById('step-1');
  const step2 = document.getElementById('step-2');
  const step3 = document.getElementById('step-3');

  // Tombol navigasi
  const btnNext1  = document.getElementById('btn-next-1');
  const btnBack2  = document.getElementById('btn-back-2');
  const btnNext2  = document.getElementById('btn-next-2');
  const btnBack3  = document.getElementById('btn-back-3');
  const btnSubmit = document.getElementById('btn-submit');

  // Input fields step 1
  const inputName  = document.getElementById('name');
  const inputEmail = document.getElementById('email');
  const inputPhone = document.getElementById('phone');

  // Input fields step 2
  const inputQuestion = document.getElementById('question');

  // Summary div di step 3
  const formSummary = document.getElementById('form-summary');

  // Step indicator elements
  const steps     = document.querySelectorAll('.step');
  const stepLines = document.querySelectorAll('.step-line');

  // ============================================
  // HELPER: Update tampilan step indicator
  // ============================================
  function setActiveStep(stepNumber) {
    // stepNumber: 1, 2, atau 3

    steps.forEach(function (step, index) {
      step.classList.remove('active', 'done');

      if (index + 1 === stepNumber) {
        step.classList.add('active');
      } else if (index + 1 < stepNumber) {
        step.classList.add('done');
      }
    });

    stepLines.forEach(function (line, index) {
      line.classList.remove('done');
      if (index + 1 < stepNumber) {
        line.classList.add('done');
      }
    });
  }

  // ============================================
  // HELPER: Tampilkan step tertentu
  // ============================================
  function showStep(stepEl) {
    // Sembunyikan semua step
    [step1, step2, step3].forEach(function (s) {
      s.classList.remove('active');
    });

    // Tampilkan step yang dipilih
    stepEl.classList.add('active');
  }

  // ============================================
  // HELPER: Validasi input tidak boleh kosong
  // ============================================
  function validate(input, message) {
    if (!input.value.trim()) {
      // Highlight input yang kosong
      input.style.borderColor = '#e74c3c';
      input.focus();

      // Kembalikan warna border setelah user mulai mengetik
      input.addEventListener('input', function () {
        input.style.borderColor = '';
      }, { once: true });

      alert(message);
      return false;
    }
    return true;
  }

  // ============================================
  // STEP 1 → STEP 2: Tombol "Next"
  // ============================================
  btnNext1.addEventListener('click', function () {
    // Validasi semua field step 1
    if (!validate(inputName,  'Nama tidak boleh kosong.'))  return;
    if (!validate(inputEmail, 'Email tidak boleh kosong.')) return;
    if (!validate(inputPhone, 'Nomor telepon tidak boleh kosong.')) return;

    showStep(step2);
    setActiveStep(2);
    window.scrollTo({ top: document.getElementById('contact').offsetTop - 80, behavior: 'smooth' });
  });

  // ============================================
  // STEP 2 → STEP 1: Tombol "Back"
  // ============================================
  btnBack2.addEventListener('click', function () {
    showStep(step1);
    setActiveStep(1);
  });

  // ============================================
  // STEP 2 → STEP 3: Tombol "Next"
  // ============================================
  btnNext2.addEventListener('click', function () {
    if (!validate(inputQuestion, 'Pertanyaan tidak boleh kosong.')) return;

    // Tampilkan ringkasan data
    formSummary.innerHTML = `
      <p><strong>Nama:</strong> <span>${inputName.value}</span></p>
      <p><strong>Email:</strong> <span>${inputEmail.value}</span></p>
      <p><strong>Telepon:</strong> <span>${inputPhone.value}</span></p>
      <p style="margin-top:12px;"><strong>Pertanyaan:</strong></p>
      <p style="color: var(--color-text-muted); margin-top:4px;">${inputQuestion.value}</p>
    `;

    showStep(step3);
    setActiveStep(3);
    window.scrollTo({ top: document.getElementById('contact').offsetTop - 80, behavior: 'smooth' });
  });

  // ============================================
  // STEP 3 → STEP 2: Tombol "Back"
  // ============================================
  btnBack3.addEventListener('click', function () {
    showStep(step2);
    setActiveStep(2);
  });

  // ============================================
  // SUBMIT FORM
  // ============================================
  btnSubmit.addEventListener('click', function () {
    // Di sini kamu bisa hubungkan ke backend / email service
    // Untuk sekarang, tampilkan pesan sukses

    formSummary.innerHTML = `
      <div style="text-align:center; padding: 20px 0;">
        <div style="font-size: 3rem; margin-bottom: 12px;">✅</div>
        <h3 style="color: var(--color-primary); font-family: var(--font-heading); margin-bottom:8px;">
          Pesan Terkirim!
        </h3>
        <p style="color: var(--color-text-muted);">
          Terima kasih, ${inputName.value}! Kami akan menghubungi kamu segera.
        </p>
      </div>
    `;

    // Sembunyikan tombol setelah submit
    document.querySelector('.form-buttons').style.display = 'none';

    // Reset form setelah 4 detik (opsional)
    setTimeout(function () {
      inputName.value     = '';
      inputEmail.value    = '';
      inputPhone.value    = '';
      inputQuestion.value = '';

      showStep(step1);
      setActiveStep(1);
      document.querySelector('.form-buttons').style.display = '';
    }, 4000);
  });

});
