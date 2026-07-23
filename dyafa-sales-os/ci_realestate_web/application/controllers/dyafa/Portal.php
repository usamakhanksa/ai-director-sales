<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Portal - Corporate Self-Service Portal.
 *
 * Restricted to role 'Corporate Client'. Reuses the same Dso_Controller
 * session mechanism (dso_user_id / dso_role) as the internal staff app,
 * but every query here is filtered by the logged-in user's account_id so
 * a client can never see another account's data.
 */
class Portal extends Dso_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dyafa/Dso_users_model');
        $this->load->model('dyafa/Dso_accounts_model');
        $this->load->model('dyafa/Dso_reservations_model');
        $this->load->model('dyafa/Dso_collections_model');
        $this->load->model('dyafa/Dso_contracts_model');
        $this->load->model('dyafa/Dso_properties_model');
        $this->load->model('dyafa/Dso_property_rates_model');
        $this->load->library('form_validation');

        // Any already-authenticated non-corporate user should not be able to
        // browse the portal actions (except the login/authenticate/logout
        // which are whitelisted as public in Dso_Controller). Any of the
        // corporate sub-roles (see dso_corporate_roles) may reach the portal;
        // individual actions are further gated by require_corporate_capability().
        $method = strtolower($this->router->fetch_method());
        if (!in_array($method, array('login', 'authenticate', 'logout', 'setup_2fa', 'verify_2fa'), true)) {
            $this->require_role($this->config->item('dso_corporate_roles'));
        }
    }

    public function login()
    {
        if ($this->session->userdata('dso_user_id') && in_array($this->dso_role(), $this->config->item('dso_corporate_roles'), true)) {
            redirect('dyafa/portal/dashboard');
            return;
        }
        $data['error'] = $this->session->flashdata('dso_login_error');
        $this->load->view('dyafa/layout/guest_header');
        $this->load->view('dyafa/portal/login', $data);
        $this->load->view('dyafa/layout/guest_footer');
    }

    public function authenticate()
    {
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('dso_login_error', validation_errors());
            redirect('dyafa/portal/login');
            return;
        }

        $user = $this->Dso_users_model->get_by_username($this->input->post('username'));

        if (!$user || !in_array($user->role, $this->config->item('dso_corporate_roles'), true) || $user->status !== 'Active'
            || !password_verify($this->input->post('password'), $user->password)) {
            $this->session->set_flashdata('dso_login_error', 'Invalid credentials.');
            redirect('dyafa/portal/login');
            return;
        }

        // BRD Section 10 lists 2FA as "(Optional)", but CorporateFinance sees
        // invoices/credit limits/outstanding balances (Section 10/16) - it is
        // made mandatory for that sub-role specifically, not optional. First
        // login forces enrollment (setup_2fa); every login after that
        // requires a code (verify_2fa). Other corporate sub-roles are
        // unaffected and log in as before.
        if ($user->role === 'CorporateFinance') {
            $this->session->set_userdata('dso_pending_2fa_user_id', $user->id);
            redirect($user->totp_enabled ? 'dyafa/portal/verify_2fa' : 'dyafa/portal/setup_2fa');
            return;
        }

        $this->_complete_login($user);
    }

    /**
     * Mandatory 2FA enrollment for a CorporateFinance user's first login.
     * The generated secret is held only in session until a submitted code
     * proves the user has actually added it to an authenticator app -
     * nothing is persisted to dso_users until that verification succeeds.
     */
    public function setup_2fa()
    {
        $pending_id = $this->session->userdata('dso_pending_2fa_user_id');
        if (!$pending_id) {
            redirect('dyafa/portal/login');
            return;
        }
        $user = $this->Dso_users_model->get($pending_id);
        if (!$user || $user->totp_enabled) {
            redirect('dyafa/portal/login');
            return;
        }

        $this->load->library('dso_totp');
        $secret = $this->session->userdata('dso_pending_2fa_secret');
        if (!$secret) {
            $secret = $this->dso_totp->generate_secret();
            $this->session->set_userdata('dso_pending_2fa_secret', $secret);
        }

        $error = null;
        if ($this->input->method() === 'post') {
            if ($this->dso_totp->verify_code($secret, $this->input->post('code'))) {
                $this->Dso_users_model->enable_totp($pending_id, $secret);
                $this->session->unset_userdata('dso_pending_2fa_secret');
                $this->_complete_login($user);
                return;
            }
            $error = 'Invalid code. Please try again.';
        }

        $data['secret'] = $secret;
        $data['otpauth_uri'] = $this->dso_totp->otpauth_uri($secret, $user->username);
        $data['error'] = $error;
        $this->load->view('dyafa/layout/guest_header');
        $this->load->view('dyafa/portal/setup_2fa', $data);
        $this->load->view('dyafa/layout/guest_footer');
    }

    /** Mandatory 2FA code check for a CorporateFinance user who already completed enrollment. */
    public function verify_2fa()
    {
        $pending_id = $this->session->userdata('dso_pending_2fa_user_id');
        if (!$pending_id) {
            redirect('dyafa/portal/login');
            return;
        }
        $user = $this->Dso_users_model->get($pending_id);
        if (!$user || !$user->totp_enabled) {
            redirect('dyafa/portal/login');
            return;
        }

        $error = null;
        if ($this->input->method() === 'post') {
            $this->load->library('dso_totp');
            $secret = $this->Dso_users_model->get_totp_secret($pending_id);
            if ($secret && $this->dso_totp->verify_code($secret, $this->input->post('code'))) {
                $this->_complete_login($user);
                return;
            }
            $error = 'Invalid code. Please try again.';
        }

        $data['error'] = $error;
        $this->load->view('dyafa/layout/guest_header');
        $this->load->view('dyafa/portal/verify_2fa', $data);
        $this->load->view('dyafa/layout/guest_footer');
    }

    /** Finalizes a login (post-password, post-2FA-if-required): sets the real session and clears any pending-2FA state. */
    protected function _complete_login($user)
    {
        $this->session->unset_userdata('dso_pending_2fa_user_id');
        $this->session->set_userdata(array(
            'dso_user_id'    => $user->id,
            'dso_name'       => $user->name,
            'dso_role'       => $user->role,
            'dso_account_id' => $user->account_id,
        ));
        redirect('dyafa/portal/dashboard');
    }

    public function logout()
    {
        $this->session->unset_userdata('dso_user_id');
        $this->session->unset_userdata('dso_name');
        $this->session->unset_userdata('dso_role');
        $this->session->unset_userdata('dso_account_id');
        redirect('dyafa/portal/login');
    }

    protected function my_account_id()
    {
        return (int) $this->session->userdata('dso_account_id');
    }

    public function dashboard()
    {
        $account_id = $this->my_account_id();
        $data['account'] = $this->Dso_accounts_model->get($account_id);
        $data['reservations'] = $this->Dso_reservations_model->get_all($account_id);
        $data['outstanding'] = $this->Dso_collections_model->outstanding_total_for_account($account_id);
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/portal/dashboard', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function reservations()
    {
        $data['reservations'] = $this->Dso_reservations_model->get_all($this->my_account_id());
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/portal/reservations', $data);
        $this->load->view('dyafa/layout/footer');
    }

    /**
     * Hotel/availability search - lists the properties the account's
     * contract allows, with the account's corporate rate where one is set
     * (falling back to the property's standard rate list). Feeds into
     * reservation_new via a prefilled property/rate query string. No live
     * room-inventory availability is tracked anywhere in this build (no
     * per-date room count table exists) - "availability" here means
     * "contractually allowed", consistent with the existing reservation
     * validation in Dso_reservations_model::validate_against_contract().
     */
    public function search()
    {
        $account = $this->Dso_accounts_model->get($this->my_account_id());
        $allowed = array();
        $corporate_rates = array();

        if ($account && $account->contract_id) {
            $allowed = $this->Dso_contracts_model->get_allowed_properties($account->contract_id);
            $corporate_rates = $this->Dso_contracts_model->get_corporate_rates($account->contract_id);
        }

        // No allowed_properties restriction on the contract (or no contract at all) means every active property is bookable.
        $properties = empty($allowed)
            ? $this->Dso_properties_model->get_all('Active')
            : array_filter($this->Dso_properties_model->get_all('Active'), function ($p) use ($allowed) {
                return in_array($p->name, $allowed, true);
            });

        $results = array();
        foreach ($properties as $p) {
            $rate = isset($corporate_rates[$p->name]) ? (float) $corporate_rates[$p->name] : null;
            if ($rate === null) {
                $standard = $this->Dso_property_rates_model->get_for_property($p->id);
                $rate = !empty($standard) ? (float) $standard[0]->rate : null;
            }
            $results[] = array('property' => $p, 'rate' => $rate);
        }

        $data['results'] = $results;
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/portal/search', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function reservation_new()
    {
        $this->require_corporate_capability('create_reservation');

        $error = null;
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('property', 'Property', 'required');
            $this->form_validation->set_rules('check_in', 'Check-in', 'required');
            $this->form_validation->set_rules('check_out', 'Check-out', 'required');
            $this->form_validation->set_rules('rate', 'Rate', 'required|numeric');
            $this->form_validation->set_rules('room_nights', 'Room Nights', 'required|integer');
            $this->form_validation->set_rules('total_amount', 'Total Amount', 'required|numeric');

            if ($this->form_validation->run() !== FALSE) {
                $account_id = $this->my_account_id();
                $property = $this->input->post('property');
                $total_amount = $this->input->post('total_amount');

                list($ok, $msg) = $this->Dso_reservations_model->validate_against_contract($account_id, $property, $total_amount);
                if (!$ok) {
                    $error = $msg;
                } else {
                    $this->Dso_reservations_model->insert(array(
                        'account_id'   => $account_id,
                        'property'     => $property,
                        'check_in'     => $this->input->post('check_in'),
                        'check_out'    => $this->input->post('check_out'),
                        'rate'         => $this->input->post('rate'),
                        'room_nights'  => $this->input->post('room_nights'),
                        'total_amount' => $total_amount,
                        'status'       => 'Pending',
                        'created_by'   => $this->dso_user_id(),
                        'created_at'   => date('Y-m-d H:i:s'),
                    ));
                    $this->session->set_flashdata('dso_success', 'Reservation request submitted.');
                    redirect('dyafa/portal/reservations');
                    return;
                }
            }
        }
        $data['error'] = $error;
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/portal/reservation_new', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function reservation_cancel($id)
    {
        $this->require_corporate_capability('cancel_reservation');

        // Server-side ownership check - never trust the button being hidden client-side.
        $reservation = $this->Dso_reservations_model->get($id);
        if (!$reservation || (int) $reservation->account_id !== $this->my_account_id()) {
            show_404();
            return;
        }
        $this->Dso_reservations_model->cancel($id);
        $this->session->set_flashdata('dso_success', 'Reservation cancelled.');
        redirect('dyafa/portal/reservations');
    }

    public function statement()
    {
        $this->require_corporate_capability('view_statement');

        $account_id = $this->my_account_id();
        $data['account'] = $this->Dso_accounts_model->get($account_id);
        $data['collections'] = $this->Dso_collections_model->get_by_account($account_id);
        $data['reservations'] = $this->Dso_reservations_model->get_all($account_id);
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/portal/statement', $data);
        $this->load->view('dyafa/layout/footer');
    }

    /**
     * Invoice download - renders a single collection/invoice row as a PDF via
     * the already-present Dompdf_lib (no new PDF library needed), ownership-
     * checked against the logged-in account the same way reservation_cancel()
     * is, so a client can never download another account's invoice.
     */
    public function invoice_download($collection_id)
    {
        $this->require_corporate_capability('view_statement');

        $collection = $this->Dso_collections_model->get($collection_id);
        if (!$collection || (int) $collection->account_id !== $this->my_account_id()) {
            show_404();
            return;
        }
        $account = $this->Dso_accounts_model->get($this->my_account_id());

        $html = $this->load->view('dyafa/portal/invoice_pdf', array(
            'collection' => $collection,
            'account'    => $account,
        ), true);

        $this->load->library('Dompdf_lib');
        $this->dompdf_lib->write($html, array('prop_title' => 'Invoice-' . $collection->invoice_no));
    }

    /** Company User Management (BRD Section 11) - CorporateAdmin only, scoped to their own account. */
    public function users()
    {
        $this->require_corporate_capability('manage_users');

        $data['users'] = $this->Dso_users_model->get_by_account($this->my_account_id());
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/portal/users', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function user_add()
    {
        $this->require_corporate_capability('manage_users');

        $error = null;
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('name', 'Name', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
            $this->form_validation->set_rules('role', 'Role', 'required|in_list[CorporateAdmin,CorporateHR,CorporateFinance,CorporateTravelCoordinator,CorporateProjectManager]');

            if ($this->form_validation->run() !== FALSE) {
                if ($this->Dso_users_model->get_by_username($this->input->post('username'))) {
                    $error = 'That username is already taken.';
                } else {
                    $this->Dso_users_model->insert(array(
                        'name'       => $this->input->post('name'),
                        'email'      => $this->input->post('email'),
                        'username'   => $this->input->post('username'),
                        'password'   => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                        'role'       => $this->input->post('role'),
                        'account_id' => $this->my_account_id(),
                        'status'     => 'Active',
                        'created_at' => date('Y-m-d H:i:s'),
                    ));
                    $this->session->set_flashdata('dso_success', 'Company user created.');
                    redirect('dyafa/portal/users');
                    return;
                }
            } else {
                $error = validation_errors();
            }
        }
        $data['error'] = $error;
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/portal/user_form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    /**
     * Edit an existing company user (BRD Section 11 gap: previously the only
     * way to fix a mistyped email or change a sub-role was deactivate +
     * recreate). Mirrors user_add()'s validation; password is optional here -
     * a blank field leaves the existing hash untouched. Ownership-checked
     * against account_id like every other Portal method, so a CorporateAdmin
     * can never edit a user belonging to another company.
     */
    public function user_edit($id)
    {
        $this->require_corporate_capability('manage_users');

        $user = $this->Dso_users_model->get($id);
        if (!$user || (int) $user->account_id !== $this->my_account_id()) {
            show_404();
            return;
        }

        $error = null;
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('name', 'Name', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->form_validation->set_rules('password', 'Password', 'permit_empty|min_length[6]');
            $this->form_validation->set_rules('role', 'Role', 'required|in_list[CorporateAdmin,CorporateHR,CorporateFinance,CorporateTravelCoordinator,CorporateProjectManager]');

            if ($this->form_validation->run() !== FALSE) {
                $existing = $this->Dso_users_model->get_by_username($this->input->post('username'));
                if ($existing && (int) $existing->id !== (int) $id) {
                    $error = 'That username is already taken.';
                } else {
                    $data = array(
                        'name'     => $this->input->post('name'),
                        'email'    => $this->input->post('email'),
                        'username' => $this->input->post('username'),
                        'role'     => $this->input->post('role'),
                    );
                    if ($this->input->post('password')) {
                        $data['password'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
                    }
                    $this->Dso_users_model->update($id, $data);
                    $this->session->set_flashdata('dso_success', 'Company user updated.');
                    redirect('dyafa/portal/users');
                    return;
                }
            } else {
                $error = validation_errors();
            }
        }
        $data['user'] = $user;
        $data['error'] = $error;
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/portal/user_form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function user_toggle_status($id)
    {
        $this->require_corporate_capability('manage_users');

        // Server-side ownership check - a CorporateAdmin may only manage users within their own account.
        $user = $this->Dso_users_model->get($id);
        if (!$user || (int) $user->account_id !== $this->my_account_id()) {
            show_404();
            return;
        }
        $this->Dso_users_model->update($id, array('status' => $user->status === 'Active' ? 'Inactive' : 'Active'));
        redirect('dyafa/portal/users');
    }
}
