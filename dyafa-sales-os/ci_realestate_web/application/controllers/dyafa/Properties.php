<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Properties - master property list (BRD: Sales Coordinator manages
 * properties, uploads maps/property info). Read-only for every other
 * logged-in role; create/edit/delete/rates restricted to Sales Coordinator.
 *
 * dso_reservations.property and dso_contracts.allowed_properties keep
 * matching by plain name string (see implementation.md) - this module only
 * supplies the master list that reservation/contract forms pick from.
 */
class Properties extends Dso_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dyafa/Dso_properties_model');
        $this->load->model('dyafa/Dso_property_rates_model');
        $this->load->model('dyafa/Dso_property_blackout_dates_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $filters = array(
            'city'   => $this->input->get('city'),
            'status' => $this->input->get('status'),
        );
        $data['properties'] = $this->Dso_properties_model->get_all($filters);

        $city_options = array();
        foreach ($this->Dso_properties_model->get_distinct_cities() as $city) {
            $city_options[$city] = $city;
        }
        $data['dso_filter_fields'] = array(
            array('name' => 'city', 'label' => 'City', 'type' => 'select', 'options' => $city_options),
            array('name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => array('Active' => 'Active', 'Inactive' => 'Inactive')),
        );

        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/properties/list', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function add()
    {
        $this->require_role(array('Sales Coordinator'));

        $error = null;
        if ($this->input->method() === 'post') {
            $this->_validate();
            if ($this->form_validation->run() !== FALSE) {
                $name = $this->input->post('name');
                if ($this->Dso_properties_model->name_exists($name)) {
                    $error = 'A property with this name already exists.';
                } else {
                    $data = $this->_maybe_geocode($this->_collect_post());
                    list($ok, $files, $upload_error) = $this->_handle_uploads();
                    if (!$ok) {
                        $error = $upload_error;
                    } else {
                        $data = array_merge($data, $files);
                        $data['created_by'] = $this->dso_user_id();
                        $data['created_at'] = date('Y-m-d H:i:s');
                        $new_id = $this->Dso_properties_model->insert($data);
                        $this->audit('dso_properties', 'create', $new_id, null, $data);
                        $this->session->set_flashdata('dso_success', 'Property added.');
                        redirect('dyafa/properties');
                        return;
                    }
                }
            }
        }
        $data['property'] = null;
        $data['error'] = $error;
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/properties/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function edit($id)
    {
        $this->require_role(array('Sales Coordinator'));

        $property = $this->Dso_properties_model->get($id);
        if (!$property) {
            show_404();
            return;
        }
        $error = null;
        if ($this->input->method() === 'post') {
            $this->_validate();
            if ($this->form_validation->run() !== FALSE) {
                $name = $this->input->post('name');
                if ($this->Dso_properties_model->name_exists($name, $id)) {
                    $error = 'A property with this name already exists.';
                } else {
                    $data = $this->_maybe_geocode($this->_collect_post());
                    list($ok, $files, $upload_error) = $this->_handle_uploads();
                    if (!$ok) {
                        $error = $upload_error;
                    } else {
                        $data = array_merge($data, $files);
                        $data['updated_at'] = date('Y-m-d H:i:s');
                        $this->Dso_properties_model->update($id, $data);
                        $this->audit('dso_properties', 'update', $id, $property, $data);
                        $this->session->set_flashdata('dso_success', 'Property updated.');
                        redirect('dyafa/properties');
                        return;
                    }
                }
            }
        }
        $data['property'] = $property;
        $data['error'] = $error;
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/properties/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function delete($id)
    {
        $this->require_role(array('Sales Coordinator'));

        $property = $this->Dso_properties_model->get($id);
        if ($property) {
            $this->_unlink_if_exists($property->map_file);
            $this->_unlink_if_exists($property->info_file);
        }
        $this->soft_delete_row($this->Dso_properties_model, 'dso_properties', $id);
        $this->session->set_flashdata('dso_success', 'Property deleted.');
        redirect('dyafa/properties');
    }

    public function rates($id)
    {
        $property = $this->Dso_properties_model->get($id);
        if (!$property) {
            show_404();
            return;
        }
        if ($this->input->method() === 'post') {
            $this->require_role(array('Sales Coordinator'));
            $this->Dso_property_rates_model->insert(array(
                'property_id' => $id,
                'rate_type'   => $this->input->post('rate_type'),
                'rate'        => $this->input->post('rate'),
                'created_at'  => date('Y-m-d H:i:s'),
            ));
            redirect('dyafa/properties/rates/' . $id);
            return;
        }
        $data['property'] = $property;
        $data['rates'] = $this->Dso_property_rates_model->get_for_property($id);
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/properties/rates', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function delete_rate($rate_id, $property_id)
    {
        $this->require_role(array('Sales Coordinator'));
        $this->Dso_property_rates_model->delete($rate_id);
        redirect('dyafa/properties/rates/' . $property_id);
    }

    /**
     * Property Management > Availability Settings. Sales-Coordinator-gated,
     * same as rates(). Simple is_bookable toggle + blackout-dates list -
     * NOT a day-by-day room-inventory calendar (see implementation.md /
     * migration 010 for the scope note: "availability" elsewhere in this
     * app means contract-eligibility, which is unrelated to this screen).
     */
    public function availability($id)
    {
        $this->require_role(array('Sales Coordinator'));

        $property = $this->Dso_properties_model->get($id);
        if (!$property) {
            show_404();
            return;
        }
        if ($this->input->method() === 'post') {
            $action = $this->input->post('action');
            if ($action === 'toggle_bookable') {
                $this->Dso_properties_model->set_bookable($id, $this->input->post('is_bookable') ? true : false);
                $this->session->set_flashdata('dso_success', 'Availability updated.');
            } elseif ($action === 'add_blackout') {
                $this->Dso_property_blackout_dates_model->insert(array(
                    'property_id'   => $id,
                    'blackout_date' => $this->input->post('blackout_date'),
                    'reason'        => $this->input->post('reason'),
                    'created_at'    => date('Y-m-d H:i:s'),
                ));
                $this->session->set_flashdata('dso_success', 'Blackout date added.');
            }
            redirect('dyafa/properties/availability/' . $id);
            return;
        }
        $data['property'] = $property;
        $data['blackout_dates'] = $this->Dso_property_blackout_dates_model->get_by_property($id);
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/properties/availability', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function delete_blackout_date($id, $property_id)
    {
        $this->require_role(array('Sales Coordinator'));
        $this->Dso_property_blackout_dates_model->delete($id);
        redirect('dyafa/properties/availability/' . $property_id);
    }

    protected function _validate()
    {
        $this->form_validation->set_rules('name', 'Property Name', 'required');
        $this->form_validation->set_rules('total_rooms', 'Total Rooms', 'permit_empty|integer');
    }

    protected function _collect_post()
    {
        return array(
            'name'        => $this->input->post('name'),
            'city'        => $this->input->post('city'),
            'address'     => $this->input->post('address'),
            'lat'         => $this->input->post('lat') !== '' ? $this->input->post('lat') : null,
            'lng'         => $this->input->post('lng') !== '' ? $this->input->post('lng') : null,
            'description' => $this->input->post('description'),
            'total_rooms' => $this->input->post('total_rooms') ?: null,
            'status'      => $this->input->post('status') ?: 'Active',
        );
    }

    /**
     * EXTENSION POINT: no real geocoding provider is contracted yet. When the
     * user leaves lat/lng blank on the form, populate them via
     * dso_maps_mode (application/config/dso_integrations.php): 'mock'
     * (default) generates a deterministic city-based geocode via
     * Dso_maps_mock; 'off' leaves lat/lng null (no map rendered); 'live'
     * would call a real provider here in the future.
     */
    protected function _maybe_geocode(array $data)
    {
        if (!empty($data['lat']) && !empty($data['lng'])) {
            return $data;
        }
        if (empty($data['city'])) {
            return $data;
        }
        $this->load->config('dso_integrations');
        $mode = $this->config->item('dso_maps_mode');
        $mode = $mode ? $mode : 'mock';
        if ($mode === 'off') {
            return $data;
        }

        $this->load->library('Dso_maps_mock');
        $result = $this->Dso_maps_mock->geocode((object) $data);
        $data['lat'] = $result['lat'];
        $data['lng'] = $result['lng'];
        return $data;
    }

    /**
     * Handles the optional map_file / info_file uploads via CI's native
     * upload library, following the same flat per-purpose uploads/ folder
     * convention already used elsewhere in this app (uploads/logo,
     * uploads/photo_gallery, etc). Returns array($ok, $files, $error).
     */
    protected function _handle_uploads()
    {
        $files = array();
        $upload_dir = FCPATH . 'uploads/property_maps/';

        foreach (array('map_file' => 'gif|jpg|jpeg|png|pdf', 'info_file' => 'pdf|doc|docx') as $field => $types) {
            if (empty($_FILES[$field]['name'])) {
                continue;
            }
            $config = array(
                'upload_path'   => $upload_dir,
                'allowed_types' => $types,
                'max_size'      => 5120,
                'encrypt_name'  => TRUE,
            );
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if (!$this->upload->do_upload($field)) {
                return array(false, array(), $this->upload->display_errors('', ''));
            }
            $files[$field] = $this->upload->data('file_name');
        }
        return array(true, $files, null);
    }

    protected function _unlink_if_exists($filename)
    {
        if ($filename) {
            $path = FCPATH . 'uploads/property_maps/' . $filename;
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }
}
