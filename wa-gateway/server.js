const express = require('express');
const { default: makeWASocket, useMultiFileAuthState, DisconnectReason, fetchLatestBaileysVersion } = require('@whiskeysockets/baileys');
const pino = require('pino');
const fs = require('fs');
const path = require('path');
const crypto = require('crypto');

const app = express();
app.use(express.json());

const PORT = process.env.PORT || 3000;
const API_KEY = process.env.WA_GATEWAY_KEY || 'default-secret-key-change-me';
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

// GET /health & GET /status
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

// GET /qr
router.get('/qr', (req, res) => {
    res.json({
        qr: qrCodeString,
    });
});

// POST /send
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

// POST /logout
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

// Mount router under root and /wa-api for cPanel compatibility
app.use('/', router);
app.use('/wa-api', router);

connectToWhatsApp();

app.listen(PORT, () => {
    console.log(`WhatsApp Gateway berjalan di port ${PORT}`);
});
