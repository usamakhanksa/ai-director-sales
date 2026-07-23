<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Db_controller extends MY_Controller
{
    function __construct()
    {

        parent::__construct();
        if (!$this->isAdminLogin()) {
            redirect('/admin/logins', 'location');
        }

        /*if(!$this->has_method_access())
		{
			redirect('/admin/main/','location');
		}*/


        $this->load->library('Package_lib');
        $user_id = $this->session->userdata('user_id');
        $this->post_blog_credit = $this->package_lib->get_credits_by_user_id($user_id, 'post_blog_credit');
    }
    public function print_sql()
    {
        $CI = &get_instance();
        $this->load->model('Common_model');
        $this->load->library('Global_lib');
        $drop = $this->Common_model->commonQuery('DROP TABLE `form_enquiries`;');
        if ($drop) {

            $qry = "CREATE TABLE `form_enquiries` ( `enquiry_id` INT(11) NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL , `property_id` INT(11) NOT NULL , `first_name` VARCHAR(255) NOT NULL , `email` TEXT NOT NULL , `subject` VARCHAR(255) NOT NULL , `message` TEXT NOT NULL , `created_at` INT(11) NOT NULL , PRIMARY KEY (`enquiry_id`)) ENGINE = InnoDB;";
            $result = $this->Common_model->commonQuery($qry);
            var_dump($result);
        }
        exit;
    }
}
