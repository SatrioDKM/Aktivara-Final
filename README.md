# Aplikasi Manajemen Properti & Operasional (ManproApp)

## 1. Pendahuluan

**ManproApp** adalah sebuah aplikasi web modern yang dirancang untuk mengelola operasional harian sebuah properti atau gedung. Aplikasi ini memfasilitasi alur kerja antara manajerial, leader tim, dan staff lapangan untuk memastikan semua tugas dapat didistribusikan, dikerjakan, dan dilaporkan secara efisien dan transparan.

Fokus utama aplikasi ini adalah sistem **Job Pool (Papan Tugas)**, di mana leader dapat membuat daftar tugas yang kemudian dapat diambil oleh staff yang tersedia berdasarkan sistem "siapa cepat, dia dapat". Ini memastikan tidak ada pekerjaan yang tumpang tindih dan semua sumber daya dimanfaatkan secara optimal.

---

## 2. Fitur Utama

Aplikasi ini dilengkapi dengan serangkaian fitur komprehensif untuk mendukung operasional properti:

-   **Manajemen Pengguna & Hak Akses (Role-Based Access Control):** Sistem peran yang terstruktur (Superadmin, Manager, Leader, Staff) untuk memastikan setiap pengguna hanya bisa mengakses fitur yang relevan dengan pekerjaannya.
-   **Manajemen Data Master:** CRUD (Create, Read, Update, Delete) yang lengkap untuk data-data inti seperti Gedung, Lantai, Ruangan, Jenis Tugas, dan Aset.
-   **Alur Kerja Tugas (Job Pool):**
    -   Leader membuat tugas yang tersedia untuk departemennya.
    -   Staff melihat "Papan Tugas" dan dapat mengklaim pekerjaan.
    -   Sistem penguncian tugas untuk mencegah klaim ganda (menggunakan _database transaction_).
    -   Staff melaporkan hasil pekerjaan dengan deskripsi dan bukti lampiran (foto/video).
    -   Leader mereview laporan dan menyetujui atau menolak pekerjaan.
-   **Manajemen Aset:**
    -   Pencatatan semua aset properti, termasuk lokasi, status, dan stok.
    -   Alur kerja **Maintenance Aset**, di mana laporan kerusakan secara otomatis membuat tugas baru untuk tim Teknisi.
-   **Dashboard Analitik Berbasis Peran:**
    -   **Admin/Manager:** Melihat gambaran umum seluruh sistem (statistik tugas, aset, pengguna) dalam bentuk kartu dan grafik.
    -   **Leader:** Melihat statistik performa timnya.
    -   **Staff:** Melihat ringkasan pekerjaan personal mereka.
-   **Sistem Notifikasi Multi-Channel:**
    -   **Notifikasi In-App:** Ikon lonceng di navigasi yang menampilkan notifikasi real-time.
    -   **Notifikasi Telegram:** Pemberitahuan instan melalui bot Telegram untuk aksi-aksi penting (tugas baru, tugas diambil, laporan dikirim, stok menipis).
-   **Ekspor Data:** Fitur untuk mengekspor data (contoh: daftar aset) ke format Excel untuk kebutuhan laporan offline atau arsip.

---

## 3. Teknologi yang Digunakan

Aplikasi ini dibangun menggunakan tumpukan teknologi modern yang efisien dan skalabel:

-   **Backend:**
    -   PHP 8.2+
    -   Laravel 11
    -   Maatwebsite/Excel (untuk fitur ekspor)
    -   Laravel Notification Channels (untuk notifikasi Telegram)
-   **Frontend:**
    -   Blade
    -   Tailwind CSS 3
    -   Alpine.js 3
    -   Chart.js (untuk grafik di dashboard)
-   **Database:** MySQL
-   **Arsitektur:** Hybrid (kombinasi arsitektur tradisional Laravel dan API-driven untuk interaktivitas tinggi tanpa reload halaman).
-   **Development Tool:** Vite

---

## 4. Panduan Instalasi & Setup

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di lingkungan lokal Anda.

1.  **Clone Repository**

    ```bash
    git clone [URL_REPOSITORY_ANDA]
    cd manpro-app
    ```

2.  **Install Dependensi**
    Pastikan Anda memiliki Composer dan NPM terinstal.

    ```bash
    # Install dependensi PHP
    composer install

    # Install dependensi JavaScript
    npm install
    ```

