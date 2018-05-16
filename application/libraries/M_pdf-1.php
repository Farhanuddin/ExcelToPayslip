<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
 
 //require_once APPPATH."third_party/vendor/mpdf/mpdf/src/mpdf.php";
require_once APPPATH.'/third_party/vendor/autoload.php';

class M_pdf {
 
    public $param;
    public $pdf;
 
    public function __construct($param = '"en-GB-x","A4","","",10,10,10,10,6,3')
    {
        return new \Mpdf\Mpdf();
    }
}