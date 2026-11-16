# Database Design & Schema Specification
## Sistem Pengelolaan SLA & Helpdesk Support Digital
**Pemerintah Kabupaten Bandung Barat**

| Metadata | Detail |
| :--- | :--- |
| **Document Version** | v1.0.0 |
| **Database Engine** | PostgreSQL 15+ / MySQL 8.0+ |
| **ORM Compatibility** | Laravel Eloquent / Prisma |
| **Domain** | SPBE Indikator 19 (Layanan Pusat Bantuan Digital) |

---

## 1. Entity Relationship Diagram (ERD) Overview

Secara arsitektur, basis data dirancang terintegrasi untuk menangani alur dari tiket publik hingga audit reviu SPBE Indikator 19.

---

### 2. Table Specifications & Data Dictionaries

#### 2.1 Table: `departments` (OPD / Unit Kerja)
Menyimpan data Organisasi Perangkat Daerah (OPD) pemilik layanan digital.

| Column Name | Data Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique Identifier OPD |
| `code` | VARCHAR(20) | UNIQUE, NOT NULL | Kode Unik OPD (e.g., `DISKOMINFOTIK`) |
| `name` | VARCHAR(150) | NOT NULL | Nama OPD/Dinas |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | NULL ON UPDATE | Waktu diperbarui |

---

#### 2.2 Table: `users` (Pengguna & Helpdesk)
Menyimpan data identitas pengguna (Publik, ASN, Helpdesk/Agent, Admin).

| Column Name | Data Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique Identifier User |
| `department_id` | BIGINT | NULLABLE, FOREIGN KEY (`departments.id`) | OPD asal jika user ASN |
| `identity_number`| VARCHAR(30) | UNIQUE, NOT NULL | NIK / NIP Pengguna |
| `name` | VARCHAR(100) | NOT NULL | Nama Lengkap |
| `email` | VARCHAR(100) | UNIQUE, NOT NULL | Alamat Email |
| `whatsapp_number`| VARCHAR(20) | NOT NULL | Nomor WhatsApp Aktif |
| `password` | VARCHAR(255) | NOT NULL | Hash Password |
| `role` | ENUM | NOT NULL | `user`, `agent`, `admin` |
| `remember_token` | VARCHAR(100) | NULLABLE | Token Sesi Login |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Waktu Pendaftaran |
| `updated_at` | TIMESTAMP | NULL ON UPDATE | Waktu diperbarui |

---

#### 2.3 Table: `services` (Master Layanan Digital)
Katalog sistem/aplikasi digital yang dipelihara oleh Pemkab Bandung Barat.

| Column Name | Data Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique Identifier Layanan |
| `department_id` | BIGINT | NOT NULL, FOREIGN KEY (`departments.id`) | OPD Penanggung Jawab |
| `service_code` | VARCHAR(20) | UNIQUE, NOT NULL | Kode Layanan (e.g., `SERV-PERIZINAN`) |
| `name` | VARCHAR(100) | NOT NULL | Nama Layanan Digital |
| `type` | ENUM | NOT NULL | `pelayanan_publik`, `administrasi_pemerintahan` |
| `current_uptime` | DECIMAL(5,2) | DEFAULT 100.00 | Realtime Uptime Percentage |
| `status` | ENUM | DEFAULT 'active' | `active`, `maintenance`, `disabled` |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Waktu ditambahkan |
| `updated_at` | TIMESTAMP | NULL ON UPDATE | Waktu diperbarui |

---

#### 2.4 Table: `service_slas` (Konfigurasi Target SLA & SOP)
Target ambang batas penanganan layanan & penyimpanan dokumen SOP resmi.

| Column Name | Data Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique Identifier SLA Config |
| `service_id` | BIGINT | UNIQUE, NOT NULL, FOREIGN KEY (`services.id`) | ID Layanan Terkait |
| `target_uptime` | DECIMAL(5,2) | DEFAULT 99.90 | Target Uptime (%) |
| `target_response_time` | INT | NOT NULL | Target Respon (dalam Menit) |
| `target_resolution_time` | INT | NOT NULL | Target Solusi (dalam Jam) |
| `sop_document_url` | VARCHAR(255) | NULLABLE | Path File PDF SOP |
| `effective_date` | DATE | NOT NULL | Tanggal Mulai Berlaku |
| `review_date` | DATE | NOT NULL | Tanggal Jadwal Reviu Periodik |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Waktu Dibuat |
| `updated_at` | TIMESTAMP | NULL ON UPDATE | Waktu diperbarui |

---

#### 2.5 Table: `tickets` (Pengelolaan Tiket Aduan)
Tabel utama penyimpanan seluruh data laporan aduan dan tracking penanganan.

