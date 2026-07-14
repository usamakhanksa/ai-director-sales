// eslint-disable-next-line no-unused-vars
function errorHandler(err, req, res, next) {
  console.error("❌ Unhandled error:", err);

  if (res.headersSent) {
    return;
  }

  const status = err.status || 500;
  res.status(status).json({
    error: status === 500 ? "Internal Server Error" : err.message,
  });
}

function notFoundHandler(req, res) {
  res.status(404).json({ error: `Route not found: ${req.method} ${req.originalUrl}` });
}

module.exports = { errorHandler, notFoundHandler };
