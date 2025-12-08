import { TikTokLiveConnection , WebcastEvent } from 'tiktok-live-connector';
 
import { createLogger } from "./logger.js";
// const axios = require("axios");
const username = process.argv[2];
const livestream_id = process.argv[3];
const jwt_token = process.argv[4];
const logger = createLogger(livestream_id);

if (!username || !livestream_id || !jwt_token) {
    console.error("Usage: node listener.js <username> <livestream_id> <jwt_token>");
    process.exit(1);
}

logger.info("Listener started...");
logger.info(`Username: ${username}`);
logger.info(`Stream ID: ${livestream_id}`);

const client = new TikTokLiveConnection(username);

 
client.connect().then(state => {
    console.log("Connected to room:", state.roomId);
    logger.info(`Connected to room ID: ${state.roomId}`);
}
)
.catch(err => {
    console.error("Connection failed:", err);
    process.exit(1);
});

client.on(WebcastEvent.CHAT, async(data )=> {

    //console.log(data.user.uniqueId + ": " + data.comment);
    console.log(data);
    const commentData = {
        author_name: data.uniqueId,
        comment: data.comment,
        userId: data.userId,
        commentId: data.commentId,          // معرف التعليق
        createtime: data.createTime,         // وقت التعليق (Unix timestamp)
        avatar: data.profilePictureUrl,
        livestream_id:livestream_id,
    };
    // await axios.post("http://localhost/api/tiktok/comment", {
    //     username: event.uniqueId,
    //     comment: event.comment
    // });
    try {
        await axios.post(
            "https://zawed.ae/api/live/tiktok/fetchcomment",
            commentData,
            { headers: { Authorization: `Bearer ${jwt_token}` } }
        );
    } catch (e) {

        console.error("Error sending comment:", e.message);
        logger.error("Error sending comment:" + e.message);
    }
});
