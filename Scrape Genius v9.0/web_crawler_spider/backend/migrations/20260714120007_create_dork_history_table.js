/**
 * Migration: Create dork_history table
 * 
 * Stores dork generation history for users
 */

exports.up = function(knex) {
  return knex.schema.createTable('dork_history', function(table) {
    table.increments('id').primary();
    table.integer('user_id').notNullable().unsigned();
    table.text('dorks').notNullable(); // JSON string of generated dorks
    table.text('options').notNullable(); // JSON string of generation options
    table.timestamp('created_at').defaultTo(knex.fn.now());
    
    // Foreign key constraint
    table.foreign('user_id').references('id').inTable('users');
    
    // Index for performance
    table.index(['user_id', 'created_at']);
  });
};

exports.down = function(knex) {
  return knex.schema.dropTableIfExists('dork_history');
};