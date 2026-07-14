const { createContext, createBrowser, closeBrowser, humanClick, humanType, setupRequestInterception } = require('../services/browserEngine');

/**
 * Scrapes Instagram profile data for a given username
 * @param {string} username Instagram username to scrape
 * @returns {Object} Profile data including bio, followers, following, posts, etc.
 */
async function scrapeInstagramProfile(username) {
  const browser = await createBrowser({ headless: true, blockMedia: true });
  let context, page;

  try {
    ({ context, page } = await createContext(browser, {}, true));
    await setupRequestInterception(page);

    // Navigate to Instagram profile
    await page.goto(`https://www.instagram.com/${username}/`, { 
      waitUntil: 'networkidle', 
      timeout: 30000 
    });

    // Wait for content to load
    await page.waitForSelector('header section ul li span', { timeout: 10000 });

    // Extract basic profile info
    const profileData = await page.evaluate(() => {
      const fullNameEl = document.querySelector('header section h1');
      const bioEl = document.querySelector('header section h1 + div');
      const postsCountEl = document.querySelectorAll('header section ul li span')[0];
      const followersEl = document.querySelectorAll('header section ul li span')[1];
      const followingEl = document.querySelectorAll('header section ul li span')[2];
      
      return {
        fullName: fullNameEl ? fullNameEl.textContent : '',
        biography: bioEl ? bioEl.textContent : '',
        posts: postsCountEl ? parseInt(postsCountEl.textContent.replace(/,/g, '')) : 0,
        followers: followersEl ? parseInt(followersEl.textContent.replace(/,/g, '')) : 0,
        following: followingEl ? parseInt(followingEl.textContent.replace(/,/g, '')) : 0,
        isPrivate: document.querySelector('article') === null && document.querySelector('header img') !== null
      };
    });

    // Extract recent posts if public
    if (!profileData.isPrivate) {
      const posts = [];
      const postLinks = await page.$$('.ySN3v a'); // Links to individual posts
      
      for (let i = 0; i < Math.min(postLinks.length, 12); i++) { // Get first 12 posts
        try {
          const postLink = postLinks[i];
          await postLink.click();
          
          // Wait for post modal to open
          await page.waitForSelector('.QBdPU', { timeout: 5000 });
          
          const postData = await page.evaluate(() => {
            const captionEl = document.querySelector('.Fr22J span');
            const likesEl = document.querySelector('.Nm9Fw span');
            const commentsEl = document.querySelector('.Mr508 span');
            
            return {
              caption: captionEl ? captionEl.textContent : '',
              likes: likesEl ? parseInt(likesEl.textContent.replace(/,/g, '')) : 0,
              comments: commentsEl ? parseInt(commentsEl.textContent.replace(/,/g, '')) : 0,
              url: window.location.href
            };
          });
          
          posts.push(postData);
          
          // Close the post modal
          await page.click('button svg[aria-label="Close"]');
          await page.waitForTimeout(1000);
        } catch (e) {
          console.warn(`Could not extract post ${i + 1}:`, e.message);
          continue;
        }
      }
      
      profileData.postsData = posts;
    }

    return profileData;
  } catch (error) {
    console.error(`Error scraping Instagram profile for ${username}:`, error.message);
    throw error;
  } finally {
    await closeBrowser(browser);
  }
}

/**
 * Scrapes multiple Instagram profiles
 * @param {string[]} usernames Array of Instagram usernames to scrape
 * @param {Function} onProgress Callback function to report progress
 * @returns {Array} Array of profile data objects
 */
async function scrapeInstagramProfiles(usernames, onProgress = null) {
  const results = [];
  
  for (let i = 0; i < usernames.length; i++) {
    const username = usernames[i];
    
    try {
      console.log(`Scraping Instagram profile: ${username} (${i + 1}/${usernames.length})`);
      
      const profileData = await scrapeInstagramProfile(username);
      results.push({
        username,
        data: profileData,
        scrapedAt: new Date().toISOString()
      });
      
      // Report progress
      if (onProgress) {
        onProgress(i + 1, usernames.length, username);
      }
      
      // Random delay between requests to avoid rate limiting
      await new Promise(resolve => setTimeout(resolve, 2000 + Math.random() * 3000));
    } catch (error) {
      console.error(`Failed to scrape Instagram profile for ${username}:`, error.message);
      results.push({
        username,
        error: error.message,
        scrapedAt: new Date().toISOString()
      });
      
      if (onProgress) {
        onProgress(i + 1, usernames.length, username, error.message);
      }
    }
  }
  
  return results;
}

module.exports = {
  scrapeInstagramProfile,
  scrapeInstagramProfiles
};