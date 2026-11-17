# Spesifikasi Antarmuka (Implementasi Saat Ini)

Dokumen ini menjelaskan halaman dan interaksi yang sudah ada pada route serta view aplikasi. Jika sebuah menu atau route tidak tercantum di sini, dokumen lama tidak boleh dipakai sebagai bukti bahwa fitur tersebut tersedia.

## Kerangka visual

- Antarmuka dibuat dengan Blade, Tailwind CSS, dan komponen layout Blade.
- Area admin, perangkat daerah, teknisi, dan portal pelapor memiliki layout navigasi masing-masing.
- Tampilan menggunakan warna hijau/emerald sebagai aksen utama, dengan slate untuk permukaan dan teks serta amber untuk peringatan.
- Beberapa dashboard dan monitoring memakai Chart.js.
- Elemen tabel, kartu ringkasan, filter, modal, dan formulir menyesuaikan lebar layar sesuai kelas responsif yang digunakan pada view.

## Halaman umum dan autentikasi

| Halaman | Route | Perilaku |
|---|---|---|
| Beranda | `GET /` | Menampilkan informasi layanan digital dan alur pelaporan tiket. |
| Cek tiket | `GET /cek-tiket` | Pengecekan tiket publik. |
| Login pelapor | `GET, POST /login` | Masuk menggunakan email atau NIK dan password. |
| Registrasi | `GET, POST /register` | Membuat akun pelapor. |
| Login admin | `GET, POST /admin/login` | Login untuk role admin, admin perangkat daerah, dan teknisi; diarahkan ke dashboard sesuai role. |
| Logout | `POST /logout` | Mengakhiri sesi pengguna yang sudah login. |

## Portal pelapor

Semua halaman berikut berada di balik autentikasi.

| Halaman | Route | Perilaku |
|---|---|---|
| Dashboard | `GET /dashboard` | Ringkasan tiket dan grafik. |
| Ticketing | `GET /ticketing` atau `GET /tickets` | Daftar tiket pelapor dan panel detail. Bagian “Lampiran Tiket” tidak menampilkan gambar. |
| Buat tiket | `GET /buat-tiket` atau `/tickets/create`; submit `POST /buat-tiket` | Form pengajuan tiket dan lampiran. |
| Detail tiket | `GET /tickets/{ticket_no}` | Detail tiket berdasarkan nomor tiket. |
| Rating | `POST /tickets/{ticket_no}/rating` | Mengirim rating tiket. |
| SLA & SOP | `GET /sla-sop` | Menampilkan layanan dan informasi SLA/SOP yang tersedia. |
| Unduh SOP | `GET /sla-sop/{service}/download` | Mengunduh SOP tersimpan untuk layanan. |
| Profil | `GET /profile`, simpan `PUT /profile` | Melihat dan memperbarui profil. |

## Area admin

Route memakai prefix `/admin` dan autentikasi.

Catatan: route `GET /admin/tickets/{id}` masih terdaftar, tetapi `Admin\HelpdeskController` belum menyediakan action `show`. Detail yang digunakan pada halaman adalah modal baca-saja di `/admin/ticketing`.

| Halaman | Route | Interaksi |
|---|---|---|
| Dashboard | `GET /admin/dashboard` | Kartu ringkasan dan grafik tiket. |
| Ticketing | `GET /admin/ticketing` | Daftar tiket lintas perangkat daerah, pencarian/filter, informasi asal perangkat daerah, dan ringkasan SLA. Tombol “Detail Ticketing” membuka modal baca-saja; tidak ada perubahan status melalui UI admin. |
| Monitoring SLA | `GET /admin/sla-monitoring` | Ringkasan layanan dan tiket lintas perangkat daerah, filter perangkat daerah/status layanan, KPI, dan grafik. |
| Layanan digital | `GET, POST /admin/layanandigital`; `PUT, DELETE /admin/layanandigital/{id}` | Daftar serta CRUD layanan, termasuk unggah/pengelolaan file SOP. |
| Evaluasi | `GET /admin/evaluasi` | Halaman evaluasi dan ringkasan. |
| Ekspor evaluasi | `GET /admin/evaluasi/export-pdf` | Membuka alur laporan evaluasi yang disediakan aplikasi. |
| Kelola admin perangkat daerah | `GET, POST /admin/kelola-dinas`; `PUT, DELETE /admin/kelola-dinas/{id}` | Mengelola akun admin perangkat daerah. |

## Area perangkat daerah

Route memakai prefix `/perangkat-daerah` dan autentikasi. Daftar tiket/layanan dibatasi berdasarkan nama perangkat daerah.

| Halaman | Route | Interaksi |
|---|---|---|
| Dashboard | `GET /perangkat-daerah/dashboard` | Ringkasan dan grafik operasional. |
| Ticketing | `GET /perangkat-daerah/ticketing` | Daftar/filter tiket perangkat daerah. Status dapat diperbarui melalui UI. |
| Perbarui status tiket | `POST, PUT /perangkat-daerah/tickets/{id}/update-status` | Mengubah status dan informasi teknisi yang didukung endpoint. |
| Layanan digital | `/perangkat-daerah/layanandigital` | CRUD layanan, file SOP, serta dokumen layanan. |
| Dokumen layanan | `GET /perangkat-daerah/layanandigital/{service}/document`; `GET /perangkat-daerah/layanandigital/document/all` | Mengunduh dokumen layanan. |
| Kelola teknisi | `GET, POST /perangkat-daerah/teknisi`; `DELETE /perangkat-daerah/teknisi/{id}` | Melihat, menambah, dan menghapus akun teknisi. |
| Evaluasi | `GET /perangkat-daerah/evaluasi` | Ringkasan evaluasi perangkat daerah. |
| Cetak laporan | `GET /perangkat-daerah/evaluasi/cetak` | Menampilkan laporan untuk dicetak. |

## Area teknisi

Route memakai prefix `/teknisi` dan autentikasi.

| Halaman | Route | Interaksi |
|---|---|---|
| Dashboard | `GET /teknisi/dashboard` | Ringkasan tiket dan grafik. |
| Ticketing | `GET /teknisi/ticketing` | Daftar tiket dengan pencarian/filter. |
| Perbarui penanganan | `POST, PUT /teknisi/tickets/{id}/update-status` | Mengubah status, nama teknisi, catatan, dan mengunggah berkas penanganan. |

## Perilaku file

- SOP layanan diunggah melalui formulir layanan admin atau perangkat daerah dan ditautkan pada halaman SLA & SOP pelapor jika tersedia.
- Lampiran tiket dan berkas penanganan merupakan file terpisah. Menyembunyikan tampilan gambar pada bagian lampiran pelapor tidak menghapus berkas dari storage.
- File disimpan melalui Laravel filesystem; aplikasi memerlukan symbolic link storage publik untuk menyajikan file yang ditautkan.

## Catatan akses

Prefix route memisahkan area berdasarkan peran, tetapi route group saat ini menggunakan middleware `auth` dan tidak memiliki middleware role yang konsisten. Tabel ini mendeskripsikan navigasi yang tersedia, bukan jaminan otorisasi per-role.
