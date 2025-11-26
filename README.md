# Sistem Perhitungan WP (Weighted Product)

Sistem perhitungan WP untuk mendukung pengambilan keputusan menggunakan metode Weighted Product.

## Fitur

- ✅ Multi-step form untuk input data
- ✅ CRUD (Create, Read, Update, Delete) untuk analisis
- ✅ Perhitungan WP otomatis
- ✅ Penyimpanan hasil ke database
- ✅ Tampilan hasil dengan ranking
- ✅ Halaman kelola analisis

## Instalasi

### 1. Setup Database

1. Buka phpMyAdmin atau MySQL client
2. Import file `database.sql` untuk membuat database dan tabel
3. Atau jalankan perintah SQL berikut:

```sql
mysql -u root -p < database.sql
```

### 2. Konfigurasi Database

Edit file `config.php` dan sesuaikan dengan konfigurasi database Anda:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'spk_wp');
```

### 3. Akses Website

1. Pastikan XAMPP/WAMP/LAMP sudah berjalan
2. Akses melalui browser:
   - `http://localhost/spk_wp/` - Halaman utama (buat analisis baru)
   - `http://localhost/spk_wp/manage.php` - Kelola analisis (list, edit, delete)
   - `http://localhost/spk_wp/view.php?id=1` - Lihat detail analisis

## Struktur Database

### Tabel `analisis`
- Menyimpan informasi dasar analisis (judul, metode)

### Tabel `alternatif`
- Menyimpan alternatif yang akan dinilai

### Tabel `kriteria`
- Menyimpan kriteria penilaian dengan bobot dan tipe (benefit/cost)

### Tabel `nilai`
- Menyimpan nilai setiap alternatif pada setiap kriteria

### Tabel `hasil`
- Menyimpan hasil perhitungan WP dan ranking

## Cara Menggunakan

### Membuat Analisis Baru

1. Buka `index.php`
2. Isi judul analisis di Step 1
3. Tambahkan alternatif di Step 2 (minimal 2)
4. Tambahkan kriteria dengan bobot di Step 3 (pastikan total bobot = 1)
5. Isi nilai untuk setiap alternatif di Step 4
6. Klik "Hitung" untuk melihat hasil

### Mengelola Analisis

1. Buka `manage.php` untuk melihat daftar semua analisis
2. Klik "Edit" untuk mengubah analisis
3. Klik "Lihat" untuk melihat detail dan hasil
4. Klik "Hapus" untuk menghapus analisis

## API Endpoints

### GET `/api/analisis.php`
- Tanpa parameter: Mendapatkan semua analisis
- Dengan `?id=1`: Mendapatkan analisis dengan ID tertentu

### POST `/api/analisis.php`
- Membuat analisis baru

### PUT `/api/analisis.php`
- Mengupdate analisis yang sudah ada

### DELETE `/api/analisis.php?id=1`
- Menghapus analisis

### POST `/api/hasil.php`
- Menyimpan hasil perhitungan WP

### GET `/api/hasil.php?analisis_id=1`
- Mendapatkan hasil perhitungan untuk analisis tertentu

## Metode Perhitungan WP

1. **Normalisasi Nilai**
   - Benefit: `nilai / max(nilai)`
   - Cost: `min(nilai) / nilai`

2. **Perhitungan WP**
   - `WP = ∏(normalized_value^bobot)`

3. **Ranking**
   - Diurutkan berdasarkan nilai WP tertinggi

## Teknologi

- PHP 7.4+
- MySQL/MariaDB
- JavaScript (Vanilla)
- HTML5 & CSS3

## Lisensi

Dibuat untuk Kelompok WP - UIN Siber Syekh Nurjati Cirebon

