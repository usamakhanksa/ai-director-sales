/**
 * LinkedIn Search Scraper
 * 
 * Searches LinkedIn for people based on keywords and extracts profile information
 * including contact details when publicly available.
 * 
 * Requires a valid LinkedIn session cookie (li_at) for authentication.
 */

"use strict";

const puppeteer = require("puppeteer");
const db = require("../config/database");

/**
 * Wait for selector with timeout
 */
async function waitForSelector(page, selector, timeout = 10000) {
  try {
    return await page.waitForSelector(selector, { timeout });
  } catch (error) {
    console.warn(`Timeout waiting for selector: ${selector}`);
    return null;
  }
}

/**
 * Scroll to bottom of page to load dynamic content
 */
async function scrollToBottom(page) {
  await page.evaluate(() => {
    const scrollStep = () => {
      window.scrollBy(0, 500);
      const totalHeight = document.body.scrollHeight;
      const currentPos = window.pageYOffset + window.innerHeight;
      
      if (currentPos < totalHeight) {
        setTimeout(scrollStep, 100);
      }
    };
    scrollStep();
  });
  
  await page.waitForTimeout(2000); // Wait for content to load after scrolling
}

/**
 * Check if session is expired
 */
async function checkSessionStatus(page) {
  // Check for common indicators of session expiration
  const sessionExpiredIndicators = [
    'Sign in', 'Log in', 'Join now', 'Welcome', 'Get started'
  ];
  
  const pageText = await page.evaluate(() => document.body.textContent || '');
  
  for (const indicator of sessionExpiredIndicators) {
    if (pageText.includes(indicator)) {
      return true;
    }
  }
  
  // Check for login redirect
  const currentUrl = page.url();
  if (currentUrl.includes('login') || currentUrl.includes('checkpoint')) {
    return true;
  }
  
  return false;
}

/**
 * Extract search results from the page
 */
async function extractSearchResults(page, maxResults = 10) {
  try {
    const results = await page.evaluate((max) => {
      const profileItems = [];
      // Selectors for LinkedIn search results
      const resultElements = document.querySelectorAll('.search-result__wrapper, .reusable-search__result-container, .entity-result');
      
      for (let i = 0; i < resultElements.length && profileItems.length < max; i++) {
        const element = resultElements[i];
        
        try {
          // Extract profile link
          let profileLink = element.querySelector('a.app-aware-link')?.href || '';
          if (!profileLink) {
            const anchor = element.querySelector('a[href*="/in/"]');
            if (anchor) {
              profileLink = anchor.href;
            }
          }
          
          if (!profileLink) continue; // Skip if no profile link found
          
          // Extract name
          const nameElement = element.querySelector('span.entity-result__title-text a span') || 
                             element.querySelector('.actor-name span') ||
                             element.querySelector('[data-anonymize="actor-name"]');
          const fullName = nameElement ? nameElement.textContent.trim() : '';
          
          // Extract title/headline
          const titleElement = element.querySelector('.entity-result__primary-subtitle, .entity-result__headline, .subline-level-1');
          const title = titleElement ? titleElement.textContent.trim() : '';
          
          // Extract location
          const locationElement = element.querySelector('.entity-result__secondary-subtitle, .subline-level-2');
          const location = locationElement ? locationElement.textContent.trim() : '';
          
          // Extract description/summary
          const descriptionElement = element.querySelector('.entity-result__summary, .lt-line-clamp');
          const description = descriptionElement ? descriptionElement.textContent.trim() : '';
          
          // Extract photo
          const photoElement = element.querySelector('.entity-result__image, .ivm-image-wrapper');
          let photo = '';
          if (photoElement) {
            const img = photoElement.querySelector('img');
            photo = img ? img.src : '';
          }
          
          if (fullName || profileLink) {
            profileItems.push({
              fullName,
              title,
              location,
              description,
              profileUrl: profileLink,
              photo
            });
          }
        } catch (e) {
          console.warn('Error processing search result item:', e.message);
          continue;
        }
      }
      
      return profileItems;
    }, maxResults);
    
    return results;
  } catch (error) {
    console.error('Error extracting search results:', error.message);
    return [];
  }
}

