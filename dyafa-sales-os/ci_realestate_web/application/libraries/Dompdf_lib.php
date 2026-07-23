<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:  DOMPDF
* 
* Author: Geordy James 
*         @geordyjames
*          
*Location : https://github.com/geordyjames/Codeigniter-Dompdf-v0.7.0-Library
* Origin API Class: https://github.com/dompdf/dompdf
*          
* Created:  24.01.2017
* Created by Geordy James to give support to dompdf 0.7.0 and above 
* 
* Description:  This is a Codeigniter library which allows you to convert HTML to PDF with the DOMPDF library
* 
*/
require_once APPPATH.'third_party/dompdf/autoload.inc.php';

//use Dompdf\Dompdf;
use Dompdf\Dompdf;

class Dompdf_lib {
		
	public function __construct() {

		$pdf = new Dompdf();
        
        //$pdf = new \Dompdf\Dompdf();
        
        $CI =& get_instance();
		$CI->dompdf = $pdf;
		
	}
	
	public function write($html,$args = array()){
		
		$dompdf = new Dompdf(array('enable_remote' => true));
		$dompdf->loadHtml($html);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4', 'landscape');
		
		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser
		if(!isset($args['prop_title']))
			$dompdf->stream();
		else
			$dompdf->stream($args['prop_title'].".pdf" , array("Attachment" => false));	
		
	}
	
}