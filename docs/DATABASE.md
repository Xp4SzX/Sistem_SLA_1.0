# Database

Dokumen ini menjelaskan skema yang dibentuk oleh migration di `database/migrations`, bukan rancangan lama atau struktur yang hanya didefinisikan oleh model.

## Konfigurasi

- Koneksi basis data ditentukan oleh `DB_CONNECTION`.
- `.env.example` memakai PostgreSQL (`pgsql`) sebagai konfigurasi awal.
- Pengujian otomatis memakai SQLite in-memory.
- Sesuaikan koneksi, nama database, dan kredensial di `.env` dengan lingkungan masing-masing.

## Relasi utama

```text
users 1 ── * tickets
services 1 ── * tickets
services 1 ── * service_slas (foreign key; model saat ini mendefinisikan relasi has-one)
tickets 1 ── * ticket_attachments
tickets 1 ── * ticket_logs
```

`services.department_name` menyimpan nama perangkat daerah sebagai teks, bukan foreign key. Tabel `dinas_admins` menyimpan akun admin perangkat daerah secara terpisah. Migration menghapus tabel `departments` dan `users.department_id`; karena itu keduanya bukan bagian dari skema akhir.

## Tabel aplikasi

### `users`

| Kolom | Keterangan |
|---|---|
| `id` | Primary key |
| `identity_number` | Nomor identitas, unik |
| `name` | Nama pengguna |
| `email` | Email, unik |
| `whatsapp_number` | Nomor WhatsApp |
| `password` | Password yang di-hash |
| `role` | String role, default `user`; aplikasi menggunakan `admin`, `admin_dinas`, `teknisi`, dan `user` |
| `department_name` | Nama perangkat daerah, nullable |
| `remember_token`, `created_at`, `updated_at` | Kolom bawaan Laravel |

### `dinas_admins`

`id`, `nama_dinas`, `name`, `email` (unik), `whatsapp`, `penanggung_jawab`, `password`, `created_at`, `updated_at`.

### `services`

| Kolom | Keterangan |
|---|---|
| `id` | Primary key |
| `department_name` | Nama perangkat daerah, default `Pemerintah Daerah` |
| `service_code` | Kode layanan, unik |
| `name`, `type`, `status` | Nama layanan dan jenis/status sebagai string |
| `current_uptime` | Decimal (5,2), default 100 |
| `is_active` | Boolean, default true |
| `operator_role`, `category` | Nullable |
| `description`, `image`, `sop_file` | Deskripsi dan path file, nullable |
| `response_time`, `resolution_time` | String, default `30 menit` dan `8 jam` |
| `valid_from`, `valid_until` | Tanggal masa berlaku, nullable |
| `dasar_hukum`, `persyaratan`, `sistem_prosedur`, `jangka_waktu` | Informasi standar layanan |
| `biaya`, `produk_pelayanan`, `sarana_prasarana`, `kompetensi_pelaksana` | Informasi standar layanan |
| `pengawasan_internal`, `penanganan_pengaduan`, `jumlah_pelaksana` | Informasi standar layanan |
| `jaminan_pelayanan`, `jaminan_keamanan`, `evaluasi_kinerja` | Informasi standar layanan |
| `menimbang`, `mengingat` | Teks tambahan, nullable |
| `created_at`, `updated_at` | Timestamp |

Nilai jenis, status, dan kategori disimpan sebagai string; bukan tabel kategori terpisah.

### `service_slas`

`id`, `service_id` (foreign key ke `services`, cascade saat layanan dihapus), `target_uptime` (decimal 5,2; default 99,90), `target_response_time` (integer; default 30 menit), `target_resolution_time` (integer; default 8 jam), `effective_date`, `review_date`, `created_at`, `updated_at`.

Migration tidak menetapkan constraint unik pada `service_id`, walaupun model layanan saat ini mengekspos relasi SLA sebagai `hasOne`.

### `tickets`

| Kolom | Keterangan |
|---|---|
| `id`, `ticket_no` | Primary key dan nomor tiket (unik) |
| `user_id` | Foreign key ke pelapor; cascade saat pengguna dihapus |
| `service_id` | Foreign key ke layanan, nullable; cascade saat layanan dihapus |
| `assigned_agent_id` | Foreign key nullable ke `users`; menjadi null saat pengguna dihapus |
| `request_type` | Enum `pengajuan_layanan` atau `kendala_teknis` |
| `service_category`, `problem_category` | String nullable |
| `title`, `description`, `image` | Isi tiket dan path gambar nullable |
| `category`, `priority`, `status`, `sla_status` | String dengan default `backend`, `medium`, `submitted`, `on_track` |
| `first_responded_at`, `resolved_at` | Timestamp nullable |
| `rating`, `rating_feedback` | Penilaian nullable |
| `technician`, `technician_notes`, `technician_file` | Informasi penanganan; kolom penanganan dapat null |
| `created_at`, `updated_at` | Timestamp |

Migration menambahkan index gabungan pada `status` dan `sla_status`. Status tiket disimpan sebagai string, jadi daftar nilai yang digunakan aplikasi tidak dibatasi oleh enum basis data.

### `ticket_attachments`

`id`, `ticket_id` (foreign key ke `tickets`, cascade), `file_name`, `file_path`, `file_type` (nullable), `created_at`, `updated_at`.

### `ticket_logs`

`id`, `ticket_id` (foreign key ke `tickets`, cascade), `user_id` (foreign key nullable ke `users`, set null saat pengguna dihapus), `action`, `description` (nullable), `created_at`, `updated_at`.

### Tabel pendukung Laravel

- `cache` dan `cache_locks` untuk cache berbasis database.
- `sessions` untuk sesi berbasis database; berisi ID sesi, pengguna nullable, IP, user agent, payload, dan waktu aktivitas.
- `migrations` dikelola oleh Laravel.

## Batas skema

- Tidak ada migration untuk tabel `departments` pada skema akhir maupun tabel `sla_evaluations`.
- Model/relasi lama yang masih menyebut `department_id` atau `Department` tidak menciptakan kolom/tabel tersebut. Gunakan `department_name` dan tabel `dinas_admins` saat merujuk struktur yang benar-benar dimigrasikan.
- File unggahan disimpan pada storage publik Laravel; kolom basis data menyimpan path, bukan isi file.
