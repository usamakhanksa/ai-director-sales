<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Dyafa Sales OS - role definitions.
 * List of roles usable throughout the module. Controllers use
 * Dso_Controller::require_role(array $roles) to restrict access.
 */
$config['dso_roles'] = array(
    'HOD Sales',
    'Sales Manager',
    'Sales Executive',
    'Sales Coordinator',
    'Reservation Team',
    'Finance Team',
    'Management',
    'Corporate Client',
);

// roles that may access the HOD-level dashboard / team performance views
$config['dso_hod_roles'] = array('HOD Sales', 'Sales Manager', 'Management');

/*
 * Corporate Portal sub-roles (BRD Section 11: Company User Management).
 * A corporate account may have several logins, each with a different
 * capability inside the Self-Service Portal, all still scoped to the same
 * dso_users.account_id column used by the original flat 'Corporate Client'
 * role (kept for backward compatibility with pre-existing seeded logins).
 */
$config['dso_corporate_roles'] = array(
    'Corporate Client', // legacy flat role - treated the same as CorporateAdmin below
    'CorporateAdmin',
    'CorporateHR',
    'CorporateFinance',
    'CorporateTravelCoordinator',
    'CorporateProjectManager',
);

/*
 * Capability map per corporate sub-role, matching BRD Section 11:
 *   - CorporateAdmin (and legacy 'Corporate Client'): full access - create
 *     users, manage reservations, download reports, view statements.
 *   - CorporateHR: view only (BRD lists no explicit HR capability beyond
 *     user creation support - kept read-only here).
 *   - CorporateFinance: view invoices/statements/outstanding balances only.
 *   - CorporateTravelCoordinator: create/modify reservations.
 *   - CorporateProjectManager: create reservations only (no modify/cancel).
 */
$config['dso_corporate_capabilities'] = array(
    'Corporate Client'           => array('manage_users', 'create_reservation', 'modify_reservation', 'cancel_reservation', 'view_statement'),
    'CorporateAdmin'             => array('manage_users', 'create_reservation', 'modify_reservation', 'cancel_reservation', 'view_statement'),
    'CorporateHR'                => array('view_statement'),
    'CorporateFinance'           => array('view_statement'),
    'CorporateTravelCoordinator' => array('create_reservation', 'modify_reservation', 'cancel_reservation', 'view_statement'),
    'CorporateProjectManager'    => array('create_reservation', 'view_statement'),
);
