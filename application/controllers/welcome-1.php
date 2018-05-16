<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH.'/third_party/vendor/autoload.php';

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{	

		$this->load->view('welcome_message');
	}

	public function generate(){
		//echo 123;
		//die();
		//Loading Excel library
		$this->load->library('Excel');
		$file = "./uploads/abc.xls";
		$excelObj = PHPExcel_IOFactory::load($file);

		//$excelObj = $excelReader->load($tmpfname);
		$worksheet = $excelObj->getSheet(0);
		$cell_collection = $worksheet->getCellCollection();
		
		        //load mPDF library
	    $this->load->library('m_pdf');

		$rowz = '';
		$check = array();
		$this->load->library('m_pdf');
		foreach ($worksheet->getRowIterator() as $row) {
			//$rowz = '';
			//$row = $worksheet->getCell($cl)->getRow();
		    $cellIterator = $row->getCellIterator();
		    $cellIterator->setIterateOnlyExistingCells(true);
		    // Iterate over the individual cells in the row
		   	$rowz = '';
		    $rowz .= '<br><br> next row<br><br> ';

		    foreach ($cellIterator as $cell) {
		        // Display information about the cell object
		        $rowz .=  'I am Cell '. $cell->getCoordinate().PHP_EOL;
		        $rowz .=  '<br>';
		        $rowz .=  'and my value is '.$cell->getValue().PHP_EOL;
		    	$rowz .=  '<br>';
		    }

				
	 		// 	$pdfFilePath = "xyz{$key}.pdf";
	 		// 	$rowz = mb_convert_encoding($rowz, 'UTF-8', 'UTF-8');
	 		// 	$this->m_pdf->pdf->WriteHTML($rowz);
				// $this->m_pdf->pdf->Output($pdfFilePath, "F"); 
				$check[] = $rowz;
				//echo $rowz;
		   
		}

		// echo '<pre>';
		//  print_r($check[1]);
		// echo '</pre>';

		 for($i=0; $i<count($check); $i++){
		 		$mPDF = new mPDF('c', 'A4-L');
	 			$pdfFilePath = "abczzz{$i}.pdf";
	 			$check[$i] = mb_convert_encoding($check[$i], 'UTF-8', 'UTF-8');
	 			$mPDF->WriteHTML($check[$i]);
				$mPDF->Output('test/'.$pdfFilePath, "F"); 			 	
		 }
		
			$this->load->library('zip');

			$path = APPPATH.'../test';

		$this->load->library('zip');
		$this->load->helper('file');
		$path = './test/';
		$files = get_filenames($path);
		//print_r($files);

		foreach($files as $f){
			$this->zip->read_file($path.$f, true);
		}
		$this->zip->download('Download_all_files');

		// $mPDF = new mPDF('c', 'A4-L');

		// $pdfFilePath = "nn.pdf";
		// $check[0] = mb_convert_encoding($check[0], 'UTF-8', 'UTF-8');
		// $mPDF->WriteHTML($check[0]);
		// $mPDF->Output($pdfFilePath, "F"); 
		// //die();
		// $this->load->library('m_pdf');
		
		// $mPDF2 = new mPDF('c', 'A4-L');

		// $pdfFilePath = "nn1.pdf";
		// $check[1] = mb_convert_encoding($check[1], 'UTF-8', 'UTF-8');
		// $mPDF2->WriteHTML($check[1]);
		// $mPDF2->Output($pdfFilePath, "F");

		// for ($check as $key => $value) {
	 // 			$pdfFilePath = "xyz{$key}.pdf";
	 // 			$rowz = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
	 // 			$this->m_pdf->pdf->WriteHTML($value);
		// 		$this->m_pdf->pdf->Output($pdfFilePath, "F"); 			
		// }

 			//Generating PDF

	        //this the the PDF filename that user will get to download
	        

	 		

	 		//$this->m_pdf->mpdf->allow_charset_conversion=true;
			//$this->m_pdf->mpdf->charset_in='UTF-8';
	       //generate the PDF from the given html
	 		// foreach($check as $key => $ch){
	 			
	 		// }
	       
	 
	        //download it.
	         

		    //break;


		//$data = [];
        //load the view and saved it into $html variable
       // $html=$this->load->view('welcome_message', $data, true);
 
  //       //this the the PDF filename that user will get to download
  //       $pdfFilePath = "output_pdf_name.pdf";
 
  //       //load mPDF library
  //       $this->load->library('m_pdf');

 	// 	$rowz = mb_convert_encoding($rowz, 'UTF-8', 'UTF-8');

 	// 	//$this->m_pdf->mpdf->allow_charset_conversion=true;
		// //$this->m_pdf->mpdf->charset_in='UTF-8';
  //      //generate the PDF from the given html
  //       $this->m_pdf->pdf->WriteHTML($rowz);
 
  //       //download it.
  //       $this->m_pdf->pdf->Output($pdfFilePath, "D");  
        
  //       echo 'PDF Generated';

		// die();		
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */