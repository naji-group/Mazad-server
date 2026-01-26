
import puppeteer from 'puppeteer';
import axios from 'axios';
import https from 'https';
import { createLogger } from "./logger.js";

const livestream_id = process.argv[2];
const jwt_token = process.argv[3];
const live_url = process.argv[4];

// const livestream_id = 10;
// const jwt_token = "ascascsc";
// const live_url = 'https://jaco.live/@TheStage_Sa?rid=23260509978787841&source=discover&tab=other';
const logger = createLogger(livestream_id);
const agent = new https.Agent({
  rejectUnauthorized: false
});
const statisticData = {
  livestream_id: livestream_id,
};

(async () => {
  const browser = await puppeteer.launch({
    headless: "new",
    args: [
      '--no-sandbox',
      '--disable-setuid-sandbox',
      '--disable-dev-shm-usage',
      '--disable-gpu'
    ]
  });

  const page = await browser.newPage();

  await page.setUserAgent(
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120 Safari/537.36'
  );

  // دالة تستقبل التعليقات من داخل الصفحة
  await page.exposeFunction('saveComment', async (text) => {
    console.log('تعليق:', text);
    logger.info(` New commnet:  ${comment}`);
  });
  await page.goto(live_url, {
    waitUntil: 'domcontentloaded',
    timeout: 0
  });

  console.log('Livepage oppend');
  await new Promise(r => setTimeout(r, 50000));
  console.log('wait done');
  await new Promise(r => setTimeout(r, 50000));
  console.log('wait done');
  await new Promise(r => setTimeout(r, 50000));
  console.log('wait done');
  await new Promise(r => setTimeout(r, 50000));
  console.log('wait done');
  await new Promise(r => setTimeout(r, 50000));
  console.log('wait done');
  // اطبع كل HTML الصفحة
  // const html = await page.content();
  // console.log(html);
  // logger.info(html);
  // page.setDefaultNavigationTimeout(0);
  const comments = await page.evaluate(() => {
    //console.log('evaluate');
    // logger.info('evaluate');
    return Array.from(document.querySelectorAll('.comment-user')).map(c => {

      return {
        username: c.querySelector('.comment-nickname')?.innerText.trim(),
        message: c.querySelector('.comment-desc-con')?.innerText.trim(),
        time: c.querySelector('.comment-desc-time')?.innerText.trim()
      };
    });
  });

  console.log(comments);
  comments.forEach(async comment => {
    const timestampMs = Date.now();

    commentData = {
      author_name: comment.username,
      comment: comment.message,
      createtime: comment.time,
      commentId: timestampMs,         // معرف التعليق

      livestream_id: livestream_id,
    };
    await sendStatisticData(statisticData);
  });

  // مراقبة التعليقات الجديدة
  await page.evaluate(() => {
    const container = document.querySelector('.comment-area');


    if (!container) {
      console.error('❌ comment-area غير موجود');
      return;
    }
    // document.querySelectorAll('.comment').forEach(c => {
    //   console.log(c.innerText);
    //   logger.info(`💬 Comments : ${comment}`);
    //   window.saveComment(c.innerText);
    // });
    const observer = new MutationObserver(mutations => {
      mutations.forEach(mutation => {
        mutation.addedNodes.forEach(async node => {
          if (!node.classList || !node.classList.contains('comment-user')) return;

          const nickname = node.querySelector('.comment-nickname')?.innerText.trim();
          const message = node.querySelector('.comment-desc-con')?.innerText.trim();
          const time = node.querySelector('.comment-desc-time')?.innerText.trim();
          const avatar = node.querySelector('.comment-avatar')?.src;
          const likes = node.querySelector('.comment-like-num')?.innerText.trim();
          console.log(nickname);
          if (message) {

            commentData = {
              author_name: nickname,
              comment: message,
              createtime: new Date().toISOString(),
              commentId: new Date().toISOString(),         // معرف التعليق

              livestream_id: livestream_id,
            };
            await sendStatisticData(commentData);
            window.saveComment({
              nickname,
              message,
              time,
              avatar,
              likes,
              created_at: new Date().toISOString()
            });
          }
        });
      });
    });

    observer.observe(container, {
      childList: true,
      subtree: false
    });

    console.log('👀 بدأ مراقبة التعليقات...');
  });
})();
async function sendStatisticData(statisticData) {
  try {

    const response = await axios.post(
      "https://zawed.ae/api/jaco/fetchcomment",
      statisticData,
      {
        headers: { Authorization: `Bearer ${jwt_token}` },
        httpsAgent: agent // استخدام الوكيل لهذا الطلب فقط
      }
    );

    //  console.log("✅ Statistic sent successfully:", response.status);
    if (response.data.sent > 0) {
      return 1;
    } else {
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
