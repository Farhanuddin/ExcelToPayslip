<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//require_once APPPATH.'/third_party/vendor/autoload.php';


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

	//Download and Generate ExcelSheet PDF of all payslips
	//Change:: Generate all payslips of provided month

	public function generate($month, $year){

	 try{

			//Loading Excel library
			$this->load->library('Excel');

			//Loading excel file
			$file = "./uploads/".strtolower($month)."-".$year.".xls";
			//$file = "./uploads/abc.xls";
			$excelObj = PHPExcel_IOFactory::load($file);

			$worksheet = $excelObj->getSheet(0);
			$cell_collection = $worksheet->getCellCollection();
			
			//load mPDF library for Generating PDF
		    $this->load->library('m_pdf');

		    //Processing Excel sheet and Generating PDF.
		    //Multiple HTML views array
			$html_view_multiple = array();
			$emp_code = array();

			//Iterating through each row in excel file 
			foreach ($worksheet->getRowIterator() as $keyrow => $row) {

			    $cellIterator = $row->getCellIterator();
			    $cellIterator->setIterateOnlyExistingCells(true);

			    //Don't iterate if on first row
			    if($keyrow != 1){

			    	//reinitialize payslip array for next row
			    	$payslip_array = null;

				    foreach ($cellIterator as $key => $cell) {
						    	
				    	//Don't iterate cell on A Colulmn A because no need
				    		if($key != 'A'){

				    			if($key == 'B'){
				    				$emp_code[] = $cell->getValue();
				    			}

				    			//if I am on column C and D, it means they are integer so only fetch their value
					         	if( ( strpos($cell->getCoordinate(), 'C') !== false) || ( strpos($cell->getCoordinate(), 'D') !== false) ){

					         		//Insert value in Payslip data array
					         		/* Test Comment */
					         		//echo 'getValue:::'.$cell->getValue().'<br>';
					         		$payslip_array[] = $cell->getValue();

					         	}else{
					         		//Insert calculated value in Payslip data array
					         		$payslip_array[] = number_format($cell->getCalculatedValue(),0);
					         	}
					        }			   
				    }
			    }

				if(!empty($payslip_array)){
					
					//Assigning pagedata value to load payslip with corresponding data.
					$data['page_data'] = $payslip_array;
					$data['controller'] = $this;

					//Assigning month and year for this payslip
					$data['page_data']['month'] = $month;
					$data['page_data']['year'] = $year;

					//Assinging loaded payslip rendered views html views array
					$html_view_multiple[] = $this->load->view('pdf/payslip.php', $data, true);
				}		   
			}

				//Converting html views into pdf and saving to folder
				 for($i=0; $i<count($html_view_multiple); $i++){
				 		$mPDF = new mPDF('c', 'A4-L');
			 			$pdfFilePath = "payslip-{$emp_code[$i]}.pdf";
			 			$final_row[$i] = mb_convert_encoding($html_view_multiple[$i], 'UTF-8', 'UTF-8');
			 			$mPDF->WriteHTML($html_view_multiple[$i]);
						$mPDF->Output('test/'.$pdfFilePath, "F"); 			 	
				 }

				//Loading ZIP Library and downloading all files in Zip
				$this->load->library('zip');

				$path = APPPATH.'../test';

				$this->load->library('zip');
				$this->load->helper('file');

				$path = './test/';
				$files = get_filenames($path);

				//Reading and downloading all Files zip
				foreach($files as $f){
					$this->zip->read_file($path.$f, true);
					
					//deleting file
					unlink($path.$f);
				}

				//Downloading all files in zip
				$this->zip->download('Download_all_files');

	   }catch(Exception $e){
	   		echo 'Error. Operation couldnot be performed due to the following exception:'.$e->getMessage();
	   		die();
	   }
	}

	//Retriving single payslip in regards to provided month, year and emp_code
	public function get_single_payslip($month, $year, $emp_code){
		
	   try{

			//Loading Excel library
			$this->load->library('Excel');

			//Loading excel file of the month and year provided
			$file = "./uploads/".strtolower($month)."-".$year.".xls";
			
			//Loading excel sheet object
			$excelObj = PHPExcel_IOFactory::load($file);

			//Getting sheet from our excelFile object 
			$worksheet = $excelObj->getSheet(0);
			$cell_collection = $worksheet->getCellCollection();
			
			//load mPDF library for Generating PDF
		    $this->load->library('m_pdf');

		    //Processing Excel sheet and Generating PDF.

			$html_view = '';
			$payslip_array = array();
			$data['page_data'] = '';
			
			//Iterating through each row in excel file to get our Files

			foreach ($worksheet->getRowIterator() as $row) {
			    $cellIterator = $row->getCellIterator();
			    $cellIterator->setIterateOnlyExistingCells(true);

			    // Iterate over the individual cells in the row
			   	$happened = false;

			   	//Iterating through each cell in the row.
			    foreach ($cellIterator as $cell) {

			        // If you are in B Cell and employee code matches the given code.
			        if( ( strpos($cell->getCoordinate(), 'B') !== false && $cell->getValue()==$emp_code) || ($happened) )
			         {
			         	$happened = true;

				    	//if I am on column C and D, it means they are integer so only fetch their value
			         	if( ( strpos($cell->getCoordinate(), 'C') !== false) || ( strpos($cell->getCoordinate(), 'D') !== false) ){
			         		//Insert fetch value in payslip data array
			         		$payslip_array[] = $cell->getValue();

			         	}else{
			         		//insert calculated value in payslip data array
			         		$payslip_array[] = number_format($cell->getCalculatedValue(),0);
			         	}		         
				     }
			    }

			    //Generate HTML for PDF if Found
				if(!empty($payslip_array)){

					$data['page_data'] = $payslip_array;
					$data['page_data']['month'] = $month;
					$data['page_data']['year'] = $year;
					$data['controller'] = $this;

					//Loading single html view of payslip data
					$html_view = $this->load->view('pdf/payslip.php', $data, true);
				}

			}

				//Generating PDF of the HTML.
		 		$mPDF = new mPDF('c', 'A4-L');
	 			$pdfFilePath = "file{$i}.pdf";
	 			$html_view = mb_convert_encoding($html_view, 'UTF-8', 'UTF-8');
	 			
	 			$mPDF->SetTitle('payslip');	 			
	 			$mPDF->WriteHTML($html_view);
				$mPDF->Output('payslip.pdf', "D");
				

				// //Loading ZIP Library and downloading all files in Zip
				// $this->load->library('zip');

				// $path = APPPATH.'../test';

				// $this->load->helper('file');

				// $path = './test/';
				// $files = get_filenames($path);

				// foreach($files as $f){
				// 	$this->zip->read_file($path.$f, true);
					
				// 	//deleting file
				// 	unlink($path.$f);
				// }

				// //Downloading all files in zip
				// $this->zip->download('Download_all_files');

	   }catch(Exception $e){
	   		echo 'Error. Operation couldnot be performed due to the following exception:'.$e->getMessage();
	   		die();
	   }
	}

	public function convertNumberToWord($num = false)
	{
	    $num = str_replace(array(',', ' '), '' , trim($num));
	    if(! $num) {
	        return false;
	    }
	    $num = (int) $num;
	    $words = array();
	    $list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
	        'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
	    );
	    $list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
	    $list3 = array('', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
	        'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
	        'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
	    );
	    $num_length = strlen($num);
	    $levels = (int) (($num_length + 2) / 3);
	    $max_length = $levels * 3;
	    $num = substr('00' . $num, -$max_length);
	    $num_levels = str_split($num, 3);
	    for ($i = 0; $i < count($num_levels); $i++) {
	        $levels--;
	        $hundreds = (int) ($num_levels[$i] / 100);
	        $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ' ' : '');
	        $tens = (int) ($num_levels[$i] % 100);
	        $singles = '';
	        if ( $tens < 20 ) {
	            $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '' );
	        } else {
	            $tens = (int)($tens / 10);
	            $tens = ' ' . $list2[$tens] . ' ';
	            $singles = (int) ($num_levels[$i] % 10);
	            $singles = ' ' . $list1[$singles] . ' ';
	        }
	        $words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_levels[$i] ) ) ? ' ' . $list3[$levels] . ' ' : '' );
	    } //end for loop
	    $commas = count($words);
	    if ($commas > 1) {
	        $commas = $commas - 1;
	    }
	    return implode(' ', $words);
	}	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */