<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dso_ai_providers_model
 *
 * CRUD for configured LLM providers (see dso_ai_providers in
 * dyafa_sales_os_schema.sql). Owns the encrypt/decrypt boundary for
 * api_key_encrypted: insert()/update() encrypt on the way in, decrypt_key()
 * is the only way to get plaintext back out and is only ever called by
 * Dso_llm_client right before an HTTP call - never by a controller/view.
 */
class Dso_ai_providers_model extends CI_Model
{
    protected $table = 'dso_ai_providers';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('encryption');
    }

    public function get_all()
    {
        return $this->db->order_by('label', 'asc')->get($this->table)->result();
    }

    public function get($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function get_default()
    {
        return $this->db->where('is_default', 1)->where('is_enabled', 1)->get($this->table)->row();
    }

    public function get_enabled()
    {
        return $this->db->where('is_enabled', 1)->order_by('label', 'asc')->get($this->table)->result();
    }

    /** $data may include a plaintext 'api_key' - encrypted before storage, never stored raw. */
    public function insert($data)
    {
        $data = $this->_prepare($data);
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /** Blank/absent 'api_key' in $data means "keep the existing key" - it is left untouched. */
    public function update($id, $data)
    {
        $data = $this->_prepare($data);
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    public function delete($id)
    {
        return $this->db->where('id', $id)->delete($this->table);
    }

    /** Ensures exactly one row has is_default = 1. */
    public function set_default($id)
    {
        $this->db->update($this->table, array('is_default' => 0));
        return $this->db->where('id', $id)->update($this->table, array('is_default' => 1));
    }

    public function mark_test_result($id, $status, $message)
    {
        return $this->db->where('id', $id)->update($this->table, array(
            'last_test_status'  => $status,
            'last_test_message' => $message,
            'last_tested_at'    => date('Y-m-d H:i:s'),
        ));
    }

    /** Only caller should be Dso_llm_client, immediately before making the HTTP request. */
    public function decrypt_key($encrypted)
    {
        if (!$encrypted) {
            return null;
        }
        return $this->encryption->decrypt($encrypted);
    }

    /** Encrypts a plaintext 'api_key' key (if present) into api_key_encrypted + key_last4, normalizes extra_params to JSON. */
    protected function _prepare($data)
    {
        if (array_key_exists('api_key', $data)) {
            $plain = $data['api_key'];
            unset($data['api_key']);
            if ($plain !== null && $plain !== '') {
                $data['api_key_encrypted'] = $this->encryption->encrypt($plain);
                $data['key_last4'] = substr($plain, -4);
            }
        }
        if (array_key_exists('extra_params', $data) && is_array($data['extra_params'])) {
            $data['extra_params'] = json_encode($data['extra_params']);
        }
        return $data;
    }
}
