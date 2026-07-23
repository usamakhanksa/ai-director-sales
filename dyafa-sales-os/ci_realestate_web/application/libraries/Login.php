<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CI_Login {

	public function Index(){}
	
	public function isLogin()	{
		$site_url = site_url();		
		if(isset($_SESSION['f_logged_in']) && $_SESSION['f_logged_in']==TRUE && isset($_SESSION['site_url']) && $_SESSION['site_url'] == $site_url )
			return true;		
		else
			return false;
	}
}

/* End of file Myhelpers.php */