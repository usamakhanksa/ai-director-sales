const express = require("express");

const authRoutes = require("./auth.routes");
const searchRoutes = require("./search.routes");
const mapsRoutes = require("./maps.routes");
const adminRoutes = require("./admin.routes");

const router = express.Router();

router.use("/", authRoutes); // /signup, /login, /forget, /verification, /restricted/*
router.use("/search", searchRoutes); // /search/google
router.use("/search", mapsRoutes); // /search/maps, /search/maps/:id/results, /search/maps/cache
router.use("/admin", adminRoutes); // /admin/users, /admin/api-keys, /admin/usage

module.exports = router;
