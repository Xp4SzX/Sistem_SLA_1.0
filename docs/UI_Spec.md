# PRODUCT REQUIREMENTS DOCUMENT
# Sistem Fasilitas Dukungan Pengguna & Monitoring SLA

**Project:** Sistem Fasilitas Dukungan Pengguna & Monitoring SLA  
**Document:** Product Requirements Document  
**Version:** 1.0.0  
**Status:** Draft  
**Tanggal:** 14 Agustus 2026

---

# 1. Overview

## 1.1 Nama Sistem

Sistem Fasilitas Dukungan Pengguna & Monitoring SLA

## 1.2 Tujuan

Sistem digunakan untuk menyediakan fasilitas pelaporan dan penanganan permasalahan pengguna melalui ticketing serta melakukan monitoring terhadap pemenuhan Service Level Agreement (SLA).

Sistem memiliki dua pengguna:

1. User
2. Admin

User digunakan untuk membuat dan memantau ticket.

Admin digunakan untuk mengelola ticket, layanan, SLA, SOP, user, dan melakukan monitoring.

---

# 2. Problem Statement

Permasalahan yang ingin diselesaikan:

- Pelaporan permasalahan masih belum terstruktur.
- Status penanganan sulit dipantau.
- Tidak terdapat monitoring SLA secara terpusat.
- Admin kesulitan mengetahui ticket yang mendekati atau melewati batas SLA.
- Riwayat penanganan belum terdokumentasi dengan baik.
- Evaluasi terhadap pencapaian SLA belum terintegrasi.

---

# 3. Product Goals

Sistem bertujuan untuk:

- Menyediakan sistem ticketing.
- Memudahkan user melaporkan permasalahan.
- Memudahkan admin melakukan pengelolaan ticket.
- Menampilkan status ticket secara real-time berdasarkan data sistem.
- Melakukan monitoring SLA.
- Menyediakan histori penanganan.
- Menyediakan notifikasi.
- Menyediakan laporan dan evaluasi SLA.

---

# 4. User Roles

Sistem hanya memiliki dua role.

| Role | Deskripsi |
|---|---|
| User | Pengguna yang membuat dan memantau ticket |
| Admin | Pengelola sistem dan penanganan ticket |

---

# 5. Scope

## 5.1 User

User dapat:

- Login.
- Melihat dashboard.
- Membuat ticket.
- Melihat ticket miliknya.
- Melihat detail ticket.
- Melihat status SLA ticket.
- Melihat timeline penanganan.
- Mengunggah attachment.
- Memberikan feedback.
- Menerima notifikasi.
- Mengelola profile.

## 5.2 Admin

Admin dapat:

- Login.
- Melihat dashboard.
- Melihat seluruh ticket.
- Memverifikasi ticket.
- Assign ticket.
- Mengubah status ticket.
- Mengelola layanan.
- Mengelola kategori.
- Mengelola SLA.
- Mengelola SOP.
- Melakukan review SLA.
- Mengelola user.
- Melihat feedback.
- Melihat notifikasi.
- Melihat audit log.

---

# 6. Core Features

## 6.1 Authentication

Fitur:

- Login.
- Logout.
- Session management.
- Role-based access.

---

## 6.2 Ticket Management

Ticket memiliki informasi:

- Ticket number.
- User.
- Service.
- Category.
- Subject.
- Description.
- Priority.
- Status.
- SLA.
- Assignment.
- Attachment.
- Created time.
- Updated time.

---

## 6.3 Ticket Status

Status ticket:

