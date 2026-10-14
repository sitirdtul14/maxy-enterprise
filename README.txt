MAXY CORPORATE LANDING PAGE (PHP)
- Semua teks/isi ada di data.php (Indonesia) dan data.en.php (Inggris). Edit di sana, halaman langsung berubah (tidak perlu sentuh index.php).
- Bahasa: tombol ID | EN di menu atas (?lang=id atau ?lang=en), pilihan diingat lewat cookie. data.en.php hanya berisi teks; nomor WA, sosmed, alamat, dan gambar diambil dari data.php. Jumlah item tiap daftar di data.en.php harus sama dengan data.php.
- Form "Adopsi AI..." menyimpan data ke ../maxy-storage/leads.csv (di luar web root; atur lewat storage_dir di data.php, cadangan ./storage), lalu membuka WhatsApp.
- Jalankan lokal: php -S localhost:8000 lalu buka http://localhost:8000
- Fitur: menu HP (hamburger), menu aktif sesuai posisi scroll, animasi muncul saat scroll, angka berhitung naik, logo klien berjalan otomatis.
- Gambar dummy: assets/ (ganti dengan nama file yang sama). Font Garet: taruh di assets/fonts/ (lihat LETAKKAN-FONT-GARET-DI-SINI.txt), otomatis terdeteksi. Tanpa Garet dipakai Montserrat self-host (tanpa Google Fonts).
- Warna: Biru #192e6d, Kuning #ffbf00.

LOGO KLIEN: taruh logo asli di assets/clients/ dengan nama sesuai daftar (png/webp/jpg/svg, latar transparan lebih baik):
  adira, anteraja, pakuwon, assa, mito, daai-tv, lingotalk, bitcorp, kemendagri, komdigi, kemendasmen
Otomatis dipakai menggantikan placeholder di assets/clients/_placeholder/. Disarankan lebar min. 300px (png/webp) atau SVG.
Izin pemakaian logo: minta ke tiap klien/partner.

FOTO HERO: simpan foto dari maxy.academy/enterprise (https://maxy.academy/img/sektor/corporate/hero.jpg) sebagai assets/hero.jpg (boleh juga .png/.webp).
Otomatis menggantikan ilustrasi SVG, lengkap dengan chip "Boost ROI" dan "Productive Team" (teks ada di data.php > hero > chips). Disarankan rasio mendekati 4:3.4, lebar min. 1200px.

REVISI 10 OKT 2026 (ringkas)
- Panduan nama file dan ukuran foto/video: lihat PANDUAN-ASET.txt. Contoh prompt foto AI untuk card program: PROMPT-FOTO-AI.txt.
- data.php > site > placeholders: true menampilkan kotak contoh untuk foto/video yang belum ada, false menyembunyikannya.
- data.php > testimonial > dummy: true menampilkan industri saja (tanpa nama dan perusahaan). Ubah ke false bila testimoni asli sudah ada izin tampil.
- Logo klien disematkan langsung ke HTML (data URI) lewat index.php; logo asli resolusi penuh ada di assets/clients/_original/.
- Email footer: people@infomaxy.academy (data.php > site > email). Ikon sosmed footer mengikuti daftar site > socials.
- Skor dan area pada AI Check Up adalah contoh tampilan (data.php > checkup > areas), bukan hasil nyata.
