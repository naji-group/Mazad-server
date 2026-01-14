
import axios from 'axios';
import https from 'https';
import { createLogger } from "./logger.js";
// const axios = require("axios");

const livestream_id = process.argv[2];
const jwt_token = process.argv[3];
const logger = createLogger(livestream_id);
const statisticData = {   
    livestream_id: livestream_id,
};

if ( !livestream_id || !jwt_token) {
    console.error("Usage: node listener.js <livestream_id> <jwt_token>");
    process.exit(1);
}
 
const agent = new https.Agent({
    rejectUnauthorized: false
});

/**
 *  
 * @param {Object} statisticData  
 */
async function sendStatisticData(statisticData) {
    try {
         
        const response = await axios.post(
            "https://zawed.ae/api/live/youtube/fetchcomment",
            statisticData,
            {
                headers: { Authorization: `Bearer ${jwt_token}` },
                httpsAgent: agent // استخدام الوكيل لهذا الطلب فقط
            }
        );
        
      //  console.log("✅ Statistic sent successfully:", response.status);
      if (response.data.sent > 0) {
        return 1;
      }else{
        process.exit(1);
      }
         
        
    } catch (error) {
       // console.error("❌ Error sending statistic:", error.message);
        logger.error("Error sending statistic: " + error.message);
        
        // يمكنك إعادة رمي الخطأ أو إرجاع null
     //   throw error; // أو return null;
     return 0;
    }
}



 
async function runfetchLoop(statisticData) {
    try {
        console.log("✅ Statistic sent successfully:");
        await sendStatisticData(statisticData);
    } catch (e) {
        logger.error("Loop error: " + e.message);
    } finally {
        setTimeout(() => runfetchLoop(statisticData), 10 * 1000);
    }
}

runfetchLoop(statisticData);