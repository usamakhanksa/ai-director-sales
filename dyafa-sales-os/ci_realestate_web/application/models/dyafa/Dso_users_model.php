<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dso_users_model extends CI_Model
{
    protected $table = 'dso_users';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('encryption');
    }

    public function get_by_username($username)
    {
        return $this->db->where('username', $username)->get($this->table)->row();
    }

    public function get($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function all($role = null)
    {
        $this->db->from($this->table);
        if ($role) {
            $this->db->where('role', $role);
        }
        $this->db->order_by('name', 'asc');
        return $this->db->get()->result();
    }

    public function first_by_role($role)
    {
        return $this->db->where('role', $role)->order_by('id', 'asc')->limit(1)->get($this->table)->row();
    }

    public function get_by_account($account_id)
    {
        return $this->db->where('account_id', $account_id)->order_by('name', 'asc')->get($this->table)->result();
    }

    public function sales_executives()
    {
        return $this->db->where('role', 'Sales Executive')->get($this->table)->result();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    /**
     * Persists a freshly-verified TOTP secret and marks 2FA enabled.
     * Encrypted via the same CI Encryption boundary as Dso_ai_providers_model
     * / Dso_integration_credentials_model - never stored or displayed as
     * plaintext outside get_totp_secret(), which is only ever called
     * immediately before verifying a submitted code.
     */
    public function enable_totp($id, $plain_secret)
    {
        return $this->db->where('id', $id)->update($this->table, array(
            'totp_secret_encrypted' => $this->encryption->encrypt($plain_secret),
            'totp_enabled'          => 1,
        ));
    }

    /** Only caller should be Portal::verify_2fa(), immediately before checking a submitted code. */
    public function get_totp_secret($id)
    {
        $user = $this->get($id);
        if (!$user || !$user->totp_secret_encrypted) {
            return null;
        }
        return $this->encryption->decrypt($user->totp_secret_encrypted);
    }
}
