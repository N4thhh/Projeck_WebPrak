# MaWallet - Aplikasi Pengelola Keuangan Pribadi

## Deskripsi Proyek
MaWallet adalah aplikasi web yang dirancang sebagai pendamping keuangan pribadi Anda. Aplikasi ini membantu Anda melacak pendapatan, mengelola pengeluaran, dan mendapatkan wawasan yang jelas tentang kebiasaan belanja Anda melalui ringkasan dan grafik sederhana. Dengan MaWallet, pencatatan transaksi harian menjadi lebih mudah, terstruktur, dan dapat diakses di mana saja.

Sistem ini bertujuan untuk menyediakan alat yang intuitif bagi pengguna untuk mengontrol keuangan mereka, mengidentifikasi tren pengeluaran, dan mengelola berbagai sumber dana (dompet) dalam satu platform terpusat.

## Fitur Utama
- **Autentikasi Pengguna**: Sistem registrasi dan login yang aman untuk melindungi data keuangan setiap pengguna.
- **Dashboard Finansial**: Halaman utama yang menampilkan ringkasan total saldo, total pemasukan, dan total pengeluaran bulan ini.
- **Manajemen Dompet (Wallets)**: Pengguna dapat menambah dan mengelola berbagai sumber dana seperti tunai, rekening bank, atau dompet digital.
- **Manajemen Kategori**: Membuat kategori pemasukan (misal: Gaji, Bonus) dan pengeluaran (misal: Makanan, Transportasi) untuk analisis yang lebih terperinci.
- **Pencatatan Transaksi**: Mencatat semua pemasukan dan pengeluaran dengan detail seperti deskripsi, tanggal, dompet sumber, dan kategori yang relevan.
- **Riwayat Transaksi**: Melihat seluruh riwayat transaksi untuk melacak arus kas dari waktu ke waktu.
- **Grafik Pengeluaran**: Visualisasi data pengeluaran dalam bentuk grafik untuk membantu pengguna memahami tren belanja bulanan.
- **Manajemen Pengguna**: Fitur untuk menambah, melihat, mengedit, dan menghapus data pengguna yang terdaftar di dalam sistem.

## Teknologi yang Digunakan
- **Frontend**: HTML, CSS, JavaScript.
- **Backend**: PHP.
- **Database**: MySQL.
- **Lainnya**: TailwindCSS (untuk styling), Chart.js (untuk grafik), Lucide Icons (untuk ikon).

## Cara Penggunaan

1.  **Instalasi & Konfigurasi**:
    * Unduh atau clone repositori ini ke server lokal Anda (misalnya, menggunakan XAMPP).
    * Buat sebuah database baru di phpMyAdmin atau panel kontrol database lainnya dengan nama `mawallet_db`.
    * Impor file `mawallet_db.sql` ke dalam database yang telah Anda buat untuk membuat struktur tabel yang diperlukan.
    * Buka file `includes/db.php` dan sesuaikan kredensial database jika diperlukan (standarnya adalah user `root` tanpa password).
    * Buka file `includes/config.php` dan atur variabel `$base_url` sesuai dengan URL proyek di server lokal Anda (contoh: `http://localhost/Projeck_WebPrak`).

2.  **Sebagai Pengguna**:

    * **Registrasi & Login**:
        * Buka aplikasi di browser Anda.
        * Buat akun baru melalui halaman registrasi atau masuk jika sudah memiliki akun.
    * **Kelola Dompet & Kategori**:
        * Sebelum mencatat transaksi, buka menu "Wallets" untuk menambahkan sumber dana Anda (misal: Dompet Tunai, Rekening BNI).
        * Selanjutnya, buka menu "Categories" untuk membuat kategori yang sesuai dengan kebiasaan finansial Anda. Aplikasi akan memberikan peringatan jika belum ada kategori atau dompet saat akan menambah transaksi.
    * **Catat Transaksi**:
        * Gunakan menu "Incomes" atau "Expenses" untuk menambahkan catatan pemasukan atau pengeluaran baru.
        * Isi semua detail yang diperlukan seperti jumlah, deskripsi, dompet, dan kategori.
    * **Pantau Keuangan**:
        * Kembali ke "Dashboard" untuk melihat ringkasan finansial terkini.
        * Buka menu "History" untuk melihat riwayat lengkap dari semua transaksi yang telah Anda catat.

## Kontributor
*(Bagian ini dapat diisi dengan nama-nama anggota tim proyek Anda)*
1.  [Bungaran Natanael Siahaan](https://github.com/N4thhh)
2.  [Aathifah Dihyan Calysta](https://github.com/aathifahdc)
3.  [Fernando Ramadhani](https://github.com/fernando-FTD)
4.  [Arjuna Gunatama Sihombing](https://github.com/RjunKece)
