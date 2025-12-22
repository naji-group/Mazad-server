import winston from "winston";
import path from "path";
import fs from "fs";
 
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const logDir = path.join(__dirname, "logs");
//const logDir = "logs";

// إنشاء المجلد إذا لم يكن موجودًا
if (!fs.existsSync(logDir)) {
    fs.mkdirSync(logDir, { recursive: true });
}

export function createLogger(livestreamId = "default",source="tik") {
    const logFile = path.join(logDir, `listener_${livestreamId}_${source}.log`);

    return winston.createLogger({
        level: "info",
        format: winston.format.combine(
            winston.format.timestamp({ format: "YYYY-MM-DD HH:mm:ss" }),
            winston.format.printf(info => `${info.timestamp} [${info.level}]: ${info.message}`)
        ),
        transports: [
            new winston.transports.File({ filename: logFile }),
            new winston.transports.Console()
        ]
    });
}
