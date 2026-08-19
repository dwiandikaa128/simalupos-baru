const { makeWASocket, useMultiFileAuthState, DisconnectReason } = require('@whiskeysockets/baileys');
const express = require('express');
const qrcode = require('qrcode-terminal');

const app = express();
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

const PORT = process.env.PORT || 3000;
let sock = null;
let latestQr = null;
let botStatus = 'connecting';
let botUser = null;

async function connectToWhatsApp() {
    console.log('🔄 Memulai koneksi WhatsApp Bot Simalu...');
    botStatus = 'connecting';
    const { state, saveCreds } = await useMultiFileAuthState('auth_info_baileys');

    sock = makeWASocket({
        auth: state,
        printQRInTerminal: true,
        browser: ['Simalu POS', 'Chrome', '1.0.0'],
    });

    sock.ev.on('creds.update', saveCreds);

    sock.ev.on('connection.update', (update) => {
        const { connection, lastDisconnect, qr } = update;

        if (qr) {
            latestQr = qr;
            botStatus = 'qr_ready';
            console.log('📸 Scan QR Code dibawah ini atau via Admin Panel:');
            qrcode.generate(qr, { small: true });
        }

        if (connection === 'close') {
            const shouldReconnect = (lastDisconnect?.error?.output?.statusCode !== DisconnectReason.loggedOut);
            console.log('⚠️ Koneksi terputus. Mencoba reconnect:', shouldReconnect);
            latestQr = null;
            botStatus = 'disconnected';
            botUser = null;
            if (shouldReconnect) {
                connectToWhatsApp();
            }
        } else if (connection === 'open') {
            latestQr = null;
            botStatus = 'connected';
            botUser = sock?.user || { id: 'WhatsApp Bot' };
            console.log('✅ WA Bot Simalu BERHASIL TERHUBUNG & SIAP MEMPROSES NOTIFIKASI!');
        }
    });
}

// Endpoint GET untuk status WA Bot & QR string
app.get('/status', (req, res) => {
    return res.json({
        success: true,
        status: botStatus,
        qr: latestQr,
        user: botUser,
    });
});

// Endpoint POST untuk logout WA Bot
app.post('/logout', async (req, res) => {
    try {
        if (sock) {
            await sock.logout();
            sock = null;
        }
        latestQr = null;
        botStatus = 'disconnected';
        botUser = null;
        setTimeout(() => connectToWhatsApp(), 2000);
        return res.json({ success: true, message: 'Berhasil logout WA Bot.' });
    } catch (e) {
        return res.status(500).json({ success: false, error: e.message });
    }
});

// Endpoint POST untuk menerima pesan dari Laravel POS
app.post('/send-message', async (req, res) => {
    try {
        const { phone, message } = req.body;

        if (!phone || !message) {
            return res.status(400).json({ success: false, message: 'Phone dan message wajib diisi.' });
        }

        if (!sock || botStatus !== 'connected') {
            return res.status(503).json({ success: false, message: 'WA Bot belum terhubung atau sedang inisialisasi.' });
        }

        // Format nomor ke format internasional (628xxx)
        let cleanedPhone = phone.replace(/[^0-9]/g, '');
        if (cleanedPhone.startsWith('08')) {
            cleanedPhone = '628' + cleanedPhone.substring(2);
        } else if (cleanedPhone.startsWith('8')) {
            cleanedPhone = '62' + cleanedPhone;
        }

        let jid = `${cleanedPhone}@s.whatsapp.net`;

        // Query ke server WhatsApp untuk cek eksistensi nomor & dapatkan JID resmi
        try {
            const [waUser] = await sock.onWhatsApp(cleanedPhone);
            if (waUser && waUser.exists) {
                jid = waUser.jid;
            } else {
                console.warn(`⚠️ Nomor ${cleanedPhone} tidak terdeteksi aktif di server WhatsApp, mencoba kirim ke ${jid}`);
            }
        } catch (e) {
            console.warn(`⚠️ Gagal verifikasi onWhatsApp untuk ${cleanedPhone}: ${e.message}`);
        }

        await sock.sendMessage(jid, { text: message });
        console.log(`📩 Notifikasi berhasil terkirim ke WA ${cleanedPhone} (JID: ${jid})`);

        return res.json({ success: true, message: 'Notifikasi WA berhasil dikirim.' });
    } catch (error) {
        console.error('❌ Gagal mengirim pesan WA:', error);
        return res.status(500).json({ success: false, error: error.message });
    }
});

app.listen(PORT, () => {
    console.log(`🚀 WA Bot HTTP Service berjalan di http://localhost:${PORT}`);
    connectToWhatsApp();
});
