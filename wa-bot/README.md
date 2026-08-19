# 📲 Simalu WhatsApp Bot Microservice (Gratisan)

Skrip ini adalah microservice Node.js ringan yang menggunakan library `@whiskeysockets/baileys` (100% Gratis & Open Source) untuk mengirimkan notifikasi otomatis transaksi **Simalu Membership** ala BCA Mobile.

---

## 🛠️ Cara Menjalankan di Komputer Kasir / Server

1. **Masuk ke folder `wa-bot`**:
   ```bash
   cd wa-bot
   ```

2. **Install Dependencies**:
   ```bash
   npm install
   ```

3. **Jalankan Microservice**:
   ```bash
   npm start
   ```

4. **Scan QR Code**:
   * Saat pertama kali dijalankan, QR Code akan muncul di terminal/command prompt.
   * Buka WhatsApp di HP (Nomor khusus Toko/Membership) -> **Perangkat Tertaut (Linked Devices)** -> **Tautkan Perangkat** -> Scan QR Code tersebut.

5. **Selesai!**
   * Service akan berjalan secara otomatis di `http://localhost:3000`.
   * Setiap kali ada transaksi/top-up di Kasir POS Simalu, notifikasi WA real-time akan dikirimkan otomatis ke pelanggan.
