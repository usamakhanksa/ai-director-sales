# Scrape Genius v9.0 - Web Crawler Spider

## Enhanced Web Scraping Solution with Advanced Anti-Detection

This is an enterprise-grade web scraping platform featuring advanced anti-detection measures, comprehensive social media scraping capabilities, and professional API management.

## 🚀 Key Features

### Anti-Detection Technology
- **Advanced Fingerprint Injection**: Realistic browser fingerprint generation
- **Ghost Cursor**: Human-like mouse movements and interactions  
- **Request Filtering**: Automatic blocking of media, fonts, and tracking requests
- **Dynamic Proxy Rotation**: Intelligent IP rotation system
- **Adaptive Delays**: Variable timing to mimic human behavior

### Social Media Scraping
- **Instagram Profiler**: Extract profiles, followers, bios, and posts
- **Facebook Data Extractor**: Multi-source data aggregation
- **LinkedIn Email Finder**: Professional email discovery
- **Twitter/X Scraper**: Tweet and profile analysis
- **Expandable Framework**: Easy addition of new platforms

### Professional API Management
- **User-Facing API Keys**: Secure, rate-limited access
- **Public API Endpoint**: `/v1/scrape` for third-party integrations
- **Usage Analytics**: Detailed consumption tracking
- **Quota Management**: Configurable rate limits

### Enterprise Security
- **JWT Authentication**: Secure token-based system
- **Refresh Tokens**: Automatic token rotation
- **Session Management**: Proper session handling
- **Rate Limiting**: Per-user and per-API controls

## 🛠️ Tech Stack

- **Frontend**: Next.js 14+, React 18+, TypeScript
- **Backend**: Node.js, Express.js, TypeScript
- **Database**: MySQL, Prisma ORM, Knex.js
- **Browser Automation**: Playwright, Playwright Extra
- **Styling**: Tailwind CSS
- **Security**: JWT, bcrypt, helmet, csrf

## 📋 Prerequisites

- Node.js 18+
- MySQL 8.0+
- Playwright browsers (`npx playwright install`)
- API keys for various services (optional)

## 🚀 Quick Start

1. Clone the repository
```bash
git clone <repository-url>
cd web_crawler_spider
```

2. Install dependencies
```bash
npm install
```

3. Install Playwright browsers
```bash
npx playwright install
```

4. Install additional dependencies for enhanced features
```bash
npm install fingerprint-generator fingerprint-injector ghost-cursor
```

5. Configure environment variables
```bash
cp .env.example .env
# Edit .env with your configuration
```

6. Set up the database
```bash
npx prisma migrate dev
```

7. Start the development server
```bash
npm run dev
```

## 🔧 Configuration

### Environment Variables
```env
# Database
DATABASE_URL="mysql://user:password@localhost:3306/scrapedb"

# JWT Configuration
JWT_SECRET=your_jwt_secret
JWT_REFRESH_SECRET=your_refresh_secret

# Application Settings
NEXT_PUBLIC_APP_NAME="Scrape Genius"
NODE_ENV=development

# Backend Configuration
BACKEND_URL=http://localhost:3001
SCRAPER_BACKEND_URL=http://localhost:3001

# Anti-Detection Settings
PLAYWRIGHT_TIMEOUT=30000
RANDOM_DELAY_MIN_MS=1500
RANDOM_DELAY_MAX_MS=4500
PROXY_LIST_FILE=proxies.txt
```

## 📖 API Documentation

### Authentication
- Login: `POST /api/auth/login`
- Signup: `POST /api/auth/signup`
- Protected routes require `Authorization: Bearer <token>` header

### Social Media Scraping
- Instagram: `POST /api/social/instagram`
- Get results: `GET /api/social/instagram/{jobId}`

### API Key Management
- Create key: `POST /api/user/api-keys`
- List keys: `GET /api/user/api-keys`
- Delete key: `DELETE /api/user/api-keys?id={id}`

### Public API
- Scrape: `POST /api/v1/scrape` (requires X-API-Key header)
- Get results: `GET /api/v1/scrape?jobId={id}&module={module}`

## 🧪 Running Tests

```bash
# Run unit tests
npm test

# Run integration tests
npm run test:integration

# Run E2E tests
npm run test:e2e
```

## 🚀 Deployment

### Production Build
```bash
npm run build
npm start
```

### Docker Support
```bash
docker-compose up -d
```

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🐛 Issues & Support

Please submit issues and feature requests through the GitHub issue tracker.

## 🆕 What's New in v9.0

- **Advanced Anti-Detection**: Fingerprint injection, ghost cursor, request filtering
- **Expanded Social Media**: Instagram, Facebook, LinkedIn, Twitter scraping
- **Professional API**: User-facing API keys with rate limiting
- **Enterprise Security**: JWT refresh tokens, session management
- **Unified Architecture**: Single codebase for all scraping modules
- **Job Queue System**: Asynchronous job processing with progress tracking

---

Built with ❤️ for ethical web scraping and data extraction.

For documentation on all enhanced features, see [ENHANCED_FEATURES.md](ENHANCED_FEATURES.md).