/**
 * Extract contact information from a profile page
 */
async function extractContactInfoFromProfile(page) {
  try {
    // Try to open contact info modal
    const contactInfoButton = await page.$('button.pv-contact-info__contact-type.modal-trigger');
    if (contactInfoButton) {
      await contactInfoButton.click();
      await page.waitForTimeout(2000); // Wait for modal to load
      
      const contactData = await page.evaluate(() => {
        const contactInfo = {};
        
        // Look for contact info in the modal
        const contactModal = document.querySelector('.artdeco-modal');
        if (contactModal) {
          const contactItems = contactModal.querySelectorAll('.pv-contact-info__ci-container dl.pv-contact-info__item');
          for (const item of contactItems) {
            const label = item.querySelector('dt.pv-contact-info__ci-label');
            const value = item.querySelector('dd.pv-contact-info__ci-value span');
            
            if (label && value) {
              const labelText = label.textContent.trim().toLowerCase();
              const valueText = value.textContent.trim();
              
              if (labelText.includes('email')) {
                contactInfo.email = valueText;
              } else if (labelText.includes('phone') || labelText.includes('mobile')) {
                contactInfo.phone = valueText;
              } else if (labelText.includes('websites') || labelText.includes('website')) {
                contactInfo.website = valueText;
              }
            }
          }
        }
        
        return contactInfo;
      });
      
      // Close the modal
      const closeModalButton = await page.$('button.artdeco-close-container');
      if (closeModalButton) {
        await closeModalButton.click();
      }
      
      return contactData;
    } else {
      // If no contact info button, return empty object
      return {};
    }
  } catch (error) {
    console.error('Error extracting contact info from profile:', error.message);
    return {};
  }
}

/**
 * Main run function for the LinkedIn search scraper
 */
