/**
 * LinkedIn Profile Scraper
 * 
 * Extracts detailed public profile information from LinkedIn profiles:
 * - Profile: name, title, location, picture, description and url
 * - Experiences: title, company name, location, duration, start date, end date and description
 * - Education: school name, degree name, start date and end date
 * - Volunteer experiences: title, company, description, start date and end date name
 * - Skills: name and endorsement count
 * - Certifications: certification, issuing organization, issue and expiry dates
 * - Contact Info: email and phone when publicly available
 * 
 * Requires a valid LinkedIn session cookie (li_at) for authentication.
 */

"use strict";

const puppeteer = require("puppeteer");
const db = require("../config/database");

// Regular expressions for parsing dates
const DATE_REGEX = /(\d{4})-(\d{2})-(\d{2})/;
const MONTH_YEAR_REGEX = /(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s+(\d{4})/g;
const MONTH_YEAR_RANGE_REGEX = /((Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s+\d{4})\s*[-–]\s*((Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s+\d{4}|Present)/g;

/**
 * Converts month name to number
 */
function monthToNumber(month) {
  const months = {
    'Jan': 1, 'Feb': 2, 'Mar': 3, 'Apr': 4, 'May': 5, 'Jun': 6,
    'Jul': 7, 'Aug': 8, 'Sep': 9, 'Oct': 10, 'Nov': 11, 'Dec': 12
  };
  return months[month] || 1;
}

/**
 * Parse date string to ISO format
 */
function parseDate(dateString) {
  if (!dateString) return null;
  
  // Try to match month year format like "Jan 2020"
  const monthYearMatch = dateString.match(/^(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s+(\d{4})$/);
  if (monthYearMatch) {
    const month = monthToNumber(monthYearMatch[1]);
    const year = parseInt(monthYearMatch[2]);
    return new Date(year, month - 1, 1).toISOString();
  }
  
  // Try to match YYYY-MM-DD format
  const isoMatch = dateString.match(DATE_REGEX);
  if (isoMatch) {
    return new Date(dateString).toISOString();
  }
  
  return null;
}

/**
 * Parse duration in days between two dates
 */
function calculateDuration(startDateStr, endDateStr) {
  if (!startDateStr) return 0;
  
  const startDate = parseDate(startDateStr);
  const endDate = endDateStr === 'Present' || endDateStr === 'Current' 
    ? new Date() 
    : parseDate(endDateStr);
    
  if (!startDate) return 0;
  
  const start = new Date(startDate);
  const end = endDate ? new Date(endDate) : new Date();
  
  // Calculate difference in days
  const diffTime = Math.abs(end - start);
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
}

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
 * Click "Show more" buttons to expand content sections
 */
async function expandSections(page) {
  try {
    // Click "Show more" buttons for experiences, education, etc.
    const showMoreSelectors = [
      'button[aria-label*="show more"]',
      'button[aria-label*="Show more"]',
      '.pv-skills-section button.pv-profile-section__see-more-inline',
      '.pv-profile-section .pv-profile-section__actions-container button',
      'button[data-control-name="position_see_more"]',
      'button[data-test-modal][data-test-single-photo-viewer-modal]',
      'button.pv-contact-info__contact-type.modal-trigger'
    ];
    
    for (const selector of showMoreSelectors) {
      try {
        const elements = await page.$$(selector);
        for (const element of elements) {
          try {
            await element.click().catch(() => {});
            await page.waitForTimeout(1000);
          } catch (e) {
            // Ignore errors when clicking
          }
        }
      } catch (e) {
        // Continue with other selectors
      }
    }
    
    // Expand skill endorsements
    const skillExpandButtons = await page.$$('.pv-skill-category-entity__featured-skill-button');
    for (const button of skillExpandButtons) {
      try {
        await button.click().catch(() => {});
        await page.waitForTimeout(500);
      } catch (e) {
        // Ignore errors
      }
    }
  } catch (e) {
    console.warn('Error expanding sections:', e.message);
  }
}

/**
 * Extract contact information from the profile
 */
async function extractContactInfo(page) {
  try {
    // Try to open contact info modal first
    const contactInfoButton = await page.$('button.pv-contact-info__contact-type.modal-trigger');
    if (contactInfoButton) {
      await contactInfoButton.click();
      await page.waitForTimeout(2000); // Wait for modal to load
      
      const contactData = await page.evaluate(() => {
        const contactInfo = {};
        
        // Look for email
        const emailElements = document.querySelectorAll('a[href^="mailto:"]');
        if (emailElements.length > 0) {
          contactInfo.email = emailElements[0].getAttribute('href').replace('mailto:', '');
        }
        
        // Look for phone numbers
        const phoneElements = document.querySelectorAll('a[href^="tel:"]');
        if (phoneElements.length > 0) {
          contactInfo.phone = phoneElements[0].getAttribute('href').replace('tel:', '');
        }
        
        // Alternative approach - look for contact info in the modal
        const contactModal = document.querySelector('.artdeco-modal');
        if (contactModal) {
          const contactItems = contactModal.querySelectorAll('.pv-contact-info__ci-container dl.pv-contact-info__item');
          for (const item of contactItems) {
            const label = item.querySelector('dt.pv-contact-info__ci-label');
            const value = item.querySelector('dd.pv-contact-info__ci-value');
            
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
      
      // Close the modal if it was opened
      const closeModalButton = await page.$('button.artdeco-close-container');
      if (closeModalButton) {
        await closeModalButton.click();
      }
      
      return contactData;
    } else {
      // If no contact info button, try alternative method
      const contactData = await page.evaluate(() => {
        const contactInfo = {};
        
        // Look for email in the page
        const emailRegex = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-z]{2,}$/g;
        const pageText = document.body.textContent || '';
        const emails = pageText.match(emailRegex);
        if (emails && emails.length > 0) {
          contactInfo.email = emails[0];
        }
        
        // Look for phone numbers in the page
        const phoneRegex = /(?:\+?(\d{1,3}))?[-.\s]?\(?(\d{3})\)?[-.\s]?(\d{3})[-.\s]?(\d{4})/g;
        const phones = pageText.match(phoneRegex);
        if (phones && phones.length > 0) {
          contactInfo.phone = phones[0];
        }
        
        return contactInfo;
      });
      
      return contactData;
    }
  } catch (error) {
    console.error('Error extracting contact info:', error.message);
    return {};
  }
}

/**
 * Extract profile basic information
 */
async function extractProfileInfo(page) {
  try {
    // Wait for profile header to load
    await waitForSelector(page, '.pv-top-card--fade-in-enabled', 15000);
    
    const profileData = await page.evaluate(() => {
      // Get name
      const nameElement = document.querySelector('h1.text-heading-xlarge');
      const fullName = nameElement ? nameElement.textContent.trim() : '';
      
      // Split name into first and last
      const nameParts = fullName.split(' ');
      const firstName = nameParts[0] || '';
      const lastName = nameParts.slice(1).join(' ') || '';
      
      // Get title/headline
      const titleElement = document.querySelector('.text-body-medium.break-words');
      const title = titleElement ? titleElement.textContent.trim() : '';
      
      // Get location
      const locationElement = document.querySelector('span.text-body-small.inline.t-black--light');
      const location = locationElement ? locationElement.textContent.trim() : '';
      
      // Get photo
      const photoElement = document.querySelector('.pv-top-card__photo-container img');
      const photo = photoElement ? photoElement.src : '';
      
      // Get description/about section
      const descriptionElement = document.querySelector('.pv-shared-text-with-see-more span');
      const description = descriptionElement ? descriptionElement.textContent.trim() : '';
      
      // Get industry
      let industry = '';
      const industryElements = document.querySelectorAll('section.pv-about-section dd.core-rail');
      for (const element of industryElements) {
        const text = element.textContent.trim();
        if (text && !text.includes('...') && text.length < 100) {
          industry = text;
          break;
        }
      }
      
      return {
        fullName,
        firstName,
        lastName,
        title,
        location,
        photo,
        description,
        industry
      };
    });
    
    return profileData;
  } catch (error) {
    console.error('Error extracting profile info:', error.message);
    return {};
  }
}

/**
 * Extract work experiences
 */
async function extractExperiences(page) {
  try {
    const experiences = await page.evaluate(() => {
      const experienceItems = [];
      const experienceElements = document.querySelectorAll('.pv-profile-section ul li');
      
      for (const element of experienceElements) {
        try {
          // Check if this is a position item
          const positionTitle = element.querySelector('.pv-entity__summary-info-v2 h3, .pv-entity__summary-info h3')?.textContent?.trim() || 
                               element.querySelector('.pv-entity__summary-info-v2 h2, .pv-entity__summary-info h2')?.textContent?.trim() || '';
          
          if (!positionTitle) continue; // Skip if no position title found
          
          const companyName = element.querySelector('.pv-entity__logo-img, .pv-entity__secondary-title')?.textContent?.trim() ||
                             element.querySelector('.pv-entity__firm-container')?.textContent?.trim() ||
                             element.querySelector('[data-field="company_name"]')?.textContent?.trim() || '';
          
          const datesDiv = element.querySelector('.pv-entity__date-range span:nth-child(2)');
          const datesText = datesDiv ? datesDiv.textContent.trim() : '';
          
          const locationDiv = element.querySelector('.pv-entity__location span:nth-child(2)');
          const location = locationDiv ? locationDiv.textContent.trim() : '';
          
          const descriptionDiv = element.querySelector('.pv-entity__description');
          const description = descriptionDiv ? descriptionDiv.textContent.trim() : '';
          
          // Parse dates
          let startDate = null;
          let endDate = null;
          let endDateIsPresent = false;
          
          if (datesText) {
            const dateParts = datesText.split('-');
            if (dateParts.length >= 2) {
              startDate = dateParts[0].trim();
              endDate = dateParts[1].trim();
              
              if (endDate.toLowerCase().includes('present') || endDate.toLowerCase().includes('current')) {
                endDateIsPresent = true;
              }
            }
          }
          
          experienceItems.push({
            title: positionTitle,
            company: companyName,
            location,
            startDate,
            endDate,
            endDateIsPresent,
            description,
            durationInDays: calculateDuration(startDate, endDateIsPresent ? 'Present' : endDate)
          });
        } catch (e) {
          continue; // Skip problematic items
        }
      }
      
      return experienceItems;
    });
    
    return experiences;
  } catch (error) {
    console.error('Error extracting experiences:', error.message);
    return [];
  }
}

/**
 * Extract education history
 */
async function extractEducation(page) {
  try {
    const education = await page.evaluate(() => {
      const educationItems = [];
      const educationElements = document.querySelectorAll('.education__list-item, .pv-education-entity');
      
      for (const element of educationElements) {
        try {
          const schoolName = element.querySelector('.pv-entity__school-name, .pv-entity__logo-school-name-text')?.textContent?.trim() || '';
          
          const degreeInfo = element.querySelector('.pv-entity__degree-name .pv-entity__comma-item, .pv-entity__fos .pv-entity__comma-item')?.textContent?.trim() || '';
          const fieldOfStudy = element.querySelector('.pv-entity__fos .pv-entity__comma-item')?.textContent?.trim() || '';
          
          const datesDiv = element.querySelector('.pv-entity__dates span:nth-child(2)');
          const datesText = datesDiv ? datesDiv.textContent.trim() : '';
          
          let startDate = null;
          let endDate = null;
          
          if (datesText) {
            const dateParts = datesText.split('-');
            if (dateParts.length >= 2) {
              startDate = dateParts[0].trim();
              endDate = dateParts[1].trim();
            }
          }
          
          educationItems.push({
            schoolName,
            degreeName: degreeInfo,
            fieldOfStudy,
            startDate,
            endDate,
            durationInDays: calculateDuration(startDate, endDate)
          });
        } catch (e) {
          continue; // Skip problematic items
        }
      }
      
      return educationItems;
    });
    
    return education;
  } catch (error) {
    console.error('Error extracting education:', error.message);
    return [];
  }
}

/**
 * Extract skills
 */
async function extractSkills(page) {
  try {
    const skills = await page.evaluate(() => {
      const skillItems = [];
      const skillElements = document.querySelectorAll('.pv-skill-category-entity__featured-skill, .pv-skill-entity');
      
      for (const element of skillElements) {
        try {
          const skillName = element.querySelector('.pv-skill-category-entity__name-text, .pv-skill-entity__name')?.textContent?.trim() || '';
          
          // Try to find endorsement count
          let endorsementCount = 0;
          const endorsementText = element.querySelector('.pv-skill-entity__endorsement-count')?.textContent?.trim() || '';
          if (endorsementText) {
            const numMatch = endorsementText.match(/\d+/);
            if (numMatch) {
              endorsementCount = parseInt(numMatch[0]);
            }
          }
          
          if (skillName) {
            skillItems.push({
              skillName,
              endorsementCount
            });
          }
        } catch (e) {
          continue; // Skip problematic items
        }
      }
      
      return skillItems;
    });
    
    return skills;
  } catch (error) {
    console.error('Error extracting skills:', error.message);
    return [];
  }
}

/**
 * Extract volunteer experiences
 */
async function extractVolunteerExperiences(page) {
  try {
    const volunteerExperiences = await page.evaluate(() => {
      const volunteerItems = [];
      const volunteerElements = document.querySelectorAll('.pv-volunteering-entity');
      
      for (const element of volunteerElements) {
        try {
          const title = element.querySelector('.pv-entity__summary-info h3')?.textContent?.trim() || '';
          const company = element.querySelector('.pv-entity__secondary-title')?.textContent?.trim() || '';
          const description = element.querySelector('.pv-entity__description')?.textContent?.trim() || '';
          
          const datesDiv = element.querySelector('.pv-entity__date-range span:nth-child(2)');
          const datesText = datesDiv ? datesDiv.textContent.trim() : '';
          
          let startDate = null;
          let endDate = null;
          let endDateIsPresent = false;
          
          if (datesText) {
            const dateParts = datesText.split('-');
            if (dateParts.length >= 2) {
              startDate = dateParts[0].trim();
              endDate = dateParts[1].trim();
              
              if (endDate.toLowerCase().includes('present') || endDate.toLowerCase().includes('current')) {
                endDateIsPresent = true;
              }
            }
          }
          
          volunteerItems.push({
            title,
            company,
            description,
            startDate,
            endDate,
            endDateIsPresent
          });
        } catch (e) {
          continue; // Skip problematic items
        }
      }
      
      return volunteerItems;
    });
    
    return volunteerExperiences;
  } catch (error) {
    console.error('Error extracting volunteer experiences:', error.message);
    return [];
  }
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
 * Main run function for the LinkedIn profile scraper
 */
async function run(jobId, profileUrls, config = {}, hooks = {}) {
  const {
    logEvent = () => {},
    updateProgress = () => {},
    isCancelled = () => false,
  } = hooks;

  const sessionCookieValue = config.sessionCookieValue;
  
  if (!sessionCookieValue) {
    throw new Error('sessionCookieValue is required for LinkedIn profile scraping');
  }

  if (!profileUrls || !Array.isArray(profileUrls) || profileUrls.length === 0) {
    throw new Error('profileUrls must be a non-empty array');
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
    
    let totalProfiles = profileUrls.length;
    let processedCount = 0;
    
    for (const profileUrl of profileUrls) {
      if (isCancelled()) break;
      
      await logEvent("INFO", `Processing LinkedIn profile: ${profileUrl} (${processedCount + 1}/${totalProfiles})`);
      
      try {
        // Navigate to the profile page
        await page.goto(profileUrl, { 
          waitUntil: 'networkidle2',
          timeout: 30000 
        });
        
        // Check if session is still valid
        if (await checkSessionStatus(page)) {
          throw new Error('SessionExpired');
        }
        
        // Wait for the page to load and expand sections
        await page.waitForSelector('main', { timeout: 10000 });
        
        // Scroll down to load content
        await scrollToBottom(page);
        
        // Expand sections to reveal more content
        await expandSections(page);
        
        // Additional wait for dynamic content to load
        await page.waitForTimeout(3000);
        
        // Extract all profile information
        const userProfile = await extractProfileInfo(page);
        const experiences = await extractExperiences(page);
        const education = await extractEducation(page);
        const skills = await extractSkills(page);
        const volunteerExperiences = await extractVolunteerExperiences(page);
        
        // Extract contact information
        const contactInfo = await extractContactInfo(page);
        
        // Combine all extracted data
        const result = {
          userProfile: {
            ...userProfile,
            url: profileUrl
          },
          experiences,
          education,
          volunteerExperiences,
          skills,
          contactInfo
        };
        
        // Save result to both the existing social_results table and the new linkedin_profiles table
        await db("social_results").insert({
          job_id: jobId,
          source: "LINKEDIN",
          keyword: profileUrl, // Using the profile URL as the keyword
          name: userProfile.fullName || '',
          title: userProfile.title || '',
          description: userProfile.description || '',
          profile_url: profileUrl,
          email: contactInfo.email || null,
          phone: contactInfo.phone || null,
          raw_data: JSON.stringify(result),
          scraped_at: new Date().toISOString()
        });
        
        // Save to the new linkedin_profiles table
        await db("linkedin_profiles").insert({
          job_id: jobId,
          profile_url: profileUrl,
          full_name: userProfile.fullName || '',
          first_name: userProfile.firstName || '',
          last_name: userProfile.lastName || '',
          title: userProfile.title || '',
          location: userProfile.location || '',
          description: userProfile.description || '',
          photo_url: userProfile.photo || '',
          email: contactInfo.email || null,
          phone: contactInfo.phone || null,
          experiences: JSON.stringify(experiences),
          education: JSON.stringify(education),
          skills: JSON.stringify(skills),
          volunteer_experiences: JSON.stringify(volunteerExperiences),
          industry: userProfile.industry || null,
          raw_data: JSON.stringify(result),
          scraped_at: new Date().toISOString()
        });
        
        await logEvent("INFO", `✓ LinkedIn profile saved: ${profileUrl} (Email: ${contactInfo.email || 'N/A'}, Phone: ${contactInfo.phone || 'N/A'})`);
        
        // Update progress
        processedCount++;
        const progress = (processedCount / totalProfiles) * 100;
        await updateProgress(progress, processedCount);
        
        // Add delay between profiles to be respectful
        if (processedCount < totalProfiles && !isCancelled()) {
          await page.waitForTimeout(2000); // 2 second delay between profiles
        }
        
      } catch (error) {
        await logEvent("ERROR", `Failed to process profile ${profileUrl}: ${error.message}`);
        
        // If session expired, rethrow with specific error name
        if (error.message === 'SessionExpired') {
          throw error;
        }
      }
    }
    
  } finally {
    await browser.close();
  }
}

module.exports = { run };