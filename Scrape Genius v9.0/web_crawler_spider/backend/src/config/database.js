const knex = require("knex");
const knexConfig = require("../../knexfile");

const environment = process.env.NODE_ENV === "production" ? "production" : "development";

const db = knex(knexConfig[environment]);

module.exports = db;
