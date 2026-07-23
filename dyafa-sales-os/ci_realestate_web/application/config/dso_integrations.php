<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Dyafa Sales OS - external integration endpoints.
 * Rewritten by Administration > Integrations UI (dyafa/admin/integrations).
 * Each integration runs in live|mock|off mode - see original comment block
 * preserved in git history / implementation.md for the full rationale.
 * API keys are NOT stored here - see dso_integration_credentials (encrypted).
 */
$config['dso_pms_mode']     = 'mock';
$config['dso_pms_endpoint'] = '';
$config['dso_pms_timeout']  = 5;

$config['dso_finance_mode']     = 'mock';
$config['dso_finance_endpoint'] = '';
$config['dso_finance_timeout']  = 5;

$config['dso_maps_mode']     = 'mock';
$config['dso_maps_endpoint'] = '';
$config['dso_maps_timeout']  = 5;

$config['dso_payment_mode']     = 'mock';
$config['dso_payment_endpoint'] = '';
$config['dso_payment_timeout']  = 5;

$config['dso_reporting_mode']     = 'mock';
$config['dso_reporting_endpoint'] = '';
$config['dso_reporting_timeout']  = 5;

