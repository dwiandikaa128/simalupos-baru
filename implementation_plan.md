# Implementation Plan - Fitur Simalu Membership (Customer Balance & WA Notification)

Mengimplementasikan fitur **Simalu Membership** pada sistem POS Kopi Simalu (`kopi_simalu/CASHIER`), yang meliputi pencatatan saldo deposit pelanggan, pembayaran menggunakan saldo membership, penyimpanan uang kembalian ke saldo, notifikasi otomatis WhatsApp (berisi pengeluaran/pemasukan & sisa saldo ala BCA Mobile), ekspor laporan mutasi format PDF manual, serta halaman web kartu member digital (`/membership/{token}`).

---

## User Review Required

> [!IMPORTANT]
> **Skrip WA Bot Microservice (Local Node.js)**
> Notifikasi WhatsApp dikirimkan melalui service Node.js gratisan (berbasis Baileys/`whatsapp-web.js`) yang berjalan di server lokal / PC Kasir pada port `3000`. Kita akan menyediakan folder `wa-bot/` beserta skrip Node.js dan panduan cara menjalankan `npm start` & scan QR code WA. Jika WA bot sedang tidak aktif/offline, Laravel akan secara otomatis mencatat pesan di log tanpa membatalkan transaksi POS kasir.

---

## Open Questions

- Tidak ada open questions saat ini (semua kebutuhan nama fitur, notifikasi ala BCA mobile, & ekspor PDF manual sudah disepakati).

---

## Proposed Changes

### Database Layer

#### [NEW] [2026_08_12_000001_create_customers_table.php](file:///d:/kopi_simalu/CASHIER/database/migrations/2026_08_12_000001_create_customers_table.php)
- Membuat tabel `customers` (`id`, `name`, `phone`, `balance`, `unique_token`, `status`, `timestamps`).

#### [NEW] [2026_08_12_000002_create_customer_mutations_table.php](file:///d:/kopi_simalu/CASHIER/database/migrations/2026_08_12_000002_create_customer_mutations_table.php)
- Membuat tabel `customer_mutations` (`id`, `customer_id`, `order_id`, `type`, `amount`, `balance_before`, `balance_after`, `notes`, `created_by`, `timestamps`).

#### [NEW] [2026_08_12_000003_add_customer_fields_to_orders_table.php](file:///d:/kopi_simalu/CASHIER/database/migrations/2026_08_12_000003_add_customer_fields_to_orders_table.php)
- Menambahkan kolom `customer_id`, `paid_by_membership`, `change_to_membership` pada tabel `orders`.

---

### Backend Models & Services

#### [NEW] [Customer.php](file:///d:/kopi_simalu/CASHIER/app/Models/Customer.php)
- Eloquent Model untuk `Customer` beserta relasi `mutations()` & `orders()`, dan helper `generateUniqueToken()`.

#### [NEW] [CustomerMutation.php](file:///d:/kopi_simalu/CASHIER/app/Models/CustomerMutation.php)
- Eloquent Model untuk `CustomerMutation` beserta relasi ke `Customer`, `Order`, dan `User` (kasir).

#### [MODIFY] [Order.php](file:///d:/kopi_simalu/CASHIER/app/Models/Order.php)
- Menambahkan `customer_id`, `paid_by_membership`, `change_to_membership` ke fillable & relasi `customer()`.

#### [NEW] [CustomerMembershipService.php](file:///d:/kopi_simalu/CASHIER/app/Services/CustomerMembershipService.php)
- Layanan bisnis untuk manajemen saldo membership: `topUp()`, `payWithBalance()`, `saveChangeToBalance()`, dan trigger notifikasi WA.

#### [NEW] [WhatsAppNotificationService.php](file:///d:/kopi_simalu/CASHIER/app/Services/WhatsAppNotificationService.php)
- Layanan integrasi ke WA Bot Microservice (POST ke `http://localhost:3000/send-message`).

#### [NEW] [SendWaNotificationJob.php](file:///d:/kopi_simalu/CASHIER/app/Jobs/SendWaNotificationJob.php)
- Laravel Job untuk pengiriman notifikasi WA di background secara async agar checkout POS tetap super kencang.

---

### Controllers & Routes

