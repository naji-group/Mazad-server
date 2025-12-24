import WebSocket from 'ws';
import { createLogger } from "./logger.js";
import axios from 'axios';
import https from 'https';

const livestream_id = process.argv[2];
 
 const ACCESS_TOKEN = process.argv[3];
//const jwt_token = process.argv[4];



const logger = createLogger(livestream_id,"rs");
if (!ACCESS_TOKEN) {
  console.error('RESTREAM_TOKEN is missing');
  process.exit(1);
}

const url = `wss://chat.api.restream.io/ws?accessToken=${ACCESS_TOKEN}`;
let ws;

function connect() {
  console.log('Connecting to Restream WebSocket...');

  ws = new WebSocket(url);

  ws.on('open', () => {
    console.log('✅ Connected to Restream');
  });

  ws.on('message', (data) => {
    try {
      const action = JSON.parse(data.toString());
      console.log('📩 Message:', action);
  logger.info(`data to room comment: ${data}`);
      // 🔹 هنا يمكنك:
      // - إرسال التعليق إلى Laravel API
      // - بثه عبر Redis / Socket.IO
    } catch (e) {
        logger.error("Error sending comment:" + e.message);
      console.error('JSON parse error:', e.message);
    }
  });

  ws.on('close', () => {
    logger.error("Error sending comment:" + e.message);
    console.warn('❌ Connection closed, reconnecting...');
    setTimeout(connect, 5000);
  });

  ws.on('error', (err) => {
    logger.error("Error sending comment:" + e.message);
    console.error('WebSocket error:', err.message);
  });
}

connect();