| Column Name | Data Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique Identifier Tiket |
| `ticket_no` | VARCHAR(30) | UNIQUE, NOT NULL | Format Nomor Tiket: `SLA-YYYYMMDD-XXXXX` |
| `user_id` | BIGINT | NOT NULL, FOREIGN KEY (`users.id`) | Pengirim Tiket |
| `service_id` | BIGINT | NOT NULL, FOREIGN KEY (`services.id`) | Layanan Terkait |
| `assigned_agent_id`| BIGINT | NULLABLE, FOREIGN KEY (`users.id`) | Agent Teknisi Penanggung Jawab |
| `title` | VARCHAR(150) | NOT NULL | Judul Ringkas Kendala |
| `description` | TEXT | NOT NULL | Deskripsi Rinci Kendala |
| `category` | ENUM | DEFAULT 'software' | `backend`, `ui_ux`, `network`, `hardware`, `database` |
| `priority` | ENUM | DEFAULT 'medium' | `low`, `medium`, `high`, `urgent` |
| `status` | ENUM | DEFAULT 'submitted' | `submitted`, `verified`, `assigned`, `in_progress`, `resolved`, `closed`, `rejected` |
| `sla_status` | ENUM | DEFAULT 'on_track' | `on_track`, `warning`, `overdue` |
| `first_responded_at`| TIMESTAMP | NULLABLE | Waktu Respons Pertama oleh Teknisi |
| `resolved_at` | TIMESTAMP | NULLABLE | Waktu Tiket Dinyatakan Selesai |
| `rating` | SMALLINT | CHECK (rating BETWEEN 1 AND 5) | Umpan Balik Bintang User |
| `rating_feedback` | TEXT | NULLABLE | Catatan / Ulasan Tambahan User |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Waktu Pengajuan Tiket |
| `updated_at` | TIMESTAMP | NULL ON UPDATE | Waktu diperbarui |

---

#### 2.6 Table: `ticket_attachments` (Lampiran File)
Menyimpan lampiran bukti foto atau dokumen penunjang kendala/solusi.

| Column Name | Data Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique Identifier Attachment |
| `ticket_id` | BIGINT | NOT NULL, FOREIGN KEY (`tickets.id`) | ID Tiket Terkait |
| `file_name` | VARCHAR(255) | NOT NULL | Nama Original File |
| `file_path` | VARCHAR(255) | NOT NULL | Path/URL Penyimpanan File |
| `file_type` | VARCHAR(50) | NOT NULL | MIME Type (e.g., `image/png`, `application/pdf`) |
| `uploaded_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Waktu Upload |

---

#### 2.7 Table: `ticket_logs` (Histori Penanganan & Stepper)
Menyimpan jejak audit/aktivitas kronologis penanganan tiket.

| Column Name | Data Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique Identifier Log |
| `ticket_id` | BIGINT | NOT NULL, FOREIGN KEY (`tickets.id`) | ID Tiket Terkait |
| `actor_id` | BIGINT | NOT NULL, FOREIGN KEY (`users.id`) | Pelaku Perubahan Status/Log |
| `previous_status` | VARCHAR(30) | NULLABLE | Status Sebelum Perubahan |
| `new_status` | VARCHAR(30) | NOT NULL | Status Setelah Perubahan |
| `notes` | TEXT | NULLABLE | Catatan Log / Solusi Sementara |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Tanggal Eksekusi |

---

#### 2.8 Table: `sla_evaluations` (Audit & Evaluasi SPBE Indikator 19)
Penyimpanan hasil audit evaluasi berkala untuk pencapaian Tingkat Maturitas SPBE (Level 4 & 5).

| Column Name | Data Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique Identifier Evaluation |
| `service_id` | BIGINT | NOT NULL, FOREIGN KEY (`services.id`) | Layanan Digital yang Dievaluasi |
| `evaluation_date` | DATE | NOT NULL | Tanggal Audit Dilaksanakan |
| `findings` | TEXT | NOT NULL | Temuan Ringkas Hasil Evaluasi |
| `issues` | TEXT | NOT NULL | Kendala/Isu Utama Penyelenggaraan SLA |
| `recommendations` | TEXT | NOT NULL | Rekomendasi Tindak Lanjut |
| `follow_up_plan` | TEXT | NOT NULL | Rencana Aksi Pembenahan |
| `follow_up_progress`| INT | DEFAULT 0 | Persentase Progress Tindak Lanjut (0-100%) |
| `report_document_url`| VARCHAR(255)| NULLABLE | Path File PDF Laporan Hasil Audit |
| `created_by` | BIGINT | NOT NULL, FOREIGN KEY (`users.id`) | Admin Evaluator |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Waktu Input Evaluasi |

---

### 3. SQL Indexes & Optimization Strategy

Untuk mempertahankan responsivitas query dashboard visual di bawah **1.5 detik**, indeks basis data diterapkan pada kolom pencarian utama:

```sql
-- Indeks Pencarian Tiket Berdasarkan Nomor Tiket & User
CREATE INDEX idx_tickets_ticket_no ON tickets(ticket_no);
CREATE INDEX idx_tickets_user_id ON tickets(user_id);

-- Indeks Dashboard Operasional Helpdesk & SLA Status
CREATE INDEX idx_tickets_status_sla ON tickets(status, sla_status);
CREATE INDEX idx_tickets_created_at ON tickets(created_at DESC);

-- Indeks Pencarian Agregat berdasarkan Layanan
CREATE INDEX idx_tickets_service_id ON tickets(service_id);