3.  **Setup Environment File**
    Salin file `.env.example` menjadi `.env`.

    ```bash
    cp .env.example .env
    ```

    Buka file `.env` dan konfigurasikan variabel berikut:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=manpro_db  # Pastikan Anda sudah membuat database ini
    DB_USERNAME=root       # Sesuaikan dengan username Anda
    DB_PASSWORD=           # Sesuaikan dengan password Anda

    # Dapatkan dari BotFather di Telegram
    TELEGRAM_BOT_TOKEN=YOUR_TELEGRAM_BOT_TOKEN
    ```

4.  **Generate Application Key**

    ```bash
    php artisan key:generate
    ```

5.  **Jalankan Migrasi & Seeder**
    Perintah ini akan membuat semua tabel di database dan mengisinya dengan data awal (peran, pengguna, lokasi, dll).

    ```bash
    php artisan migrate:fresh --seed
    ```

6.  **Buat Storage Link**
    Ini penting agar file yang di-upload (foto profil, lampiran laporan) bisa diakses dari web.

    ```bash
    php artisan storage:link
    ```

7.  **Jalankan Server Pengembangan**
    Buka dua terminal:

    -   **Terminal 1 (Vite):**
        ```bash
        npm run dev
        ```
    -   **Terminal 2 (Server PHP):**
        ```bash
        php artisan serve
        ```

8.  **Akses Aplikasi**
    Buka browser Anda dan navigasikan ke `http://127.0.0.1:8000`.

---

## 5. Struktur & Penjelasan Aplikasi

### A. Rincian Peran Pengguna (Roles)

| Kode Peran | Nama Peran | Hak Akses Utama                                                          |
| :--------- | :--------- | :----------------------------------------------------------------------- |
| **SA00**   | Superadmin | Akses penuh ke seluruh sistem, termasuk manajemen pengguna.              |
| **MG00**   | Manager    | Melihat dashboard global, semua data master, dan semua alur kerja.       |
| **xx01**   | Leader     | (e.g., HK01, TK01) Membuat tugas untuk departemennya & mereview laporan. |
| **xx02**   | Staff      | (e.g., HK02, TK02) Mengambil & mengerjakan tugas, serta membuat laporan. |

### B. Rincian Tabel Database (Tables)

| Nama Tabel                   | Kegunaan Utama                                                                                       |
| :--------------------------- | :--------------------------------------------------------------------------------------------------- |
| **users**                    | Menyimpan data pengguna, termasuk `role_id` dan `telegram_chat_id`.                                  |
| **roles**                    | Menyimpan daftar peran pengguna.                                                                     |
| **buildings, floors, rooms** | Menyimpan data master lokasi fisik.                                                                  |
| **assets**                   | Menyimpan daftar semua aset, lokasi, dan statusnya.                                                  |
| **task_types**               | Menyimpan kategori atau jenis pekerjaan yang bisa dibuat.                                            |
| **tasks**                    | **Tabel Inti.** Menyimpan daftar pekerjaan, siapa yang membuat, siapa yang mengambil, dan statusnya. |
| **daily_reports**            | Menyimpan laporan hasil pekerjaan yang dikirim oleh Staff.                                           |
| **report_attachments**       | Menyimpan path file bukti dokumentasi untuk setiap laporan.                                          |
| **assets_maintenances**      | Mencatat riwayat perbaikan atau perawatan aset.                                                      |
| **notifications**            | Menyimpan data notifikasi yang akan ditampilkan kepada pengguna.                                     |

### C. Struktur Folder Aplikasi

```
manpro-app/
├── app/
│   ├── Exports/           # Kelas untuk ekspor data (Maatwebsite/Excel)
│   ├── Http/
│   │   ├── Controllers/   # Semua controller aplikasi
│   │   └── Middleware/    # Middleware kustom (CheckRole.php)
│   ├── Models/            # Model Eloquent untuk setiap tabel
│   └── Notifications/     # Kelas notifikasi (In-App & Telegram)
├── bootstrap/
│   └── app.php            # Pendaftaran middleware & route API
├── database/
│   ├── migrations/        # Skema database
│   └── seeders/           # Data awal untuk database
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│       ├── admin/         # View untuk fitur administratif (Manajemen Pengguna)
│       ├── layouts/       # Layout utama (app.blade.php, navigation.blade.php)
│       ├── maintenance/   # View untuk maintenance aset
│       ├── master/        # View untuk semua data master
│       └── tasks/         # View untuk alur kerja tugas
├── routes/
│   ├── api.php            # Rute untuk endpoint API (JSON)
│   └── web.php            # Rute untuk menampilkan halaman (Blade)
└── ...
```

---

## 6. Kredensial Default

Setelah menjalankan `db:seed`, Anda dapat login menggunakan akun berikut:

-   **Superadmin:**
    -   **Email:** `superadmin@example.com`
    -   **Password:** `password`
-   **Manager:**
    -   **Email:** `manager@example.com`
    -   **Password:** `password`
-   **Leader Teknisi:**
    -   **Email:** `leader.tk@example.com`
    -   **Password:** `password`
-   **Staff Teknisi:**
    -   **Email:** `staff.tk.dodi@example.com`
    -   **Password:** `password`
