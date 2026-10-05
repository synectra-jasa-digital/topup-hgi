// Fail-closed: tanpa API key yang kuat, gateway tidak boleh berjalan.
// Fallback rahasia default membuat endpoint /send & /logout terbuka tebak-tebakan.
// Diletakkan sebelum require apa pun supaya cek dieksekusi lebih dulu.
const API_KEY = process.env.WA_GATEWAY_KEY;
if (!API_KEY || API_KEY.length < 16) {
    console.error('FATAL: WA_GATEWAY_KEY belum di-set atau terlalu pendek (min. 16 karakter).');
    console.error('Contoh: $env:WA_GATEWAY_KEY="<acak-32-byte-base64>"; node server.js');
    process.exit(1);
}

const express = require('express');
const { default: makeWASocket, useMultiFileAuthState, DisconnectReason, fetchLatestBaileysVersion } = require('@whiskeysockets/baileys');
const pino = require('pino');
const fs = require('fs');
const path = require('path');
const crypto = require('crypto');

const app = express();
app.use(express.json());

const PORT = process.env.PORT || 3000;
const AUTH_DIR = path.join(__dirname, 'auth');

let sock = null;
let qrCodeString = null;
let isConnected = false;
let pairedPhone = null;
let lastSeen = null;

function timingSafeEqualStr(a, b) {
    if (typeof a !== 'string' || typeof b !== 'string') return false;
    const bufA = Buffer.from(a);
    const bufB = Buffer.from(b);
    if (bufA.length !== bufB.length) return false;
    return crypto.timingSafeEqual(bufA, bufB);
}

// API Key Middleware
app.use((req, res, next) => {
    const key = req.headers['x-api-key'];
    if (!key || !timingSafeEqualStr(key, API_KEY)) {
        return res.status(401).json({ success: false, code: 'unauthorized', error: 'Invalid API key' });
    }
    next();
});

const router = express.Router();

const healthHandler = (req, res) => {
    res.json({
        connected: isConnected,
        status: isConnected ? 'connected' : (qrCodeString ? 'waiting_qr' : 'disconnected'),
        phone: pairedPhone,
        last_seen: lastSeen,
    });
};
router.get('/health', healthHandler);
router.get('/status', healthHandler);

router.get('/qr', (req, res) => {
    res.json({
        qr: qrCodeString,
    });
});

router.post('/send', async (req, res) => {
    if (!isConnected || !sock) {
        return res.status(503).json({ success: false, code: 'not_connected', error: 'WhatsApp Gateway tidak terhubung' });
    }

    const { phone, message } = req.body;
    if (!phone || !message) {
        return res.status(400).json({ success: false, code: 'invalid_params', error: 'Phone dan message wajib diisi' });
    }

    const formatted = phone.startsWith('62') ? phone : (phone.startsWith('0') ? '62' + phone.slice(1) : '62' + phone);
    const jid = `${formatted}@s.whatsapp.net`;

    try {
        const [onWa] = await sock.onWhatsApp(jid);
        if (!onWa || !onWa.exists) {
            return res.status(400).json({ success: false, code: 'invalid_number', error: 'Nomor tidak terdaftar di WhatsApp' });
        }

        const sent = await sock.sendMessage(onWa.jid, { text: message });
        return res.json({
            success: true,
            messageId: sent.key.id,
        });
    } catch (err) {
        console.error('Send Error:', err);
        return res.status(500).json({ success: false, code: 'send_failed', error: err.message });
    }
});

router.post('/logout', async (req, res) => {
    try {
        if (sock) {
            await sock.logout();
        }
        if (fs.existsSync(AUTH_DIR)) {
            fs.rmSync(AUTH_DIR, { recursive: true, force: true });
        }
        isConnected = false;
        pairedPhone = null;
        qrCodeString = null;
        setTimeout(connectToWhatsApp, 3000);
        return res.json({ success: true });
    } catch (err) {
        return res.status(500).json({ success: false, error: err.message });
    }
});

// Mount router under /, /api, and /wa-api for cPanel compatibility
app.use('/', router);
app.use('/api', router);
app.use('/wa-api', router);

async function connectToWhatsApp() {
    try {
        if (!fs.existsSync(AUTH_DIR)) {
            fs.mkdirSync(AUTH_DIR, { recursive: true, mode: 0o700 });
        }

        const { state, saveCreds } = await useMultiFileAuthState(AUTH_DIR);

        let version = [2, 3000, 1015901307];
        try {
            const fetched = await fetchLatestBaileysVersion();
            if (fetched && fetched.version) {
                version = fetched.version;
            }
        } catch (e) {
            console.log('Bypass fetch version, menggunakan default versi Baileys');
        }

        console.log('Menghubungkan ke WhatsApp Baileys...');

        sock = makeWASocket({
            version,
            auth: state,
            logger: pino({ level: 'silent' }),
            printQRInTerminal: true,
        });

        sock.ev.on('creds.update', saveCreds);

        sock.ev.on('connection.update', (update) => {
            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                qrCodeString = qr;
                isConnected = false;
                console.log('QR Code WhatsApp baru siap discan!');
            }

            if (connection === 'close') {
                isConnected = false;
                qrCodeString = null;
                const shouldReconnect = (lastDisconnect?.error?.output?.statusCode !== DisconnectReason.loggedOut);
                console.log('Koneksi WA terputus. Reconnect dalam 5s:', shouldReconnect);
                if (shouldReconnect) {
                    setTimeout(connectToWhatsApp, 5000);
                }
            } else if (connection === 'open') {
                isConnected = true;
                qrCodeString = null;
                lastSeen = new Date().toISOString();
                pairedPhone = sock.user?.id ? sock.user.id.split(':')[0] : null;
                console.log('✓ WhatsApp Gateway BERHASIL Terhubung! Nomor:', pairedPhone);
            }
        });
    } catch (err) {
        console.error('Error saat inisialisasi WA:', err);
        setTimeout(connectToWhatsApp, 5000);
    }
}

connectToWhatsApp();

app.listen(PORT, () => {
    console.log(`✓ WhatsApp Gateway berjalan di port ${PORT}`);
});
