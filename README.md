# 📁 Nihon Studio — Panduan Project

## Struktur Folder

```
nihon-studio/
│
├── index.html              ← Halaman utama (single page)
├── booking.html            ← Halaman booking (belum dibuat)
│
├── assets/
│   └── img/               ← 📌 LETAKKAN SEMUA GAMBAR DI SINI
│
├── css/
│   ├── style.css           ← Warna & font global
│   ├── navbar.css          ← Navbar
│   ├── home.css            ← Section Hero & About
│   ├── portfolio.css       ← Section Portfolio & Testimonies
│   ├── services.css        ← Section Services (siap diisi)
│   ├── location.css        ← Section Location
│   └── contact.css         ← Section Contact & Footer
│
└── js/
    ├── main.js             ← Inisialisasi & scroll behavior
    ├── navbar.js           ← Hamburger menu
    ├── animation.js        ← Animasi scroll reveal & ripple
    └── contact.js          ← Logic form multi-step
```

---

## 🖼️ Cara Memasang Gambar

### Langkah 1 — Simpan gambar ke folder yang benar
Letakkan semua file gambar kamu di dalam folder:
```
assets/img/
```

### Langkah 2 — Ganti nama file di HTML

Buka `index.html`, cari komentar seperti ini:
```html
<!-- Ganti src dengan path logo kamu, contoh: assets/img/logo.png -->
<img src="assets/img/logo.png" alt="Nihon Studio Logo" />
```

Ganti `logo.png` dengan nama file gambar kamu yang sebenarnya.

### Daftar Gambar yang Perlu Diganti

| Variabel di HTML             | Keterangan                        |
|------------------------------|-----------------------------------|
| `assets/img/logo.png`        | Logo Nihon Studio (navbar & footer) |
| `assets/img/hero-bg.jpg`     | Foto/gambar background hero       |
| `assets/img/hero-character.png` | Karakter anime di section hero |
| `assets/img/character-point.png` | Karakter yang menunjuk di section portfolio |
| `assets/img/portfolio-selfie.jpg` | Foto portofolio Selfie       |
| `assets/img/portfolio-photopass.jpg` | Foto portofolio Photopass |
| `assets/img/portfolio-3.jpg` | Foto portofolio ke-3              |
| `assets/img/portfolio-4.jpg` | Foto portofolio ke-4              |
| `assets/img/footer-character.png` | Karakter di footer           |

### Tips Penamaan File
- Gunakan huruf kecil semua: `hero-bg.jpg` ✅ bukan `Hero BG.jpg` ❌
- Tidak ada spasi: `footer-character.png` ✅ bukan `footer character.png` ❌
- Format gambar yang disarankan:
  - Foto: `.jpg` atau `.webp`
  - Gambar dengan background transparan (karakter): `.png`
  - Icon/logo: `.png` atau `.svg`

---

## 🗺️ Cara Ganti Google Maps

Di `index.html`, cari bagian `<iframe>` di section Location.
Ganti atribut `src` dengan embed URL dari Google Maps lokasi kamu:

1. Buka [maps.google.com](https://maps.google.com)
2. Cari lokasi studio kamu
3. Klik **Share** → **Embed a map**
4. Copy kode `src="..."` dari iframe tersebut
5. Paste ke dalam `index.html`

---

## 🎨 Cara Ganti Warna Tema

Buka `css/style.css`, ubah nilai variabel di bagian `:root`:

```css
:root {
  --color-primary: #F28C00;       /* Oranye utama → ganti di sini */
  --color-primary-light: #FFB347; /* Oranye muda */
  --color-bg: #FFFDF7;            /* Background putih hangat */
}
```

---

## 📱 Responsif

Website ini sudah responsive untuk:
- ✅ Desktop (1200px+)
- ✅ Tablet (768px - 1199px)
- ✅ Mobile / Android / iOS (< 768px)
