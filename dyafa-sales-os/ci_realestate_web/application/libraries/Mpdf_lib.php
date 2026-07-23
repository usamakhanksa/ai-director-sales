<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include("mPDF/mpdf.php");

class Mpdf_lib{
	
	
	public function genHtml($html,$args = array()){
			
			$mpdf = new mPDF(); 

			$mpdf->WriteHTML($html);
			
			
			if(!isset($args['prop_title']))
				$mpdf->Output();
			else
				$mpdf->Output($args['prop_title'].".pdf" , "D");
			
	}
	
	
}	
/* End of file Myhelpers.php */