<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 23/10/2018
 * Time: 10:54
 */
class Support extends CI_Controller
{

    private $api_key;

    public function __construct()
    {
        parent::__construct();
        check_login();
        $this->api_key = $this->config->item('crm_api_key');
        $this->crm_url = $this->config->item('crm_url');
    }

    public function tickets()
    {
        if($this->input->post('length') != ''){
            $this->load->library('MY_Curl');
            $curl = new MY_Curl();
            $curl->setPost(
                array(
                    'api_key' => $this->api_key,
                    'offset' => $this->input->post('start'),
                    'limit' => $this->input->post('length'),
                    'keyword' => $this->input->post('keyword')
                )
            );

            $curl->setUserAgent($this->input->user_agent());
            $curl->createCurl($this->crm_url.'API/tickets');
            $response = json_decode($curl->__tostring());

            $rows = array();

            $tickets = $response->data;

            foreach ($tickets as $ticket){
                $data = array('ticket'=>$ticket);
				$action = '';
				if($ticket->status == "Active"){
					$status = "<span style='color: limegreen'><strong>Active</strong></span>";
					if($this->session->userdata('employee_name') == $ticket->opened_by ){
						$action =  "<a class='btn btn-success btn-mini' onclick='rating($ticket->ticket_id)'>
						<i class='fa fa-check' style='color: white'></a>";
					}
				}else{
					$status = $ticket->status;
				}
                $rows[] = array(
                    add_leading_zeros($ticket->ticket_id),
                    anchor(base_url('support/conversations/'.$ticket->ticket_id),$ticket->subject),
                    $ticket->last_updated,
                    $ticket->date_opened,
                    $ticket->opened_by,
					$status,
//                    $this->load->view('support/ticket_status',$data,true),
					$action
                );
            }

            $json = array(
                'data' => $rows,
                'recordsFiltered' => $response->records_filtered,
                'recordsTotal' => $response->records_total
            );

            echo json_encode($json);

        } else {
            $this->load->model('employee');
            $data = array('email_options' => $this->employee->email_options());
            $this->load->view('support/tickets',$data);
        }
    }

    public function conversations($ticket_id)
    {


        $this->load->library('MY_Curl');
        $curl = new MY_Curl();
        $curl->setPost(
            array(
                'api_key' => $this->api_key,
                'ticket_id' => $ticket_id
            )
        );
        $curl->setUserAgent($this->input->user_agent());
        $curl->createCurl($this->crm_url.'API/conversations');
        $response = json_decode($curl->__tostring());

        $results = $response->data;

        $subject = '';
        $status = '';

        foreach ($results as $result){
            $subject = $result->subject;
            $status = $result->status;
        }
        $data = array(
            'ticket_id' => $ticket_id,
            'conversations' => $response->data,
            'subject' => $subject,
            'status' => $status
        );
        $this->load->view('support/conversations', $data);

    }

    public function save_tickets()
    {

        $this->load->library('MY_Curl');
        $curl = new MY_Curl();

        $ticket_id = $this->input->post('ticket_id');
        $ticket_message =  $this->input->post('message');
        $subject = $this->input->post('subject');
        $user_phone = '';
        $user_mail = '';
        if ($this->session->userdata('employee_phone') != NULL || $this->session->userdata('employee_email') != NULL ) {
            $user_phone = $this->session->userdata('employee_phone');
            $user_mail = $this->session->userdata('employee_email');
        }

        $curl->setPost(
            array(
                'api_key' => $this->api_key,
                'ticket_details' => json_encode(array(
                    'ticket_id' => $ticket_id,
                    'subject' => $subject,
                )),
                'user_details' => json_encode(array(
                    'full_name' => $this->session->userdata('employee_name'),
                    'phone' => $user_phone,
                    'email' => $user_mail,
                    'message' => $ticket_message,
                    'sender' => 'epm'
                )),
                'carbon_copies' => json_encode(array(
                    'emails' => $this->input->post('carbon_copy') ? $this->input->post('carbon_copy') : NULL
                ))
            )
        );

        $curl->setUserAgent($this->input->user_agent());
        $curl->createCurl($this->crm_url.'support_tickets/save_tickets');
        echo $curl->__tostring();

        if (!empty($_FILES)) {
            $count = 0;
            foreach ($_FILES as $FILE){

                $this->upload_files($FILE['tmp_name'], $FILE['name'], $FILE['type'], $ticket_message );

                $count++;
            }
        }


    }

    public function upload_files($temp_name, $file_name, $file_type, $ticket_message)
    {

        $data = array(
            'user_details' => array(
                'full_name' => $this->session->userdata('employee_name'),
                'message' => $ticket_message,
                'sender' => 'epm'
            )
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_URL, $this->crm_url.'upload/do_upload/');
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('User-Agent: Opera/9.80 (Windows NT 6.2; Win64; x64) Presto/2.12.388 Version/12.15',
                'Referer: '.base_url('app/curl_test'),
                'Content-Type: multipart/form-data')
        );

        curl_setopt($ch, CURLOPT_POST, true);
        $postData = array(
            'file[0]' => new CURLFile($temp_name, $file_type, $file_name),
            'data' => json_encode($data)
        );


        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);




        $response = curl_exec($ch);
    }

    public function close_tickets(){

		$ticket_id = $this->input->post('ticket_id');
		$rates =  $this->input->post('rates');

		$this->load->library('MY_Curl');
		$curl = new MY_Curl();

		$curl->setPost(
			array(
				'ticket_id' => $ticket_id,
				'rates' => $rates
			)
		);

		$curl->setUserAgent($this->input->user_agent());
		$curl->createCurl($this->crm_url.'Support/close_ticket');
		echo $curl->__tostring();

	}



}
