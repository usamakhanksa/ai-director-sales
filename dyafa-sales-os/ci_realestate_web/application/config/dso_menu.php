<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Dyafa Sales OS - internal (staff) sidebar menu definition.
 *
 * Single source of truth for the sidebar rendered in
 * application/views/dyafa/layout/header.php. Every `route` below points at
 * a real, existing controller method - nothing here links to a page that
 * doesn't exist yet.
 *
 * Each top-level node:
 *   'key'      => unique string, used for the localStorage open/closed state
 *   'label'    => nav label
 *   'icon'     => key into $dso_icons in header.php
 *   'segment'  => URI segment 2 (dyafa/<segment>/...) used for active-state matching
 *   'roles'    => null = visible to every authenticated staff role (Corporate Client
 *                 always excluded, it uses the separate Portal layout/login);
 *                 otherwise an array of exact role strings from dso_roles.php
 *   'route'    => optional direct link for a node with no children
 *   'children' => optional array of ['label', 'route' (relative to dyafa/), 'roles' (optional, narrows parent)]
 *
 * A child's 'roles' (if set) is intersected with the parent's - a child is
 * only shown if the current role passes both checks.
 *
 * Administration menu items are additionally gated inside their controllers
 * via Dso_Controller::require_permission() (dynamic RBAC, see Users & Roles) -
 * the 'roles' arrays below only control sidebar *visibility*, not access.
 */