async function run(jobId, keyword, config = {}, hooks = {}, maxResults = 10) {
  const {
    logEvent = () => {},
    updateProgress = () => {},
    isCancelled = () => false,
  } = hooks;

  const sessionCookieValue = config.sessionCookieValue;
  
  if (!sessionCookieValue) {
    throw new Error('sessionCookieValue is required for LinkedIn search scraping');
  }

  if (!keyword) {
    throw new Error('keyword is required for LinkedIn search scraping');
  }

  // Launch Puppeteer browser
  const browser = await puppeteer.launch({
    headless: true, // Set to false for debugging
    args: [
      '--no-sandbox',
      '--disable-setuid-sandbox',
      '--disable-dev-shm-usage',
      '--disable-accelerated-2d-canvas',
      '--no-first-run',
      '--no-zygote',
      '--disable-gpu'
    ]
  });

  try {
    const page = await browser.newPage();
    
    // Set the LinkedIn session cookie
    await page.setCookie({
      name: 'li_at',
      value: sessionCookieValue,
      domain: '.www.linkedin.com',
      path: '/',
      httpOnly: true,
      secure: true
    });
    
    // Set viewport and user agent
    await page.setViewport({ width: 1920, height: 1080 });
    
    await logEvent("INFO", `Starting LinkedIn search for keyword: "${keyword}"`);
    
    // Navigate to LinkedIn and perform search
    await page.goto('https://www.linkedin.com/search/', { 
      waitUntil: 'networkidle2',
      timeout: 30000 
    });
    
    // Check if session is still valid
    if (await checkSessionStatus(page)) {
      throw new Error('SessionExpired');
    }
    
    // Find and fill the search input
    const searchInput = await waitForSelector(page, 'input.search-global-typeahead__input', 10000);
    if (!searchInput) {
      // Try alternative selectors
      const alternativeInputs = [
        'input[placeholder*="Search"]',
        'input.search-global-typeahead__input',
        '#global-typeahead-search-input'
      ];
      
      for (const selector of alternativeInputs) {
        const input = await page.$(selector);
        if (input) {
          searchInput = input;
          break;
        }
      }
      
      if (!searchInput) {
        throw new Error('Could not find search input field');
      }
    }
    
    await searchInput.type(keyword);
    await searchInput.press('Enter');
    
    // Wait for search results to load
    await page.waitForSelector('.search-results-container, .search-result__wrapper, .reusable-search__result-container', { timeout: 15000 });
    
    // Wait a bit more for results to fully load
    await page.waitForTimeout(3000);
    
    // Extract initial search results
    let searchResults = await extractSearchResults(page, maxResults);
    await logEvent("INFO", `Found ${searchResults.length} initial search results`);
    
    // For each result, visit the profile to extract contact info if available
    const finalResults = [];
    let processedCount = 0;
    
    for (const result of searchResults) {
      if (isCancelled()) break;
      
      await logEvent("INFO", `Processing profile: ${result.fullName} (${processedCount + 1}/${searchResults.length})`);
      
      try {
        // Visit the profile page
        await page.goto(result.profileUrl, { 
          waitUntil: 'networkidle2',
          timeout: 30000 
        });
        
        // Check if session is still valid
        if (await checkSessionStatus(page)) {
          throw new Error('SessionExpired');
        }
        
        // Wait for profile to load
        await page.waitForSelector('.pv-top-card--fade-in-enabled', { timeout: 10000 });
        
        // Extract contact information from the profile
        const contactInfo = await extractContactInfoFromProfile(page);
        
        // Combine profile info with contact info
        const completeResult = {
          ...result,
          email: contactInfo.email || null,
          phone: contactInfo.phone || null
        };
        
        // Save to the linkedin_search_results table
        await db("linkedin_search_results").insert({
          job_id: jobId,
          search_keyword: keyword,
          profile_url: completeResult.profileUrl,
          full_name: completeResult.fullName || '',
          title: completeResult.title || '',
          location: completeResult.location || '',
          description: completeResult.description || '',
          photo_url: completeResult.photo || '',
          email: completeResult.email,
          phone: completeResult.phone,
          raw_data: JSON.stringify(completeResult),
          scraped_at: new Date().toISOString()
        });
        
        finalResults.push(completeResult);
        
        await logEvent("INFO", `✓ Profile processed: ${completeResult.fullName} (Email: ${completeResult.email || 'N/A'}, Phone: ${completeResult.phone || 'N/A'})`);
        
        // Update progress
        processedCount++;
        const progress = (processedCount / searchResults.length) * 100;
        await updateProgress(progress, processedCount);
        
        // Add delay between profile visits to be respectful
        if (processedCount < searchResults.length && !isCancelled()) {
          await page.waitForTimeout(3000); // 3 second delay between profiles
        }
        
      } catch (error) {
        await logEvent("ERROR", `Failed to process profile ${result.profileUrl}: ${error.message}`);
        
        // If session expired, rethrow with specific error name
        if (error.message === 'SessionExpired') {
          throw error;
        }
        
        // Add the result without contact info if there was an error
        await db("linkedin_search_results").insert({
          job_id: jobId,
          search_keyword: keyword,
          profile_url: result.profileUrl,
          full_name: result.fullName || '',
          title: result.title || '',
          location: result.location || '',
          description: result.description || '',
          photo_url: result.photo || '',
          email: null,
          phone: null,
          raw_data: JSON.stringify(result),
          scraped_at: new Date().toISOString()
        });
        
        finalResults.push({
          ...result,
          email: null,
          phone: null
        });
      }
    }
    
    await logEvent("INFO", `LinkedIn search completed. Found ${finalResults.length} profiles with contact info.`);
    
  } finally {
    await browser.close();
  }
}

module.exports = { run };