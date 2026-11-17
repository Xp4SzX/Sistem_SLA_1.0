# Spesifikasi Produk

## Tujuan dokumen

Dokumen ini merangkum fungsi yang tersedia pada aplikasi SLA Helpdesk saat ini berdasarkan route, controller, view, dan migration. Ini bukan daftar fitur rencana atau jaminan bahwa fungsi yang disebut sebagai ide sudah tersedia.

## Tujuan aplikasi

Menyediakan portal untuk pelaporan dan pemantauan tiket layanan digital serta alat kerja internal bagi admin pusat, perangkat daerah, dan teknisi. Informasi layanan mencakup perangkat daerah pemilik dan dokumen SOP yang dapat diunduh pelapor.

## Peran pengguna

| Peran | Fungsi yang tersedia |
|---|---|
| Pengunjung | Melihat beranda dan menggunakan halaman pengecekan tiket publik. |
| Pelapor (`user`) | Mendaftar, masuk, membuat dan melihat tiket, memberi rating, melihat layanan/SLA/SOP, dan mengubah profil. |
| Admin (`admin`) | Melihat dashboard, seluruh tiket, monitoring SLA lintas perangkat daerah, layanan digital, evaluasi/laporan, dan mengelola akun admin perangkat daerah. |
| Admin perangkat daerah (`admin_dinas`) | Melihat dashboard, tiket dan layanan perangkat daerahnya, mengelola teknisi, serta melihat evaluasi dan mencetak laporan. |
| Teknisi (`teknisi`) | Melihat dashboard dan ticketing, lalu memperbarui status serta informasi penanganan tiket. |

Pengguna mendaftar sebagai pelapor. Akses admin, admin perangkat daerah, dan teknisi menggunakan akun yang sudah tersedia. Login admin mengarahkan pengguna ke area berdasarkan role.

## Alur dan kemampuan utama

### Pelapor

1. Melihat layanan digital di beranda.
2. Mendaftar menggunakan nama, NIK 16 digit, nomor telepon/WhatsApp, email, dan password.
3. Membuat tiket dengan layanan, jenis permintaan, judul, deskripsi, prioritas, dan lampiran yang didukung formulir.
4. Melihat daftar dan detail tiket serta memberikan rating.
5. Melihat daftar layanan dan SLA/SOP. SOP tersedia melalui unduhan jika file telah diunggah untuk layanan tersebut.

Pada panel “Lampiran Tiket” di halaman pelapor, gambar tidak ditampilkan; tautan lampiran non-gambar tetap dapat tersedia.

### Admin

- Melihat ringkasan dashboard dan grafik tiket.
- Melihat daftar tiket lintas perangkat daerah, mencari/filter tiket, dan membuka detail baca-saja. Admin tidak mengubah status tiket dari detail ini.
- Memantau layanan dan tiket dari seluruh perangkat daerah, dengan filter perangkat daerah dan status layanan, KPI kepatuhan, serta grafik bulanan.
- Menambah, mengubah, dan menghapus layanan digital. Form layanan mendukung unggah SOP PDF/DOC/DOCX dan mempertahankan SOP lama saat form edit disimpan tanpa file baru.
- Membuka evaluasi dan halaman laporan.
- Mengelola akun admin perangkat daerah.

### Admin perangkat daerah dan teknisi

- Data tiket dan layanan pada area perangkat daerah disaring berdasarkan nama perangkat daerah.
- Admin perangkat daerah dapat memperbarui status tiket, mengelola layanan dan SOP, mengelola akun teknisi, serta mencetak evaluasi.
- Teknisi dapat memperbarui status, nama teknisi, catatan penanganan, dan mengunggah berkas penanganan.

## Aturan SLA yang terlihat pada aplikasi

- Admin ticketing dan monitoring menghitung indikator berdasarkan batas waktu 8 jam dan mengelompokkan tiket sebagai sesuai, mendekati, atau melewati batas.
- Tampilan ticketing perangkat daerah menggunakan `services.jangka_waktu` bila tersedia, dengan fallback 8 jam.
- Perhitungan dashboard teknisi menggunakan data tiket untuk ringkasan dan grafik.

Angka di atas menjelaskan perilaku implementasi saat ini; bukan konfigurasi SLA global yang dapat diubah dari antarmuka. Perhitungan dapat berbeda antarhalaman.

## Status dan data tiket

Tabel tiket menyimpan status sebagai string. Nilai yang dipakai kode mencakup `submitted`, `verified`, `assigned`, `in_progress`, `resolved`, `completed`, dan `closed`; beberapa halaman juga mengenali `process`. Jenis permintaan yang dibatasi di skema adalah `pengajuan_layanan` dan `kendala_teknis`.

## Batasan yang diketahui

- Route area dibungkus middleware `auth`, tetapi pembatasan role tidak diterapkan melalui middleware role yang konsisten pada setiap grup. Login admin melakukan pemeriksaan role dan redirect, namun dokumentasi ini tidak menyatakan isolasi akses sebagai kontrol yang terjamin.
- Monitoring admin memakai aturan waktu yang dihitung di controller; target SLA yang tersimpan pada `service_slas` tidak menjadi satu-satunya sumber perhitungan di semua halaman.
- Belum ada tabel migration `sla_evaluations`; halaman evaluasi bukan bukti adanya rekaman evaluasi persisten.
- Dalam route/migration saat ini tidak ditemukan modul notifikasi, audit log tersendiri, atau manajemen kategori terpisah.
- Daftar pengembangan di atas tidak dimaksudkan sebagai rekomendasi keamanan atau spesifikasi fitur baru.

## Sumber acuan implementasi

- Route: `routes/web.php`
- Skema: `database/migrations/`
- Antarmuka: `resources/views/`
- Perilaku: `app/Http/Controllers/`
