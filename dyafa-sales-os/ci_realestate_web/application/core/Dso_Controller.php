<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dso_Controller
 *
 * Base controller for the Dyafa Sales OS module. This app's core/MY_Loader.php
 * and core/MY_Router.php are Wiredesignz MX (HMVC) classes that require every
 * controller to extend MX_Controller so CI::$APP is set up correctly (session,
 * loader, router all depend on it) - a plain CI_Controller subclass fails with
 * "Unable to locate the specified class: Session.php" because CI::$APP is never
 * populated. We extend MX_Controller (same as every other controller in this
 * app) but keep Dyafa Sales OS logic and data fully independent from the
 * legacy CMS MY_Controller class.
 *
 * Handles: session-based auth check, role helper, common data.
 */
class Dso_Controller extends MX_Controller
{
    /** Controllers/methods that are reachable without being logged in. */
    protected $public_actions = array(
        'auth/login',
        'auth/authenticate',
        'auth/logout',
        'portal/login',
        'portal/authenticate',
        'portal/logout',
        'portal/setup_2fa',
        'portal/verify_2fa',
    );

    public function __construct()
    {
        parent::__construct();

        $this->load->config('dso_roles');
        $this->load->helper(array('url'));

        $class  = strtolower($this->router->fetch_class());
        $method = strtolower($this->router->fetch_method());
        $current = $class . '/' . $method;

        if (in_array($current, $this->public_actions, true)) {
            return;
        }

        if (!$this->session->userdata('dso_user_id')) {
            redirect('dyafa/login');
            exit;
        }
    }

    /**
     * Restrict the current action to a set of allowed roles.
     * Renders a simple 403 view and stops execution if not permitted.
     *
     * Legacy check, kept working forever for the 40+ existing call sites.
     * New code (Administration module, new Phase 4 controllers) should
     * prefer require_permission() below, which is backed by the dynamic
     * dso_roles/dso_permissions/dso_role_permissions tables instead of a
     * hardcoded role-name array.
     */
    protected function require_role(array $roles)
    {
        $role = $this->session->userdata('dso_role');
        if (!in_array($role, $roles, true)) {
            $this->output->set_status_header(403);
            echo $this->load->view('dyafa/errors/403', array('role' => $role), true);
            exit;
        }
    }

    /**
     * Restrict the current action to roles holding a given dynamic
     * permission key (see dso_permissions / dso_role_permissions, managed
     * under Administration > Users & Roles). Resolves the logged-in
     * user's role_id from session and checks dso_role_permissions.
     */
    protected function require_permission($permission_key)
    {
        if (!$this->has_permission($permission_key)) {
            $this->output->set_status_header(403);
            echo $this->load->view('dyafa/errors/403', array('role' => $this->dso_role()), true);
            exit;
        }
    }

    /** True if the logged-in user's role holds the given permission key. */
    protected function has_permission($permission_key)
    {
        $role_id = $this->session->userdata('dso_role_id');
        if (!$role_id) {
            return false;
        }
        $this->load->model('dyafa/Dso_permissions_model');
        return $this->Dso_permissions_model->role_has_permission($role_id, $permission_key);
    }

    /**
     * Restrict the current Corporate Portal action to sub-roles that hold a
     * given capability (see dso_corporate_capabilities in dso_roles.php).
     * Mirrors require_role() but checks a capability instead of an exact role
     * match, since several corporate sub-roles can share one capability.
     */
    protected function require_corporate_capability($capability)
    {
        $role = $this->session->userdata('dso_role');
        $map = $this->config->item('dso_corporate_capabilities');
        $allowed = isset($map[$role]) ? $map[$role] : array();
        if (!in_array($capability, $allowed, true)) {
            $this->output->set_status_header(403);
            echo $this->load->view('dyafa/errors/403', array('role' => $role), true);
            exit;
        }
    }

    /** Convenience accessor for the logged-in dso user id. */
    protected function dso_user_id()
    {
        return (int) $this->session->userdata('dso_user_id');
    }

    protected function dso_role()
    {
        return $this->session->userdata('dso_role');
    }

    protected function dso_team_id()
    {
        return $this->session->userdata('dso_team_id');
    }

