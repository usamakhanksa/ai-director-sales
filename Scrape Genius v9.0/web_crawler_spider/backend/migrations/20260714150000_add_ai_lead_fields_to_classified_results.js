/**
 * Migration: Add AI lead-qualification fields to classified_results
 *
 * Lets the Lead Qualifier tool persist its LEAD / NOT_LEAD verdict per
 * scraped ad instead of re-classifying on every page view.
 */
exports.up = async function (knex) {
  await knex.schema.alterTable("classified_results", (t) => {
    t.boolean("ai_is_lead").nullable();
    t.enum("ai_label", ["LEAD", "NOT_LEAD"]).nullable();
    t.timestamp("ai_classified_at").nullable();
  });
};

exports.down = async function (knex) {
  await knex.schema.alterTable("classified_results", (t) => {
    t.dropColumn("ai_is_lead");
    t.dropColumn("ai_label");
    t.dropColumn("ai_classified_at");
  });
};
