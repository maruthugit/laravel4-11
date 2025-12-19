<?php    
class qr_code {
	
	function qr_code() {}
	
	// File Name must in png
	function generate($text='', $dir='', $file_name='') {
		include "phpqrcode/qrlib.php";
		
		if (!is_dir($dir))
			mkdir($dir);
		
		$filename = $dir . '/' . $file_name;
		$errorCorrectionLevel = 'H'; // 'L','M','Q','H'
		$matrixPointSize = 8; // 1 - 10
		
		QRcode::png($text, $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
	}
	
}
?>