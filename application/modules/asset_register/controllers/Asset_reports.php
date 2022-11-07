<?php

class Asset_reports extends CI_Controller{
    
    public function __construct() {
        parent::__construct();
        check_login();
        $this->load->helper('asset_functions_helper');
     }


    public function asset_depreciation_report(){

            $this->load->model('asset');


                    $issue_date=date('Y-m-d');
                    $sub_location_id="";
                    $asset_group_id="";
                    
                   $data['issue_date']=$issue_date;
                   $data['asset_group_id']=$asset_group_id;
                   $data['sub_location_id']=$sub_location_id;


            $this->load->model('Asset_group');
            $this->load->model('Inventory_location');
            $data['location_options']=general_sub_location_options();
            $data['asset_group_options']= $this->Asset_group->asset_group_options();//dropdown_options();
            $data['title'] = 'Asset List';
            $data['asset_list']=assets_depreciation_report($issue_date,$asset_group_id,$sub_location_id);
            $this->load->view('asset_reports/asset_depreciation_report',$data);

    }

     public function asset_depreciation_report_filter(){

                   $this->load->model('asset');
                    $issue_date=$this->input->post('issue_date');
                    $asset_group_id=$this->input->post('asset_group_id');
                    $sub_location_id=$this->input->post('sub_location_id');
        
                    $data['issue_date']=$issue_date;
                    $data['asset_group_id']=$asset_group_id;
                    $data['sub_location_id']=$sub_location_id;


            $this->load->model('Asset_group');
            $this->load->model('Inventory_location');
            $data['location_options']=general_sub_location_options();
            $data['asset_group_options']= $this->Asset_group->asset_group_options();
            $data['title'] = 'Asset List';
            $data['asset_list']=assets_depreciation_report($issue_date,$asset_group_id,$sub_location_id);
            $this->load->view('asset_reports/depreciation_list',$data);

    }

     public function asset_deprecition_report_print_preview($issue_date,$asset_group_id='',$sub_location_id=''){

                  $data['issue_date']=$issue_date;
                  $data['asset_group_id']=$asset_group_id;
                  $data['sub_location_id']=$sub_location_id;
                  $data['asset_list']=assets_depreciation_report($issue_date,$asset_group_id,$sub_location_id);
                  $data['print']='print';

                  $html = $this->load->view('asset_reports/asset_deprecition_report_print_preview',$data,true);

                 //this the PDF filename that user will get to download

                 //load mPDF library
                 $this->load->library('m_pdf');
                 //actually, you can pass mPDF parameter on this load() function
                 $pdf = $this->m_pdf->load();
                 $pdf->AddPage('L', // L - landscape, P - portrait
                    '', '', '', '',
                    15, // margin_left
                    15, // margin right
                    15, // margin top
                    15, // margin bottom
                    9, // margin header
                    6); // margin footer
                 $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>'. $this->session->userdata('employee_name').'</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>'.strftime('%d/%m/%Y %H:%M:%S').'</span>
                        </div>
                    </div>';
                $pdf->SetFooter($footercontents);
                $pdf->WriteHTML($html);
                //$this->mpdf->Output($file_name, 'D'); // download force

                $pdf->Output('asset_deprecition_report.pdf', 'I'); // view in the explorer
          
    }


     public function asset_schedule_report(){

             $this->load->model('asset');


                  $from=date('Y-m-d');
                  $to=date('Y-m-d');
                  $sub_location_id="";
                  $asset_group_id="";
                    
                  $data['asset_group_id']=$asset_group_id;
                  $data['sub_location_id']=$sub_location_id;
                  $data['from']=$from;
                  $data['to']=$to;


            $this->load->model('Asset_group');
            $this->load->model('Inventory_location');
            $data['location_options']=general_sub_location_options();
            $data['asset_group_options']= $this->Asset_group->asset_group_options();
            $data['title'] = 'Asset List';
            $data['asset_list']=asset_chedule_report($from,$to,$asset_group_id,$sub_location_id);
            $this->load->view('asset_reports/asset_schedule_report',$data);

    }

    public function asset_schedule_report_filter(){

             $this->load->model('asset');


                   $from=$this->input->post('from_date');
                   $to=$this->input->post('to_date');
                   $asset_group_id=$this->input->post('asset_group_id');
                   $sub_location_id=$this->input->post('sub_location_id');
                    
               
                  $data['asset_group_id']=$asset_group_id;
                  $data['sub_location_id']=$sub_location_id;
                  $data['from']=$from;
                  $data['to']=$to;


            $this->load->model('Asset_group');
            $this->load->model('Inventory_location');
            $data['location_options']=general_sub_location_options();
            $data['asset_group_options']= $this->Asset_group->asset_group_options();
            $data['title'] = 'Asset List';
            $data['asset_list']=asset_chedule_report($from,$to,$asset_group_id,$sub_location_id);
            $this->load->view('asset_reports/schedule_list',$data);

    }


    public function asset_schedule_report_print_preview($from,$to,$asset_group_id='',$sub_location_id=''){

                  $data['asset_group_id']=$asset_group_id;
                  $data['sub_location_id']=$sub_location_id;
                  $data['asset_list']=asset_chedule_report($from,$to,$asset_group_id,$sub_location_id);
                  $data['print']='print';

                 $html = $this->load->view('asset_reports/asset_schedule_report_print_preview',$data,true);

                 //this the PDF filename that user will get to download

                 //load mPDF library
                 $this->load->library('m_pdf');
                 //actually, you can pass mPDF parameter on this load() function
                 $pdf = $this->m_pdf->load();
                 $pdf->AddPage('L', // L - landscape, P - portrait
                    '', '', '', '',
                    15, // margin_left
                    15, // margin right
                    15, // margin top
                    15, // margin bottom
                    9, // margin header
                    6); // margin footer
                 $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>'. $this->session->userdata('employee_name').'</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>'.strftime('%d/%m/%Y %H:%M:%S').'</span>
                        </div>
                    </div>';
                $pdf->SetFooter($footercontents);
                $pdf->WriteHTML($html);
                //$this->mpdf->Output($file_name, 'D'); // download force

                $pdf->Output('asset schedule report.pdf', 'I'); // view in the explorer
          
    }
    

}

