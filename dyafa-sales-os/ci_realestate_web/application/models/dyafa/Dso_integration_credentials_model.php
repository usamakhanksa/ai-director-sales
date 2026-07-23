<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dso_integration_credentials_model
 *
 * Encrypted-at-rest storage for the PMS/Finance/Maps/Payment/Reporting API
 * keys, replacing the plaintext var_export() write that used to live in
 * Admin/Integrations.php. Mirrors Dso_ai_providers_model's encrypt/decrypt
 * boundary exactly: decrypt_key() is the only way to get plaintext back out,
 * and is only ever called by the integration libraries immediately before an
 * HTTP call - never by a controller/view (only key_last4 is ever displayed).
 * mode/endpoint/timeout remain non-secret settings in
 * application/config/dso_integrations.php - this table only owns the key.
 */
class Dso_integration_credentials_model extends CI_Model
{
    protected $table = 'dso_integration_credentials';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('encryption');
    }

    /** Keyed by integration_key (e.g. 'dso_pms', 'dso_finance', ...). */
    public function get_all_keyed()
    {
        $rows = $this->db->get($this->table)->result();
        $keyed = array();
        foreach ($rows as $row) {
            $keyed[$row->integration_key] = $row;
        }
        return $keyed;
    }

    public function get($integration_key)
    {
        return $this->db->where('integration_key', $integration_key)->get($this->table)->row();
    }

    /**
     * Convenience accessor for the integration libraries: returns the
     * decrypted plaintext key for $integration_key, or '' if none is set.
     * This is the ONLY place outside decrypt_key() itself that plaintext is
     * produced, and it is never echoed to a view.
     */
    public function get_key($integration_key)
    {
        $row = $this->get($integration_key);
        if (!$row || !$row->api_key_encrypted) {
            return '';
        }
        return (string) $this->decrypt_key($row->api_key_encrypted);
    }

    /**
     * Upserts the encrypted key for one integration. A blank/absent
     * $plain_api_key means "keep the existing key" - it is left untouched
     * (same convention as Dso_ai_providers_model::update()).
     */
    public function upsert($integration_key, $plain_api_key, $updated_by = null)
    {
        $existing = $this->get($integration_key);

        $data = array('updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $updated_by);
        if ($plain_api_key !== null && $plain_api_key !== '') {
            $data['api_key_encrypted'] = $this->encryption->encrypt($plain_api_key);
            $data['key_last4'] = substr($plain_api_key, -4);
        }

        if ($existing) {
            return $this->db->where('integration_key', $integration_key)->update($this->table, $data);
        }

        $data['integration_key'] = $integration_key;
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /** Only callers should be get_key() above and the integration libraries, immediately before an HTTP request. */
    public function decrypt_key($encrypted)
    {
        if (!$encrypted) {
            return null;
        }
        return $this->encryption->decrypt($encrypted);
    }
}
