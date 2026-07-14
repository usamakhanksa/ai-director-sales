# Enhanced Web Crawler Spider - Comprehensive Feature Documentation

## Overview
This enhanced version of the Web Crawler Spider includes advanced anti-detection measures, expanded scraping capabilities, and improved architecture for enterprise-grade web scraping.

## Key Enhancements

### 1. Advanced Anti-Detection System
- **Fingerprint Injection**: Dynamically generates realistic browser fingerprints including viewport, user agent, and navigator properties
- **Ghost Cursor**: Implements human-like mouse movements and interactions
- **Request Interception**: Blocks media files, fonts, and tracking requests to reduce bandwidth and avoid detection
- **Random Delays**: Variable timing between actions to mimic human behavior
- **Proxy Rotation**: Dynamic proxy switching for IP rotation

### 2. Expanded Social Media Scraping
- **Instagram Scraper**: Extracts profile information, followers, following, bio, and recent posts
- **Unified Job System**: Consistent job management across all scraping modules
- **Progress Tracking**: Real-time progress updates and logging
- **Error Handling**: Robust error recovery and reporting

### 3. Professional API Management
- **User-Facing API Keys**: Secure, rate-limited access for third-party integrations
- **Public API Endpoint**: `/v1/scrape` endpoint for external usage
- **Usage Tracking**: Detailed logging of API consumption
- **Quota Management**: Configurable rate limits per API key

### 4. Enhanced Authentication
- **Refresh Tokens**: Secure token rotation with configurable expiration
- **Unified JWT System**: Single authentication system across frontend and backend
- **Session Management**: Proper token validation and revocation

## Architecture

### Frontend Components
- **Next.js App Router**: Modern routing and server-side rendering
- **React Components**: Reusable UI components for job management and results display
- **Authentication Hook**: Centralized auth management across the application

### Backend Services
- **Express.js API**: RESTful endpoints for all scraping operations
- **Knex.js/Prisma ORM**: Dual database access layer for legacy and new features
- **Playwright Integration**: Headless browser automation with advanced stealth
- **Job Queue System**: Asynchronous job processing with progress tracking

### Database Structure
- **Unified Schema**: All data models consolidated in Prisma schema
- **Job Management**: Centralized job tracking across all scraping modules
- **API Key Management**: Secure key generation and usage tracking
- **Result Storage**: Structured storage for scraped data with metadata

## Installation & Setup

### Prerequisites
- Node.js 18+
- MySQL 8.0+
- Playwright browsers (install with `npx playwright install`)

### Dependencies
```bash
npm install fingerprint-generator fingerprint-injector ghost-cursor
```

### Environment Configuration
```env
# JWT Configuration
JWT_SECRET=your_jwt_secret_here
JWT_REFRESH_SECRET=your_refresh_secret_here
JWT_EXPIRES_IN=15m
JWT_REFRESH_EXPIRES_IN=7d

# Database
DATABASE_URL="mysql://user:password@localhost:3306/scrapedb"

# Backend Configuration
BACKEND_URL=http://localhost:3001
PLAYWRIGHT_TIMEOUT=30000
RANDOM_DELAY_MIN_MS=1500
RANDOM_DELAY_MAX_MS=4500
PROXY_LIST_FILE=proxies.txt

# API Configuration
SCRAPER_BACKEND_URL=http://localhost:3001
```

### Database Migration
```bash
npx prisma migrate dev --name add_unified_tables
```

## API Endpoints

### Authentication
- `POST /api/auth/login` - User login
- `POST /api/auth/signup` - User registration
- `POST /api/auth/refresh` - Token refresh (when implemented)

### Social Media Scraping
- `POST /api/social/instagram` - Start Instagram scraping job
- `GET /api/social/instagram/{jobId}` - Get Instagram job results

### API Key Management
- `POST /api/user/api-keys` - Create new API key
- `GET /api/user/api-keys` - List user API keys
- `DELETE /api/user/api-keys?id={id}` - Deactivate API key

### Public API
- `POST /api/v1/scrape` - Public scraping endpoint (requires API key)
- `GET /api/v1/scrape?jobId={id}&module={module}` - Get job results

## Usage Examples

### Creating an API Key
```javascript
const response = await fetch('/api/user/api-keys', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer YOUR_JWT_TOKEN'
  },
  body: JSON.stringify({
    name: 'My Production Key',
    rateLimit: 10000
  })
});
```

### Using the Public API
```javascript
const response = await fetch('/api/v1/scrape', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-API-Key': 'YOUR_API_KEY_HERE'
  },
  body: JSON.stringify({
    module: 'instagram',
    keywords: ['@username1', '@username2']
  })
});
```

### Starting an Instagram Scrape
```javascript
const response = await fetch('/api/social/instagram', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer YOUR_JWT_TOKEN'
  },
  body: JSON.stringify({
    keywords: ['@instagram', '@natgeo']
  })
});
```

## Security Features

### Authentication Security
- Short-lived access tokens (15 minutes default)
- Long-lived refresh tokens (7 days default)
- Secure token storage and validation
- Session revocation capability

### Rate Limiting
- Per-API key request limits
- Usage tracking and monitoring
- Automatic blocking when limits exceeded
- Configurable rate limits per key

### Anti-Detection Measures
- Realistic browser fingerprints
- Human-like interaction patterns
- Request filtering to avoid detection
- Proxy rotation for IP diversity

## Performance Optimizations

### Resource Management
- Efficient browser instance management
- Context isolation between jobs
- Automatic cleanup of resources
- Memory leak prevention

### Network Optimization
- Request filtering to reduce bandwidth
- Efficient data transfer protocols
- Caching mechanisms where appropriate
- Parallel processing capabilities

## Monitoring & Logging

### Job Tracking
- Real-time progress updates
- Detailed error logging
- Performance metrics collection
- Resource utilization tracking

### API Usage
- Per-key usage statistics
- Request/response logging
- Performance monitoring
- Anomaly detection

## Future Enhancements

### Planned Features
- TikTok, YouTube, and other platform scrapers
- Advanced data validation and cleaning
- Machine learning-based detection avoidance
- Enterprise-level security features
- Advanced export options (Google Sheets, Airtable)
- Real-time notifications and webhooks

### Scalability Improvements
- Distributed job processing
- Auto-scaling infrastructure
- Load balancing across workers
- Advanced caching layers

## Troubleshooting

### Common Issues
1. **Browser Launch Errors**: Ensure Playwright browsers are installed
2. **Rate Limiting**: Check API key usage against configured limits
3. **Authentication Errors**: Verify JWT tokens and refresh mechanisms
4. **Database Connection**: Confirm database URL and credentials

### Debugging Tips
- Enable verbose logging in development
- Monitor browser console for stealth detection
- Check proxy connectivity if using proxy rotation
- Verify rate limits and usage quotas

## Contributing

### Development Workflow
1. Fork the repository
2. Create a feature branch
3. Make changes following the established patterns
4. Write tests for new functionality
5. Submit a pull request with detailed description

### Code Standards
- Follow existing code style and conventions
- Include comprehensive documentation
- Add appropriate error handling
- Ensure backward compatibility where possible

## License

This project is licensed under the MIT License - see the LICENSE file for details.

---

This enhanced system provides a robust, scalable, and feature-rich web scraping solution suitable for enterprise applications with advanced anti-detection capabilities and professional-grade API management.