    /**
     * Territory scoping for Leads/Contracts/Accounts/Reservations list
     * queries (Teams > territory assignment). Returns null when the
     * logged-in user has no team, or their team has zero explicit
     * dso_team_accounts/dso_team_properties rows - both cases mean
     * "no restriction", so existing seeded data and any team an HOD hasn't
     * yet scoped keep behaving exactly as before this feature existed.
     */
    protected function my_team_account_ids()
    {
        $team_id = $this->dso_team_id();
        if (!$team_id) {
            return null;
        }
        $this->load->model('dyafa/Dso_teams_model');
        $ids = $this->Dso_teams_model->account_ids($team_id);
        return empty($ids) ? null : $ids;
    }

    protected function my_team_property_ids()
    {
        $team_id = $this->dso_team_id();
        if (!$team_id) {
            return null;
        }
        $this->load->model('dyafa/Dso_teams_model');
        $ids = $this->Dso_teams_model->property_ids($team_id);
        return empty($ids) ? null : $ids;
    }

    /**
     * Records one row into dso_audit_log. $before/$after may be a stdClass
     * row, an array, or null - both are JSON-encoded as-is. Called from the
     * top of every add()/edit()/delete() on the financially/legally
     * significant entities (Contracts, Accounts, Adhoc Sales, Properties,
     * Collections, Targets, Roles, Teams; Leads already had soft_delete()).
     * Never throws - a logging failure must not block the underlying CRUD
     * action, so any DB error here is caught and written to the CI log only.
     */
    protected function audit($table_name, $action, $row_id, $before = null, $after = null)
    {
        try {
            $this->load->model('dyafa/Dso_audit_log_model');
            $this->Dso_audit_log_model->record($this->dso_user_id(), $table_name, $row_id, $action, $before, $after);
        } catch (Exception $e) {
            log_message('error', 'DSO audit log write failed for ' . $table_name . '#' . $row_id . ': ' . $e->getMessage());
        }
    }

    /**
     * Soft-deletes a row via $model->delete($id) (every model listed above
     * now performs an UPDATE ... SET deleted_at instead of a hard DELETE)
     * and records the pre-delete row snapshot to the audit log in one call,
     * so controllers get both behaviors from a single line.
     */
    protected function soft_delete_row($model, $table_name, $id)
    {
        $before = $model->get($id);
        $model->delete($id);
        $this->audit($table_name, 'delete', $id, $before, null);
    }

    /**
     * Shared pagination helper (CI native Pagination library) for list
     * endpoints on entities most likely to grow unbounded (Leads,
     * Reservations, Notifications). Returns array('offset' => int,
     * 'per_page' => int, 'links' => string) - callers pass 'offset' into
     * their model's get_all($filters, $limit, $offset) and echo 'links' in
     * the view. $total_rows must be counted by the caller beforehand
     * (a cheap COUNT(*) with the same filters, no LIMIT).
     */
    protected function paginate($base_url, $total_rows, $per_page = 25)
    {
        $this->load->library('pagination');
        $offset = (int) $this->input->get('offset');

        $config = array(
            'base_url'           => $base_url,
            'total_rows'         => $total_rows,
            'per_page'           => $per_page,
            'use_page_numbers'   => false,
            'reuse_query_string' => true,
            'full_tag_open'      => '<div class="dso-pagination">',
            'full_tag_close'     => '</div>',
            'num_tag_open'       => '<span class="dso-page-num">',
            'num_tag_close'      => '</span>',
            'cur_tag_open'       => '<span class="dso-page-num dso-page-current">',
            'cur_tag_close'      => '</span>',
            'next_tag_open'      => '<span class="dso-page-nav">',
            'next_tag_close'     => '</span>',
            'prev_tag_open'      => '<span class="dso-page-nav">',
            'prev_tag_close'     => '</span>',
            'first_tag_open'     => '<span class="dso-page-nav">',
            'first_tag_close'    => '</span>',
            'last_tag_open'      => '<span class="dso-page-nav">',
            'last_tag_close'     => '</span>',
        );
        $this->pagination->initialize($config);

        return array(
            'offset'   => $offset,
            'per_page' => $per_page,
            'links'    => $this->pagination->create_links(),
        );
    }
}
