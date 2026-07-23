const express = require("express");
const cors = require("cors");
const helmet = require("helmet");

const routes = require("./routes");
const { apiLimiter } = require("./middleware/rateLimiter");
const { errorHandler, notFoundHandler } = require("./middleware/errorHandler");

const jobsRoutes = require("./routes/jobs.routes");
const socialRoutes = require("./routes/social.routes");
const classifiedRoutes = require("./routes/classified.routes");
const exportRoutes = require("./routes/export.routes");
const dorkGeneratorRoutes = require("./routes/dorkGenerator.routes"); // NEW: Dork generator routes
const leadQualifierRoutes = require("./routes/leadQualifier.routes");
const linkedinProfileRoutes = require("./routes/linkedinProfileRoutes"); // NEW: LinkedIn profile scraper routes
const linkedinSearchRoutes = require("./routes/linkedinSearchRoutes"); // NEW: LinkedIn search scraper routes
const googleNewsRoutes = require("./routes/google-news.routes"); // NEW: Google News RSS routes

const app = express();

app.use(helmet());
app.use(
  cors({
    origin: process.env.FRONTEND_ORIGIN || "http://localhost:3000",
    credentials: true,
  })
);
app.use(express.json({ limit: "2mb" }));
app.use(apiLimiter);

app.get("/health", (req, res) => res.json({ status: "ok" }));

app.get("/", (req, res) => {
  res.json({
    service: "ScrapeGenius backend",
    health: "GET /health",
    routes: {
      auth: [
        "POST /v1/signup",
        "POST /v1/login",
        "POST /v1/signuploginwithgoogle",
        "POST /v1/forget",
        "POST /v1/reset-password",
        "POST /v1/verification",
        "POST /v1/logout (auth required)",
        "GET /v1/restricted/purchasecodeactivation/:code (auth required)",
        "GET /v1/restricted/getgooglecode (auth required)",
        "GET /v1/restricted/users?limit= (admin required)",
      ],
      search: [
        "POST /v1/search/google (auth required)",
        "POST /v1/search/maps (auth required)",
        "POST /v1/search/maps/:searchQueryId/results (auth required)",
        "GET /v1/search/maps/cache?query= (auth required)",
      ],
      admin: [
        "GET /v1/admin/users (admin required)",
        "PATCH /v1/admin/users/:id (admin required)",
        "GET /v1/admin/api-keys (admin required)",
        "POST /v1/admin/api-keys (admin required)",
        "PATCH /v1/admin/api-keys/:id (admin required)",
        "DELETE /v1/admin/api-keys/:id (admin required)",
        "GET /v1/admin/usage?date= (admin required)",
        "GET /v1/admin/search-queries?limit= (admin required)",
      ],
    },
  });
});

app.use("/v1", routes);
app.use("/v1/jobs", jobsRoutes);
app.use("/v1/social", socialRoutes);
app.use("/v1/classified", classifiedRoutes);
app.use("/v1/export", exportRoutes);
app.use("/v1/dorks", dorkGeneratorRoutes); // NEW: Register dork generator routes
app.use("/v1/lead-qualifier", leadQualifierRoutes);
app.use("/api/linkedin-profile", linkedinProfileRoutes); // NEW: Register LinkedIn profile scraper routes
app.use("/api/linkedin-search", linkedinSearchRoutes); // NEW: Register LinkedIn search scraper routes
app.use("/v1/scrape/google-news", googleNewsRoutes); // NEW: Register Google News RSS routes
app.use(notFoundHandler);
app.use(errorHandler);

module.exports = app;