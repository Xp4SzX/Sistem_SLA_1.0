# Product Requirement Document (PRD)
## Sistem Pengelolaan SLA & Helpdesk Support Digital
**Pemerintah Kabupaten Bandung Barat**

| Metadata | Detail |
| :--- | :--- |
| **Document Version** | v1.0.0 |
| **Status** | Approved / Design Baseline |
| **Year Focus** | 2026 |
| **Domain** | SPBE Indikator 19 (Layanan Pusat Bantuan Digital) |
| **Target User Group** | Masyarakat/ASN (User) & Tim IT Diskominfotik (Admin/Teknisi) |

---

## 1. Executive Summary & Objective

### 1.1 Latar Belakang
Sistem SLA & Helpdesk Support Pemkab Bandung Barat dirancang sebagai portal terpadu pengelolaan aduan, masalah teknis, dan permintaan layanan digital di lingkungan Pemkab Bandung Barat. Sistem ini menjadi instrumen utama pemenuhan **Indikator 19 SPBE (Pusat Bantuan Layanan Digital)** untuk mencapai kapabilitas pemantauan dan evaluasi tingkat maturitas tinggi (Level 4–5).

### 1.2 Tujuan Utama
* **Transparansi & Akuntabilitas:** Memberikan tracking tiket real-time serta visibilitas penanganan masalah teknis bagi pengguna (ASN & Publik).
* **Kepastian Layanan (SLA Management):** Menjamin waktu tanggap (*response time*) dan waktu penyelesaian (*resolution time*) terukur otomatis sesuai standar SOP.
* **Kepatuhan SPBE:** Menyediakan modul evaluasi, reviu berkala, serta laporan performa SLA per OPD owner.

---

## 2. Design System & UI/UX Guidelines

* **Style Architecture:** Modern Government Tech / Enterprise Portal, Clean UI, High-Contrast.
* **Color Palette:**
  * **Primary Deep Navy:** `#0F172A` (Header, Sidebar, Core Branding)
  * **Accent Amber Gold:** `#F59E0B` (Highlights, Secondary Actions)
  * **Background Neutral:** `#F8FAFC` (Light Gray Canvas)
  * **Surface/Card:** `#FFFFFF` (White)
* **Status & Alert Badges:**
  * **Compliant / Resolved:** `#10B981` (Green)
  * **In Progress / Active:** `#3B82F6` (Blue)
  * **Warning / Action Needed:** `#F59E0B` (Yellow)
  * **Breached / Urgent / Overdue:** `#EF4444` (Red)
* **Typography & Geometry:**
  * Font Family: `Inter` atau `Plus Jakarta Sans`
  * Corner Radius: `12px` (Cards, Inputs, Buttons, Modals)
  * Visual Elevation: Subtle Soft Shadows (`0px 4px 6px -1px rgba(0,0,0,0.05)`)

---

## 3. User Roles & Access Matrix

| Role | Core Capabilities |
| :--- | :--- |
| **Public / Guest** | Meneropong katalog layanan, cek status tiket via search bar, baca SOP & FAQ, akses registrasi/login. |
| **Registered User (ASN / Publik)** | Membuat tiket aduan, memantau *SLA countdown bar*, menerima notifikasi, memberikan konfirmasi & rating bintang (1-5). |
| **Helpdesk / Agent Teknisi** | Memproses tiket, memperbarui log penanganan, mengubah status tiket, memungut lampiran pendukung. |
| **Admin System / Executive** | Pengaturan Master Layanan, Konfigurasi Target SLA/SOP, Akses Dashboard Eksekutif SPBE Indikator 19, Audit & Reviu Evaluasi. |

---

## 4. Detailed Feature Specifications & Functional Requirements

### 4.1 Modul User Flow

#### A. Public Landing Page
* **Header Navigation:** Logo "SLA Pemkab Bandung Barat", Menu (`Home`, `Layanan`, `Cek Tiket`, `SOP`, `FAQ`), CTA (`Masuk`, `Buat Laporan`).
* **Hero Banner:** Headline *"Fasilitas Dukungan Pengguna Layanan Digital Pemerintah"*, Subheadline, & Global Search Bar (*Live search* layanan/nomor tiket).
* **Summary Stats (4 Grid Cards):** Total Layanan Digital, % Kepatuhan SLA (Target 99.9%), Rata-rata Respons (<30 Menit), Total Tiket Selesai.
* **Featured Digital Services Grid:** Card layanan (Nama Layanan, Badges Jenis Layanan, Indicator Uptime Realtime, Button "Buat Tiket").
* **Process Flow Section (4-Step Infographic):**
  1. *Buat Tiket*
  2. *Verifikasi Sistem/Admin*
  3. *Penanganan & Penghitungan SLA*
  4. *Penyelesaian & Umpan Balik*
* **Footer:** Logo Resmi Pemkab Bandung Barat, Contact Center, Copyright 2026.

#### B. Authentication Module (Split Screen Layout)
* **Left Canvas (Brand Accent):** Background Deep Navy, Graphic Visual *"Transparansi & Akuntabilitas Digital"*, Infocard SPBE Indikator 19.
* **Right Canvas (Tabbed Form):**
  * **Tab Login:** Input Credential (Email/NIK/NIP), Input Password, Checkbox *"Ingat Saya"*, Link *"Lupa Password"*, Primary CTA *"Masuk"*.
  * **Tab Register:** Input Nama Lengkap, NIK/NIP, Email, Nomor WhatsApp, Password Validation, Checkbox Syarat & Ketentuan, Primary CTA *"Daftar"*.