```text
Submitted
    ↓
Verified
    ↓
Assigned
    ↓
In Progress
    ↓
Resolved
    ↓
Closed

Status tambahan:

Rejected
Cancelled
6.4 Priority

Priority:

Low
Medium
High
Urgent
7. SLA Management

Sistem harus dapat menentukan SLA berdasarkan layanan dan aturan SLA.

Informasi SLA:

Response time.
Resolution time.
SLA target.
Elapsed time.
Remaining time.
SLA percentage.
SLA status.

Status SLA:

ON TRACK
WARNING
OVERDUE
COMPLIANT
BREACHED
8. SLA Monitoring

Admin dapat melihat:

Ticket yang masih dalam SLA.
Ticket yang mendekati deadline.
Ticket yang telah melewati deadline.
Persentase SLA.
Average response time.
Average resolution time.
SLA compliance.
9. Notification

Notifikasi diberikan ketika:

Ticket dibuat.
Ticket diverifikasi.
Ticket ditugaskan.
Ticket diproses.
SLA mendekati deadline.
SLA terlewati.
Ticket resolved.
Ticket closed.
10. Feedback

Setelah ticket diselesaikan, user dapat memberikan:

Rating.
Comment.

Feedback digunakan untuk evaluasi kualitas layanan.

11. Dashboard
11.1 User Dashboard

Menampilkan:

Total Ticket
Open Ticket
In Progress
Resolved

Ditambah:

Ticket terbaru.
Status ticket.
SLA ticket.
Tombol Create Ticket.
11.2 Admin Dashboard

Menampilkan:

Total Tickets
Open Tickets
In Progress
Resolved
Overdue SLA
SLA Compliance
Average Response Time
Average Resolution Time

Dashboard juga menampilkan ticket yang membutuhkan perhatian.

12. UI/UX REQUIREMENTS

Bagian ini menyatukan seluruh kebutuhan UI/UX ke dalam prd.md.

13. UI/UX Goals

UI harus:

Sederhana.
Mudah digunakan.
Responsive.
Konsisten.
Menampilkan status dengan jelas.
Meminimalkan jumlah langkah.
Memberikan feedback setelah aksi.
Memiliki validasi form.
Menampilkan error dengan jelas.
14. Design Principles
14.1 Simplicity

Interface tidak boleh terlalu kompleks.

14.2 Visibility

Informasi penting seperti status ticket dan SLA harus mudah ditemukan.

14.3 Consistency

Button, badge, card, table, modal, dan form menggunakan pola desain yang konsisten.

14.4 Feedback

Setiap aksi penting memberikan feedback.

Contoh:

Ticket berhasil dibuat.
Ticket berhasil diperbarui.
Ticket berhasil ditugaskan.
Ticket berhasil diselesaikan.
15. Global Layout

Desktop:

┌─────────────────────────────────────────────────────┐
│                     TOP NAVBAR                      │
├───────────────┬─────────────────────────────────────┤
│               │                                     │
│    SIDEBAR    │            MAIN CONTENT             │
│               │                                     │
│ Dashboard     │                                     │
│ Tickets       │                                     │
│ Services      │                                     │
│ SLA           │                                     │
│ Users         │                                     │
│               │                                     │
│ Profile       │                                     │
└───────────────┴─────────────────────────────────────┘

Mobile:

┌─────────────────────────────┐
│ ☰    LOGO           🔔 👤  │
├─────────────────────────────┤
│                             │
│        MAIN CONTENT         │
│                             │
└─────────────────────────────┘

Sidebar pada mobile menjadi drawer.

16. Navigation
16.1 User
Dashboard
Tickets
Notifications
Profile

CTA utama:

+ Buat Ticket
16.2 Admin
Dashboard
Tickets


Services
Categories
SLA
SOP
SLA Review


Users


Notifications
Profile
17. Authentication UI
Login
┌─────────────────────────────────────────┐
│                                         │
│                 LOGO                    │
│                                         │
│     Sistem Dukungan & Monitoring SLA    │
│                                         │
│ Email                                   │
│ ┌─────────────────────────────────────┐ │
│ │                                     │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ Password                                │
│ ┌─────────────────────────────────────┐ │
│ │ •••••••••••                         │ │
│ └─────────────────────────────────────┘ │
│                                         │
│          [       Login       ]          │
│                                         │
└─────────────────────────────────────────┘

Komponen:

Email.
Password.
Show password.
Login button.
Error message.
18. User UI
18.1 User Dashboard
┌─────────────────────────────────────────────┐
│ Dashboard                                   │
│ Selamat datang, User                        │
├────────────┬────────────┬───────────────────┤
│ Total      │ Open       │ Resolved          │
│ Ticket     │ Ticket     │ Ticket            │
│    12      │     3      │      9            │
└────────────┴────────────┴───────────────────┘


┌─────────────────────────────────────────────┐
│ Ticket Terbaru                              │
├─────────────────────────────────────────────┤
│ SLA-00003  Login Error    In Progress       │
│ SLA-00002  Network        Resolved           │
└─────────────────────────────────────────────┘


              [ + Buat Ticket ]
19. Ticket List

URL:

/user/tickets

Fitur:

Search.
Filter status.
Filter priority.
Filter service.
Sort.
Pagination.

Desktop:

┌────────────────────────────────────────────────────┐
│ My Tickets                         [+ New Ticket] │
├────────────┬──────────┬──────────┬───────┬────────┤
│ Ticket     │ Subject  │ Service  │Status │Priority│
├────────────┼──────────┼──────────┼───────┼────────┤
│ SLA-00001  │ Login    │ Service A│ Open  │ High   │
│ SLA-00002  │ Database │ Service B│ Closed│ Medium │
└────────────┴──────────┴──────────┴───────┴────────┘

Mobile menggunakan card:

┌─────────────────────────────┐
│ SLA-00001                   │
│ Login tidak dapat dilakukan │
│                             │
│ [High] [In Progress]        │
│                             │
│ Service A                   │
│                             │
│             [Detail →]      │
└─────────────────────────────┘
20. Create Ticket

URL:

/user/tickets/create

Form:

Layanan
[ Pilih layanan ▼ ]


Kategori
[ Pilih kategori ▼ ]


Judul
[________________________]


Prioritas
[ Medium ▼ ]


Deskripsi
┌────────────────────────────┐
│                            │
│                            │
└────────────────────────────┘


Lampiran
[ Upload File ]


[ Batal ] [ Submit Ticket ]

Field wajib:

Layanan
Kategori
Judul
Prioritas
Deskripsi
21. Ticket Detail

URL:

/user/tickets/{id}

Informasi:

Ticket Number
Subject
Service
Category
Priority
Status
Description
Attachment
SLA
Timeline

Contoh:

┌─────────────────────────────────────────────┐
│ SLA-20260814-00001                          │
│ Login tidak dapat dilakukan                 │
│                                             │
│ [High] [In Progress]                        │
├─────────────────────────────────────────────┤
│ Service                                     │
│ Sistem Perizinan Online                     │
│                                             │
│ Description                                 │
│ User tidak dapat login ke sistem.           │
├─────────────────────────────────────────────┤
│ SLA STATUS                                  │
│                                             │
│ Response SLA       Resolution SLA            │
│ 20 menit           4 jam 20 menit            │
│                                             │
│ [ ON TRACK ]                                │
├─────────────────────────────────────────────┤
│ Timeline                                    │
│                                             │
│ ● Ticket dibuat                             │
│ ● Ticket diverifikasi                       │
│ ● Ticket ditangani                          │
└─────────────────────────────────────────────┘
22. Ticket Timeline

Timeline menampilkan:

Status
Actor
Timestamp
Note

Contoh:

● In Progress


Admin Sistem
14 Agustus 2026, 08:20


Ticket sedang diperiksa.
23. Admin Dashboard UI
┌──────────────────────────────────────────────────────┐
│ Dashboard Admin                                      │
├────────────┬────────────┬────────────┬───────────────┤
│ Total      │ Open       │ Resolved   │ Overdue       │
│ Tickets    │ Tickets    │ Tickets    │ SLA           │
│    120     │    32      │    80      │      8        │
└────────────┴────────────┴────────────┴───────────────┘


┌───────────────────────────┐ ┌────────────────────────┐
│ Ticket Status             │ │ SLA Compliance         │
│                           │ │                        │
│ Open        32            │ │ 92%                    │
│ In Progress 20            │ │                        │
│ Resolved    50            │ │ Target: 95%            │
└───────────────────────────┘ └────────────────────────┘


┌──────────────────────────────────────────────────────┐
│ Ticket Membutuhkan Perhatian                         │
├────────────┬────────────┬────────────┬───────────────┤
│ Ticket     │ Service    │ SLA        │ Status        │
├────────────┼────────────┼────────────┼───────────────┤
│ SLA-00021  │ Service A  │ 85%        │ Warning       │
│ SLA-00019  │ Service B  │ 105%       │ Overdue       │
└────────────┴────────────┴────────────┴───────────────┘
24. Admin Ticket Management

URL:

/admin/tickets

Admin dapat:

Melihat seluruh ticket.
Search.
Filter status.
Filter priority.
Filter service.
Filter SLA.
Filter tanggal.
Assign admin.
Mengubah status.
Membuka detail.
25. Admin Ticket Detail

Admin dapat melihat:

User
Service
Category
Priority
Description
Attachment
SLA
Assignment
Timeline

Action:

Assign
Change Status
Add Note
Resolve
Close

Aksi penting harus menggunakan confirmation modal.

26. SLA UI

SLA harus ditampilkan secara visual.

Informasi:

Target
Elapsed Time
Remaining Time
Percentage Used
Status

Contoh:

Resolution SLA


Target:
8 jam


Elapsed:
6 jam 20 menit


Remaining:
1 jam 40 menit


Usage:
79%


Status:
[ WARNING ]

Progress:

0%                         80%             100%
|--------------------------|----------------|
       ON TRACK             WARNING          OVERDUE
27. Service Management UI

URL:

/admin/services

Admin dapat:

Create service.
Edit service.
Activate/deactivate.
View service detail.

Table:

Service
Type
Status
Active SLA
Open Tickets
Actions
28. Category Management UI

URL:

/admin/categories

Fitur:

Create
Edit
Activate / Deactivate
Search

Table:

Category
Description
Status
Total Tickets
Actions
29. SLA Management UI

URL:

/admin/sla

Table:

SLA
Service
Target
Response Time
Resolution Time
Status
Actions

Form:

Service
[ Select Service ]


SLA Name
[____________________]


Response Time
[ 30 ] minutes


Resolution Time
[ 480 ] minutes


Effective Date
[ DD/MM/YYYY ]


Review Date
[ DD/MM/YYYY ]


Status
[ Active ▼ ]


[ Cancel ] [ Save SLA ]
30. SOP Management UI

URL:

/admin/sop

Admin dapat:

Upload SOP.
View SOP.
Download SOP.
Edit metadata.
Activate/deactivate SOP.

Table:

Document Number
SOP Name
Service
Effective Date
Review Date
Status
Actions
31. SLA Review UI

URL:

/admin/sla-reviews

Form:

Service
[ Select Service ]


SLA
[ Select SLA ]


Review Date
[ DD/MM/YYYY ]


Findings
[________________________]


Issues
[________________________]


Recommendations
[________________________]


Follow Up
[________________________]


[ Save Review ]
32. User Management UI

URL:

/admin/users

Table:

Name
Email
Role
Status
Total Tickets
Created At
Actions

Admin dapat:

Search.
Filter.
View user.
Activate/deactivate user.
33. Notification UI

Notification icon:

🔔

Notifikasi:

Ticket berhasil dibuat.
Ticket telah diverifikasi.
Ticket telah ditugaskan.
Ticket sedang diproses.
Ticket mendekati SLA.
Ticket melewati SLA.
Ticket telah diselesaikan.
Ticket telah ditutup.

Unread:

●
34. Profile UI

User dan Admin memiliki:

Name
Email
Role
Account Status

Action:

Edit Profile
Change Password
Logout
35. Design System
Typography

Gunakan font sans-serif modern, misalnya:

Inter

Hierarchy:

H1: 32px
H2: 24px
H3: 20px
Body: 14-16px
Caption: 12-13px
36. Color System
Primary

Digunakan untuk:

Primary button.
Link.
Active navigation.
Highlight.
Success
Resolved
Closed
Compliant
Active
Warning
Pending
Near Deadline
SLA Warning
Danger
Overdue
Breached
Rejected
Error
Neutral
Draft
Inactive
Disabled
Information

Status tidak boleh hanya dibedakan berdasarkan warna.

Contoh:

✓ COMPLIANT
! WARNING
× OVERDUE
37. Components

Komponen UI:

Button
Input
Textarea
Select
Search
Date Picker
File Upload
Badge
Status Badge
Priority Badge
Card
Table
Pagination
Modal
Dropdown
Toast
Alert
Tabs
Breadcrumb
Progress Bar
Timeline
Empty State
Skeleton Loading
38. Empty State

Jika belum ada ticket:

┌─────────────────────────────┐
│                             │
│       [ Illustration ]      │
│                             │
│ Belum ada ticket            │
│                             │
│ Buat ticket jika Anda       │
│ membutuhkan bantuan.        │
│                             │
│       [Buat Ticket]         │
└─────────────────────────────┘
39. Loading State

Gunakan skeleton loading pada:

Dashboard.
Ticket list.
Ticket detail.
Table.
SLA monitoring.
40. Error State
┌─────────────────────────────┐
│ Terjadi kesalahan            │
│                             │
│ Data tidak dapat dimuat.    │
│                             │
│       [ Coba Lagi ]         │
└─────────────────────────────┘
41. Confirmation Modal

Digunakan untuk:

Delete.
Close ticket.
Reject ticket.
Deactivate service.
Deactivate user.

Contoh:

┌────────────────────────────────────┐
│ Konfirmasi                         │
├────────────────────────────────────┤
│ Apakah Anda yakin ingin menutup    │
│ ticket ini?                        │
│                                    │
│ [ Batal ]           [ Ya, Tutup ]  │
└────────────────────────────────────┘
42. Toast

Success:

✓ Ticket berhasil dibuat.

Warning:

! Ticket mendekati batas SLA.

Error:

× Gagal menyimpan data.
43. Responsive Design

UI harus mendukung:

Mobile
Tablet
Desktop

Breakpoint:

Mobile:
< 768px


Tablet:
768px - 1023px


Desktop:
>= 1024px

Table pada mobile berubah menjadi card.

44. Accessibility

UI harus mempertimbangkan:

Kontras warna.
Label input.
Keyboard navigation.
Focus state.
Alt text.
Error message.
Status tidak hanya menggunakan warna.

Contoh:

❌ hanya:
🔴


✅:
🔴 OVERDUE
45. User Flow
User
Login
  ↓
Dashboard
  ↓
Buat Ticket
  ↓
Pilih Layanan
  ↓
Isi Permasalahan
  ↓
Submit
  ↓
Ticket Created
  ↓
Monitor Ticket
  ↓
Ticket Resolved
  ↓
Feedback
  ↓
Closed
Admin
Login
  ↓
Admin Dashboard
  ↓
Monitor Ticket
  ↓
Verify Ticket
  ↓
Assign Admin
  ↓
Process Ticket
  ↓
Monitor SLA
  ↓
Resolve Ticket
  ↓
User Confirmation
  ↓
Ticket Closed
46. Permission Matrix
Feature	User	Admin
Dashboard	✓	✓
Create Ticket	✓	-
View Own Ticket	✓	✓
View All Ticket	-	✓
Assign Ticket	-	✓
Change Ticket Status	Limited	✓
Services	-	✓
Categories	-	✓
SLA	Related	✓
SOP	View	✓
SLA Review	-	✓
User Management	-	✓
Feedback	✓	View
Notifications	✓	✓
47. MVP Screens
Authentication
1. Login
User
2. Dashboard
3. Ticket List
4. Create Ticket
5. Ticket Detail
6. Notifications
7. Profile
Admin
8. Dashboard
9. Ticket Management
10. Ticket Detail
11. Services
12. Categories
13. SLA
14. Users
15. Notifications
16. Profile

Total:

16 screens minimum
48. UI-to-Database Mapping
UI	Database
Login	users
User Dashboard	tickets
Ticket List	tickets
Create Ticket	tickets
Ticket Detail	tickets
Timeline	ticket_histories
Assignment	ticket_assignments
Attachment	attachments
Feedback	feedbacks
Service	digital_services
Category	categories
SLA	sla_rules
SOP	sop_documents
SLA Review	sla_reviews
Notification	notifications
Audit	audit_logs
49. Laravel UI Structure

Implementasi UI menggunakan:

resources/
│
├── views/
│   │
│   ├── layouts/
│   │   ├── app.blade.php
│   │   ├── user.blade.php
│   │   └── admin.blade.php
│   │
│   ├── auth/
│   │   └── login.blade.php
│   │
│   ├── user/
│   │   ├── dashboard.blade.php
│   │   ├── tickets/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   └── show.blade.php
│   │   ├── notifications.blade.php
│   │   └── profile.blade.php
│   │
│   └── admin/
│       ├── dashboard.blade.php
│       ├── tickets/
│       ├── services/
│       ├── categories/
│       ├── sla/
│       ├── sop/
│       ├── reviews/
│       ├── users/
│       ├── notifications/
│       └── profile/
│
└── components/
    ├── button.blade.php
    ├── badge.blade.php
    ├── card.blade.php
    ├── modal.blade.php
    ├── table.blade.php
    ├── alert.blade.php
    ├── toast.blade.php
    └── timeline.blade.php
50. UI Priority
P0 — MVP
Login
User Dashboard
Admin Dashboard
Ticket List
Create Ticket
Ticket Detail
Ticket Management
SLA Monitoring
P1 — Important
Service Management
Category Management
SLA Management
Notifications
Feedback
User Management
P2 — Development
SOP Management
SLA Review
Advanced Analytics
Uptime Monitoring
51. Definition of Done

UI dianggap selesai apabila:

 Login berfungsi.
 User dapat melihat dashboard.
 User dapat membuat ticket.
 User dapat melihat ticket.
 User dapat melihat detail ticket.
 Timeline tersedia.
 User dapat memberikan feedback.
 Admin dapat melihat dashboard.
 Admin dapat melihat seluruh ticket.
 Admin dapat melakukan assignment.
 Admin dapat mengubah status.
 SLA dapat dimonitor.
 SLA warning tersedia.
 SLA overdue tersedia.
 Service management tersedia.
 Category management tersedia.
 SLA management tersedia.
 User management tersedia.
 Notification tersedia.
 Responsive.
 Loading state tersedia.
 Empty state tersedia.
 Error state tersedia.
 Confirmation modal tersedia.
 Form validation tersedia.
52. Project Documentation Structure

Dokumentasi project:

docs/
│
├── prd.md
│   ├── Product Requirement
│   ├── Functional Requirement
│   ├── User Role
│   ├── Business Flow
│   ├── UI/UX Requirement
│   ├── User Flow
│   ├── Screen Requirement
│   └── Definition of Done
│
└── database.md
    ├── Database Requirement
    ├── Table
    ├── Column
    ├── Relationship
    ├── Index
    └── Constraint

Dengan struktur ini, tidak perlu ui.prd.md lagi.

prd.md menjadi dokumen utama yang menjelaskan apa yang harus dibuat, siapa yang menggunakan, bagaimana alurnya, dan seperti apa UI-nya, sedangkan database.md khusus menjelaskan bagaimana data sistem disimpan.

53. Document Information
Document:
prd.md


Version:
1.0.0


Status:
Draft


Project:
Sistem Fasilitas Dukungan Pengguna & Monitoring SLA


Roles:
User
Admin


Backend:
Laravel


Frontend:
Blade + Tailwind CSS


Database:
PostgreSQL