$config['dso_menu'] = array(
    array(
        'key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard', 'segment' => 'dashboard',
        'roles' => null,
        'children' => array(
            array('label' => 'My Dashboard', 'route' => 'dashboard'),
            array('label' => 'Daily Sales', 'route' => 'dashboard/daily'),
            array('label' => 'HOD Dashboard', 'route' => 'dashboard/hod', 'roles' => array('HOD Sales', 'Sales Manager', 'Management')),
            array('label' => 'My Performance', 'route' => 'dashboard/my_performance'),
            array('label' => 'Team Performance', 'route' => 'dashboard/team_performance', 'roles' => array('HOD Sales', 'Sales Manager', 'Management')),
        ),
    ),
    array(
        'key' => 'leads', 'label' => 'Leads', 'icon' => 'leads', 'segment' => 'leads',
        'roles' => array('HOD Sales', 'Sales Manager', 'Sales Executive'),
        'children' => array(
            array('label' => 'All Leads', 'route' => 'leads'),
            array('label' => 'My Leads', 'route' => 'leads/index/mine'),
            array('label' => 'Unassigned Leads', 'route' => 'leads/index/unassigned', 'roles' => array('HOD Sales', 'Sales Manager')),
            array('label' => 'Add New Lead', 'route' => 'leads/add'),
            array('label' => 'Lead Sources', 'route' => 'leads/sources'),
            array('label' => 'AI Generated Leads', 'route' => 'leads/index/ai'),
        ),
    ),
    array(
        'key' => 'ai_lead_generation', 'label' => 'AI Lead Generation', 'icon' => 'aiassistant', 'segment' => 'leadgeneration',
        'roles' => array('HOD Sales', 'Sales Manager', 'Sales Executive', 'Management'),
        'children' => array(
            array('label' => 'Recommendations', 'route' => 'aiassistant'),
            array('label' => 'Lead Scoring Config', 'route' => 'leadscoringconfig', 'roles' => array('HOD Sales', 'Sales Manager', 'Management')),
            array('label' => 'Generate Leads', 'route' => 'leadgeneration', 'roles' => array('HOD Sales', 'Sales Manager', 'Management')),
            array('label' => 'AI Settings', 'route' => 'aiconfig', 'roles' => array('HOD Sales', 'Sales Manager', 'Management')),
        ),
    ),
    array(
        'key' => 'contracts', 'label' => 'Contracts', 'icon' => 'contracts', 'segment' => 'contracts',
        'roles' => array('HOD Sales', 'Sales Manager', 'Sales Executive', 'Finance Team', 'Management'),
        'children' => array(
            array('label' => 'All Contracts', 'route' => 'contracts'),
            array('label' => 'Active', 'route' => 'contracts/index/active'),
            array('label' => 'Pending Approval', 'route' => 'contracts/index/pending'),
            array('label' => 'Expiring Soon', 'route' => 'contracts/index/expiring'),
            array('label' => 'Create Contract', 'route' => 'contracts/add', 'roles' => array('HOD Sales', 'Sales Manager', 'Sales Executive')),
            array('label' => 'Contract Funnel', 'route' => 'contracts/funnel'),
        ),
    ),
    array(
        'key' => 'accounts', 'label' => 'Corporate Accounts', 'icon' => 'accounts', 'segment' => 'accounts',
        'roles' => array('HOD Sales', 'Sales Manager', 'Sales Executive', 'Management'),
        'children' => array(
            array('label' => 'Account List', 'route' => 'accounts'),
            array('label' => 'Add Account', 'route' => 'accounts/add', 'roles' => array('HOD Sales', 'Sales Manager')),
            array('label' => 'Performance', 'route' => 'accounts/performance'),
        ),
    ),
    array(
        'key' => 'reservations', 'label' => 'Reservations', 'icon' => 'reservations', 'segment' => 'reservations',
        'roles' => array('HOD Sales', 'Sales Manager', 'Sales Executive', 'Reservation Team', 'Management'),
        'children' => array(
            array('label' => 'All Reservations', 'route' => 'reservations'),
            array('label' => 'Pending', 'route' => 'reservations/index/pending'),
            array('label' => "Today's Check-ins", 'route' => 'reservations/index/checkins_today'),
            array('label' => "Today's Check-outs", 'route' => 'reservations/index/checkouts_today'),
            array('label' => 'New Reservation', 'route' => 'reservations/add'),
            array('label' => 'Reservation Calendar', 'route' => 'reservations/calendar'),
        ),
    ),
    array(
        'key' => 'adhoc', 'label' => 'Adhoc Sales', 'icon' => 'adhoc', 'segment' => 'adhoc',
        'roles' => array('HOD Sales', 'Sales Manager', 'Sales Executive', 'Management'),
        'children' => array(
            array('label' => 'Opportunities Board', 'route' => 'adhoc/board'),
            array('label' => 'All Adhoc Sales', 'route' => 'adhoc'),
            array('label' => 'New Adhoc Sale', 'route' => 'adhoc/add'),
            array('label' => 'Proposals', 'route' => 'adhoc/index/proposals'),
            array('label' => 'Events', 'route' => 'adhoc/index/events'),
            array('label' => 'Adhoc Revenue', 'route' => 'reports/adhoc_sales'),
        ),
    ),
    array(
        'key' => 'activities', 'label' => 'Activities', 'icon' => 'activities', 'segment' => 'activities',
        'roles' => array('HOD Sales', 'Sales Manager', 'Sales Executive', 'Management'),
        'children' => array(
            array('label' => 'My Activities', 'route' => 'activities/index/mine'),
            array('label' => 'Team Activities', 'route' => 'activities/index/team', 'roles' => array('HOD Sales', 'Sales Manager', 'Management')),
            array('label' => 'Log Activity', 'route' => 'activities/add'),
        ),
    ),
    array(
        'key' => 'properties', 'label' => 'Property Management', 'icon' => 'properties', 'segment' => 'properties',
        'roles' => null,
        'children' => array(
            array('label' => 'Properties List', 'route' => 'properties'),
            array('label' => 'Add Property / Upload Map', 'route' => 'properties/add', 'roles' => array('Sales Coordinator')),
        ),
    ),
    array(
        'key' => 'collections', 'label' => 'Collections', 'icon' => 'collections', 'segment' => 'collections',
        'roles' => array('Finance Team', 'HOD Sales', 'Sales Manager', 'Management'),
        'children' => array(
            array('label' => 'Outstanding Payments', 'route' => 'collections'),
            array('label' => 'Aging Report', 'route' => 'collections/aging'),
            array('label' => 'Credit Limits', 'route' => 'collections/credit_limits'),
            array('label' => 'Invoices', 'route' => 'collections/invoices'),
            array('label' => 'Statements', 'route' => 'collections/statements'),
        ),
    ),
    array(
        'key' => 'targets', 'label' => 'Targets', 'icon' => 'targets', 'segment' => 'targets',
        'roles' => array('HOD Sales', 'Sales Manager', 'Sales Executive', 'Sales Coordinator', 'Reservation Team', 'Management'),
        'children' => array(
            array('label' => 'My Targets', 'route' => 'targets'),
            array('label' => 'Team Targets', 'route' => 'targets', 'roles' => array('HOD Sales', 'Sales Manager', 'Management')),
            array('label' => 'Set Targets', 'route' => 'targets/add', 'roles' => array('HOD Sales', 'Sales Manager', 'Management')),
            array('label' => 'Achievement Report', 'route' => 'targets/performance', 'roles' => array('HOD Sales', 'Sales Manager', 'Management')),
        ),
    ),
    array(
        'key' => 'aiassistant', 'label' => 'AI Sales Assistant', 'icon' => 'aiassistant', 'segment' => 'aiassistant',
        'roles' => array('HOD Sales', 'Sales Manager', 'Sales Executive', 'Management'),
        'children' => array(
            array('label' => 'Recommendations', 'route' => 'aiassistant'),
            array('label' => 'Predictions', 'route' => 'aiassistant/predictions'),
            array('label' => 'Next Best Actions', 'route' => 'aiassistant/next_best_actions'),
            array('label' => 'Analytics', 'route' => 'aiassistant/analytics'),
            array('label' => 'Generate Now', 'route' => 'aiassistant/generate', 'roles' => array('HOD Sales', 'Sales Manager', 'Management')),
            array('label' => 'AI Provider Config', 'route' => 'aiconfig', 'roles' => array('HOD Sales', 'Sales Manager', 'Management')),
        ),
    ),
    array(
        'key' => 'reports', 'label' => 'Reports', 'icon' => 'reports', 'segment' => 'reports',
        'roles' => null,
        'children' => array(
            array('label' => 'Daily Sales Report', 'route' => 'reports/daily_sales'),
            array('label' => 'Revenue Report', 'route' => 'reports/revenue'),
            array('label' => 'Collections Aging', 'route' => 'reports/aging'),
            array('label' => 'Leads Report', 'route' => 'reports/leads'),
            array('label' => 'Reservations Report', 'route' => 'reports/reservations'),
            array('label' => 'Room Nights Report', 'route' => 'reports/room_nights'),
            array('label' => 'Contract Report', 'route' => 'reports/contracts'),
            array('label' => 'Contract Renewal Report', 'route' => 'reports/contract_renewals'),
            array('label' => 'Opportunities Report', 'route' => 'reports/opportunities'),
            array('label' => 'Adhoc Sales Report', 'route' => 'reports/adhoc_sales'),
            array('label' => 'Activities Report', 'route' => 'reports/activities'),
            array('label' => 'Corporate Accounts Report', 'route' => 'reports/corporate_accounts'),
            array('label' => 'Property Performance Report', 'route' => 'reports/property_performance'),
            array('label' => 'AI Recommendation Report', 'route' => 'reports/ai_recommendations'),
        ),
    ),
    array(
        'key' => 'administration', 'label' => 'Administration', 'icon' => 'admin', 'segment' => 'admin',
        'roles' => array('HOD Sales', 'Management'),
        'children' => array(
            array('label' => 'Users & Roles', 'route' => 'admin/users'),
            array('label' => 'Teams', 'route' => 'admin/teams'),
            array('label' => 'Integrations', 'route' => 'admin/integrations'),
            array('label' => 'Notification Center', 'route' => 'admin/notificationcenter'),
            array('label' => 'Audit Log', 'route' => 'admin/auditlog'),
        ),
    ),
    array(
        'key' => 'notifications', 'label' => 'Notifications', 'icon' => 'notifications', 'segment' => 'notifications',
        'roles' => null,
        'route' => 'notifications',
    ),
);
