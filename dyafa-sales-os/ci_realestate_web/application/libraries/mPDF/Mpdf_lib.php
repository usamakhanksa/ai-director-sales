<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include("mpdf.php");

class Mpdf_lib{
	
	public function gen($html,$email){
	
		/*$html = '
		<h1><a name="top"></a>mPDF</h1>
		<h2>Basic HTML Example</h2>
		This file demonstrates most of the HTML elements.
		<h3>Heading 3</h3>
		<h4>Heading 4</h4>
		<h5>Heading 5</h5>
		<h6>Heading 6</h6>';*/
	
		//$mpdf=new mPDF('c'); 
		
		//$mpdf->WriteHTML($html);
		//$mpdf->Output();
		//$mpdf->Output('filename.pdf','F');
		
		//$email = "azizchouhan@gmail.com";
		//echo 'email = '.$email;
		
		//error_reporting(-1);
		//ini_set('display_errors', 'On');
		//set_error_handler("var_dump");
		
		
		$mpdf=new mPDF('','A4');
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->autoLangToFont = true;
		$mpdf->defaultPageNumStyle = 'arabic-indic';
		$mpdf->WriteHTML($html); // Writing html to pdf
		// FOR EMAIL
		$content = $mpdf->Output('', 'S'); // Saving pdf to attach to email 
		//$mpdf->Output('invoice.pdf', 'D'); // Saving pdf to attach to email 
		$content = chunk_split(base64_encode($content));
		
		$attachment = $content ;
		// Email settings
		$mailto = $email;
		$domain = "ejadah-group.com";
		$from_name = 'Invoice Manager';
		$from_mail = 'reply@'.$domain;
		$replyto = 'reply@'.$domain;
		$uid = md5(uniqid(time())); 
		$subject = 'attached invoice PDF';
		$message = 'Download the attached pdf';
		$filename = 'invoice.pdf';
		
		
		//$header = "From: ".$from_name." <".$from_mail.">\r\n";
//		$header .= "Reply-To: ".$replyto."\r\n";
//		$header .= "MIME-Version: 1.0\r\n";
//		$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
//		$header .= "This is a multi-part message in MIME format.\r\n";
//		$header .= "--".$uid."\r\n";
//		$header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
//		$header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
//		$header .= $message."\r\n\r\n";
//		$header .= "--".$uid."\r\n";
//		$header .= "Content-Type: application/pdf; name=\"".$filename."\"\r\n";
//		$header .= "Content-Transfer-Encoding: base64\r\n";
//		$header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
//		$header .= $content."\r\n\r\n";
//		$header .= "--".$uid."--";
		
		
		 $separator = md5(time());

		// carriage return type (we use a PHP end of line constant)
		$eol = PHP_EOL;
		
		// main header
		$headers  = "From: ".$from_name." <".$from_mail.">".$eol;
		$headers .= "MIME-Version: 1.0".$eol; 
		$headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"";
		
		// no more headers after this, we start the body! //
		
		$body .= "--".$separator.$eol;
		$body .= "Content-Transfer-Encoding: 7bit".$eol.$eol;
		///$body .= "This is a MIME encoded message.".$eol;
		
		// message
		$body .= "--".$separator.$eol;
		$body .= "Content-Type: text/html; charset=\"iso-8859-1\"".$eol;
		$body .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
		$body .= $message.$eol;
		
		// attachment
		$body .= "--".$separator.$eol;
		$body .= "Content-Type: application/octet-stream; name=\"".$filename."\"".$eol; 
		$body .= "Content-Transfer-Encoding: base64".$eol;
		$body .= "Content-Disposition: attachment".$eol.$eol;
		$body .= $attachment.$eol;
		$body .= "--".$separator."--";
		

		
		
		
		//$is_sent = @mail($mailto, $subject, "", $headers);
		$is_sent = @mail($mailto, $subject, $body, $headers);
		//$is_sent = mail($mailto, $subject, "", $header);
		//$mpdf->Output(); // For sending Output to browser
		//$mpdf->Output('lubus_mdpf_demo.pdf','D'); // For Download
		
		//$msg = "First line of text\nSecond line of text";
		
		// use wordwrap() if lines are longer than 70 characters
		//$msg = wordwrap($msg,70);
		
		// send email
		//mail("azizchouhan@gmail.com","My subject",$msg);
		
		if(is_sent)
			echo "done";
		else
			echo 'problem';
	}
	

	public function genHtml($html){
			
			$mpdf = new mPDF(); 

			$mpdf->WriteHTML($html);
			
			$mpdf->Output();
			
			return $mpdf->Output('filename.pdf','F');
	}
	
	
}	
/* End of file Myhelpers.php */