#### [NEW] [Admin/CustomerController.php](file:///d:/kopi_simalu/CASHIER/app/Http/Controllers/Admin/CustomerController.php)
- Controller Admin: Manajemen data member, formulir tambah/edit, detail mutasi, manual top-up, & download PDF laporan mutasi.

#### [MODIFY] [Pos/PosController.php](file:///d:/kopi_simalu/CASHIER/app/Http/Controllers/Pos/PosController.php)
- Penambahan endpoint pencarian member cepat (`GET /pos/customers/search`), pendaftaran member instan (`POST /pos/customers/store`), integrasi metode pembayaran `simalu_membership`, dan simpan kembalian ke saldo.

#### [NEW] [Public/MembershipController.php](file:///d:/kopi_simalu/CASHIER/app/Http/Controllers/Public/MembershipController.php)
- Halaman kartu member digital & mutasi saldo publik via URL `/membership/{token}`.

#### [MODIFY] [web.php](file:///d:/kopi_simalu/CASHIER/routes/web.php)
- Mendaftarkan rute-rute baru untuk Admin Member Management, POS API Endpoints, Export PDF, dan Halaman Web Member Publik.

---

### UI / Views

#### [NEW] [admin/customers/index.blade.php](file:///d:/kopi_simalu/CASHIER/resources/views/admin/customers/index.blade.php)
- Tampilan daftar member Simalu Membership dengan statistik total saldo & opsi top-up.

#### [NEW] [admin/customers/show.blade.php](file:///d:/kopi_simalu/CASHIER/resources/views/admin/customers/show.blade.php)
- Detail profil member, riwayat mutasi, tombol Top-Up, dan tombol **Download Rekapan PDF**.

#### [NEW] [pdf/customer-mutations.blade.php](file:///d:/kopi_simalu/CASHIER/resources/views/pdf/customer-mutations.blade.php)
- Template PDF resmi laporan mutasi pengeluaran & penambahan saldo member (siap dikirim manual ke pelanggan).

#### [NEW] [public/membership.blade.php](file:///d:/kopi_simalu/CASHIER/resources/views/public/membership.blade.php)
- Tampilan kartu member digital responsif mobile dengan QR Code, saldo real-time, & riwayat transaksi.

#### [MODIFY] [sidebar-admin.blade.php](file:///d:/kopi_simalu/CASHIER/resources/views/components/sidebar-admin.blade.php)
- Menambahkan menu **Simalu Membership** di sidebar Admin.

#### [MODIFY] [pos/index.blade.php](file:///d:/kopi_simalu/CASHIER/resources/views/pos/index.blade.php)
- Menambahkan modal/dropdown pencarian member, indikator saldo member terpilih, metode pembayaran `Simalu Membership`, dan checkbox `Simpan Kembalian ke Saldo`.

---

### Microservice WA Bot (Node.js)

#### [NEW] [wa-bot/package.json](file:///d:/kopi_simalu/CASHIER/wa-bot/package.json) & [wa-bot/index.js](file:///d:/kopi_simalu/CASHIER/wa-bot/index.js)
- Server Express + Baileys / `whatsapp-web.js` sederhana yang menerima POST request berisi `{ phone, message }` dan menyunting ke format WA.

---

## Verification Plan

### Automated Tests
- Menjalankan `php artisan test` untuk memastikan seluruh fitur yang ada tidak broken.

### Manual Verification
1. **Migration & DB Verification**: Run `php artisan migrate` dan pastikan tabel `customers`, `customer_mutations`, serta perubahan pada `orders` berhasil terbuat.
2. **Admin Membership Management**: Uji tambah member baru, lihat detail member, jalankan Top-Up manual, dan unduh laporan PDF.
3. **POS Checkout & Saldo**:
   - Cari member pada modal checkout POS.
   - Lakukan transaksi pembayaran menggunakan **Simalu Membership**. Pastikan saldo terpotong dengan benar & mutasi tercatat.
   - Lakukan transaksi tunai lalu centang **Simpan Kembalian ke Saldo**. Pastikan saldo bertambah & mutasi tercatat.
4. **Halaman Publik `/membership/{token}`**: Buka link di browser & pastikan kartu digital serta saldo tampil dengan indah & responsif.
5. **WA Bot Integration**: Jalankan microservice di `wa-bot/` atau simulasi log payload notifikasi pesan ala BCA Mobile.