#### C. User Dashboard & Ticket Tracking
* **Layout Scaffolding:** Persistent Left Sidebar Navigation & Sticky Topbar (Search, Bell Notification, Profile "Arrafi Hilmi").
* **Key Metric Widgets:** Total Tiket (8), Diproses (2 - Blue), Selesai (5 - Green), Rating Pending (1 - Yellow).
* **Main Data Table (Tiket Saya):**
  * Columns: *No. Tiket* (`SLA-20260814-00001`), *Kategori Layanan*, *Judul Masalah*, *Priority Badge* (Low/Med/High/Urgent), *Status Badge*, *SLA Countdown Bar*, *Action Button*.
* **Ticket Detail Drawer (Slide-Over Panel):**
  * Vertical Workflow Stepper: `Submitted` $\rightarrow$ `Verified` $\rightarrow$ `Assigned` $\rightarrow$ `In Progress` $\rightarrow$ `Resolved` $\rightarrow$ `Closed`.
  * SLA Tracker Metrics: Realisasi vs Target Waktu Respon & Penyelesaian.
  * Catatan Solusi dari Admin/Teknisi.
  * Interactive Modal/Form: *"Konfirmasi & Rating Layanan (1–5 Stars)"*.

---

### 4.2 Modul Admin Flow

#### A. Helpdesk Management Dashboard
* **Sidebar Portal Admin:** Logo "SLA Admin Portal", Dashboard, Kelola Tiket, Kelola Layanan, SLA & SOP, Review SLA, Users, Audit Log.
* **Topbar Widget:** System Health Monitor (e.g., Uptime 99.9%), Global Search, Admin Identity.
* **KPI Metrics:** Total Diterima (34), Unassigned (5 - Red Warning), Dalam Penanganan (19), Selesai (6), Breached/Overdue (5 - Red Alert).
* **Operational Ticket Table:**
  * Multi-Filter Controls: Filter Layanan, Kategori Kebutuhan (*Backend, UI/UX, Network, Hardware*), Prioritas, SLA Status (*On Track, Warning, Overdue*).
  * Data Fields: Checkbox Batch Operations, No. Tiket, Identity Pengirim, Nama Layanan, Badge Prioritas, Realtime SLA Timer, Assigned Agent Avatar, Badge Status, Quick Action Dropdown.
* **Ticket Action Modal:** Visualisasi Detail Kendala, File Attachment Viewer, Input Form (*Kategori, Adjust Prioritas, Assignment Penanggung Jawab, Input Progress Log*), CTA Actions (*In Progress, Resolve Ticket, Reject Report*).

#### B. Executive SLA Monitoring Dashboard (SPBE Indikator 19)
* **Top Controls:** Dynamic OPD/Layanan Dropdown Filter & Date Range Picker.
* **Strategic Header Cards:**
  1. Overall SLA Compliance % (e.g., 94.2%)
  2. Response Compliance % (96%)
  3. Resolution Compliance % (92%)
  4. System Uptime Target (99.9%)
  5. Total Overdue Incident Count (20)
* **Analytics Visualizations (2x2 Grid):**
  1. *Line Chart:* Trend Performa SLA vs Volume Tiket Masuk (Bulanan).
  2. *Donut Chart:* Distibusi Tiket Belum Selesai Berdasarkan Prioritas.
  3. *Bar Chart:* Tingkat Kepatuhan SLA per Sistem Layanan Digital.
  4. *Bar Chart:* Volume Tiket Berdasarkan Kategori (*Network, Software, Hardware, Database*).
* **SLA Health Table:** Master Layanan, OPD Penanggung Jawab, Target vs Realisasi Uptime, Target vs Realisasi Response, Target vs Realisasi Resolution, Index Kepatuhan %, Badge Status Performance.

#### C. Master Management & Evaluation Module
* **Module Layout:** Tabbed Layout `[1. Master Layanan]` | `[2. SLA & SOP]` | `[3. Reviu & Evaluasi SLA]`.
* **Tab 1 (Master Layanan Table):** ID Layanan, Nama Layanan, Tipe Layanan (*Pelayanan Publik / Administrasi Pemerintahan*), OPD Owner, Status Aktif, Action Menu (Edit/Disable).
* **Tab 2 (SLA & SOP Configuration Form):**
  * Select Layanan, Input Target Uptime (%), Input Target Response Time (Menit/Jam), Input Target Resolution Time (Jam/Hari).
  * File Drag & Drop Component: Upload Dokumen SOP Resmi (`.pdf`).
  * Date Picker: Tanggal Berlaku Efektif & Jadwal Reviu Periodik.
* **Tab 3 (Audit Form & Log Indikator 19 Level 4 & 5):**
  * Audit Input Form: Tanggal Evaluation, Layanan Terkait, Temuan Evaluasi, Kendala Utama, Rekomendasi Perbaikan, Rencana Tindak Lanjut.
  * Historical Reports Table: List Dokumen Evaluasi Periodik dengan Fitur *Download Report (.pdf)* dan *Progress Indicator Tracking* (e.g., *"Tindak Lanjut: 60%"*).

---

## 5. Non-Functional Requirements (NFR)

* **Performance:** Halaman dashboard harus mampu memuat data tabular & grafik visualisasi dalam waktu $<1.5$ detik.
* **Accessibility & Responsiveness:** UI didesain fully responsive (Desktop 1440px, Tablet 768px, Mobile 375px) mengacu pada standar aksesibilitas WCAG 2.1 AA.
* **Data Integrity & Auditability:** Setiap perubahan status tiket, penetapan teknisi, dan pembaruan dokumen evaluasi wajib tercatat dalam *System Audit Log* yang immutable.