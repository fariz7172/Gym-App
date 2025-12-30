# Gym-App Management System

Aplikasi Manajemen Gym berbasis Laravel 10.

## Fitur Utama

-   **Manajemen Member**: Pendaftaran, status membership, cetak kartu member (QR Code).
-   **Membership**: Paket membership, durasi, dan renewal.
-   **Attendance**: Check-in/Check-out member menggunakan QR Code atau manual.
-   **Reporting**: Laporan pendapatan, kedatangan, dan data member.
-   **Role Based Access**: Super Admin, Branch Admin, dan Staff.

## Persyaratan Sistem

-   PHP >= 8.1
-   Composer
-   Node.js & NPM
-   MySQL / MariaDB

## Cara Instalasi (Untuk Pengguna Lain/Clone)

Ikuti langkah-langkah ini untuk menjalankan aplikasi di komputer lokal Anda:

1.  **Clone Repository**
    ```bash
    git clone https://github.com/fariz7172/Gym-App.git
    cd Gym-App
    ```

2.  **Install Dependencies**
    ```bash
    composer install
    npm install
    ```

3.  **Konfigurasi Environment**
    -   Duplikat file `.env.example` menjadi `.env`.
    -   Sesuaikan konfigurasi database di file `.env`:
    ```ini
    DB_DATABASE=gym_db
    DB_USERNAME=root
    DB_PASSWORD=
    ```

4.  **Generate App Key**
    ```bash
    php artisan key:generate
    ```

5.  **Database Migration & Seeding**
    ```bash
    php artisan migrate --seed
    ```

6.  **Setup Storage Link**
    ```bash
    php artisan storage:link
    ```

7.  **Build Assets**
    ```bash
    npm run build
    ```

8.  **Jalankan Server**
    ```bash
    php artisan serve
    ```
    Buka `http://127.0.0.1:8000` di browser.

---

## Cara Menyimpan Perubahan ke GitHub (Untuk Pemilik)

Jika Anda telah melakukan perubahan pada kode dan ingin menyimpannya ke GitHub:

1.  **Inisialisasi Git (Jika belum)**
    ```bash
    git init
    git branch -M main
    git remote add origin https://github.com/fariz7172/Gym-App.git
    ```

2.  **Simpan Perubahan**
    ```bash
    git add .
    git commit -m "Update fitur terbaru"
    ```

3.  **Upload ke GitHub**
    ```bash
    git push -u origin main
    ```
    *Catatan: Jika diminta login, masukkan username GitHub dan Personal Access Token (bukan password akun).*

## Struktur Folder Penting

-   `app/Http/Controllers`: Logika backend.
-   `app/Models`: Model database.
-   `resources/views`: Tampilan (Blade templates).
-   `routes/web.php`: Definisi routing aplikasi.
