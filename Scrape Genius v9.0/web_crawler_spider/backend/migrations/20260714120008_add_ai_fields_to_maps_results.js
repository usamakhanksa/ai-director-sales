/**
 * Migration: Add AI enhancement fields to maps_job_results table
 * 
 * Adds fields for storing AI-analyzed data from Google Maps scraping
 */

exports.up = function(knex) {
  return knex.schema.table('maps_job_results', function(table) {
    // AI-enhanced fields
    table.json('additional_emails'); // JSON array of additional email addresses found
    table.text('business_description'); // Business description extracted by AI
    table.json('services'); // Array of services offered extracted by AI
    table.decimal('lead_score', 5, 2); // AI-calculated lead score (0-100)
    table.string('lead_rating', 20); // Lead rating (Hot/Warm/Cold)
    
    // Index for performance on commonly queried fields
    table.index(['lead_score', 'lead_rating']);
  });
};

exports.down = function(knex) {
  return knex.schema.table('maps_job_results', function(table) {
    table.dropIndex(['lead_score', 'lead_rating']);
    table.dropColumn('additional_emails');
    table.dropColumn('business_description');
    table.dropColumn('services');
    table.dropColumn('lead_score');
    table.dropColumn('lead_rating');
  });
};