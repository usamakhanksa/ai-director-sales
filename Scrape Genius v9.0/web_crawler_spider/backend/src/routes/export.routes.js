/**
 * Export API Routes
 * 
 * Endpoints:
 *   POST /v1/export/:jobId — Trigger export generation
 *   GET  /v1/export/:exportId/download — Download the generated file
 */

"use strict";

const express = require("express");
const fs = require("fs");
const db = require("../config/database");
const { requireAuth } = require("../middleware/auth");
const { requireAuthOrInternal } = require("../middleware/internalAuth");
const auth = requireAuthOrInternal(requireAuth);
const { exportResults, getExportFilePath } = require("../services/exportService");

const router = express.Router();

router.post("/:jobId", auth, async (req, res, next) => {
  try {
    const jobId = Number(req.params.jobId);
    const { format } = req.body; // XLSX, CSV, HTML, TXT

    if (!format) {
      return res.status(400).json({ success: false, error: "format is required (XLSX, CSV, HTML, TXT)" });
    }

    // Verify job belongs to user
    const job = await db("scrape_jobs").where({ id: jobId, user_id: req.user.id }).first();
    if (!job) {
      return res.status(404).json({ success: false, error: "Job not found" });
    }

    const result = await exportResults({
      jobId,
      userId: req.user.id,
      format,
    });

    res.json({
      success: true,
      data: {
        exportId: result.exportId,
        rowCount: result.rowCount,
        downloadUrl: `/api/export/${result.exportId}/download`, // Frontend URL will proxy this
      },
      message: `Export generated successfully (${result.rowCount} rows)`,
    });
  } catch (err) {
    if (err.message.includes("Invalid format") || err.message.includes("No results")) {
      return res.status(400).json({ success: false, error: err.message });
    }
    next(err);
  }
});

router.get("/:exportId/download", auth, async (req, res, next) => {
  try {
    const exportId = Number(req.params.exportId);
    
    const { filePath, format } = await getExportFilePath(exportId, req.user.id);

    const fileName = filePath.split(/[\\/]/).pop();
    
    res.setHeader("Content-Disposition", `attachment; filename="${fileName}"`);
    
    // Set appropriate content type
    const mimeTypes = {
      "XLSX": "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
      "CSV": "text/csv",
      "HTML": "text/html",
      "TXT": "text/plain",
    };
    if (mimeTypes[format]) {
      res.setHeader("Content-Type", mimeTypes[format]);
    }

    const fileStream = fs.createReadStream(filePath);
    fileStream.pipe(res);
    
    fileStream.on('error', (err) => {
      console.error(`[ExportRoute] Error streaming file: ${err.message}`);
      if (!res.headersSent) {
        res.status(500).json({ success: false, error: "Error streaming file" });
      }
    });

  } catch (err) {
    if (err.message.includes("not found") || err.message.includes("exists")) {
      return res.status(404).json({ success: false, error: err.message });
    }
    next(err);
  }
});

// GET /v1/export - List export history for user
router.get("/", auth, async (req, res, next) => {
  try {
    const limit = Math.min(Number(req.query.limit) || 50, 100);
    const offset = Number(req.query.offset) || 0;

    const exports = await db("export_records")
      .where({ user_id: req.user.id })
      .orderBy("created_at", "desc")
      .limit(limit)
      .offset(offset);

    const total = await db("export_records").where({ user_id: req.user.id }).count("id as count").first();

    res.json({
      success: true,
      data: exports,
      total: Number(total.count)
    });
  } catch (err) {
    next(err);
  }
});


module.exports = router;
