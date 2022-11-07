<?php
/**
 * COMMON USEFUL FUNCTIONS
 */
function check_login(){
    $CI =& get_instance();

    $CI->output->set_header('Last-Modified:'.gmdate('D, d M Y H:i:s').'GMT');
    $CI->output->set_header('Cache-Control: no-location, no-cache, must-revalidate');
    $CI->output->set_header('Cache-Control: post-check=0, pre-check=0',false);
    $CI->output->set_header('Pragma: no-cache');

    if($CI->session->userdata('employee_id') == NULL){
        system_log('Attempt Access');
        echo '<script>window.location = "'.base_url("app/login").'"</script>';
    }
}

function check_permission($permission,$redirect = false){
    $CI =& get_instance();
    $session_permissions = $CI->session->userdata('permissions');

    $allowed = $CI->session->userdata('employee_id') == 1 ? true : false;
    if(is_array($session_permissions)) {
        $allowed = $allowed || in_array($permission, $session_permissions) || in_array("Administrative Actions", $session_permissions);
    }

    if(!$allowed && $redirect){
        redirect(base_url()) ;
    }
    return  $allowed;
}

function check_privilege($privilege,$redirect = false){
    $CI =& get_instance();
    $permission_privileges = $CI->session->userdata('permission_privileges');
    $allowed = $CI->session->userdata('employee_id') == 1 ? true : false;
    if(is_array($permission_privileges)) {
        $allowed = $allowed || in_array($privilege, $permission_privileges) || check_permission('Administrative Actions');
    }
    if (!$allowed && $redirect) {
        redirect(base_url());
    }
    return $allowed;
}

function br2nl($string)
{
    return preg_replace("=<br */?>=i", "\n", $string);
}

function inspect_object($object){
    ini_set("memory_limit","4096M");
    //This function will expand and display arrays and objects
    echo "<pre>";
    print_r($object);
    echo "</pre>";
}

function add_leading_zeros($number,$length = 4){
    $difference = $length - strlen($number);
    $ret_val = '';
    for($i = 0; $i < $difference; $i++){
        $ret_val .= '0';
    }
    return $ret_val .= $number;
}

function array_sort($array, $sorting_attribute, $order=SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $sorting_attribute) {
                        $sortable_array[$k] = strtoupper($v2);
                    }
                }
            } else {
                $sortable_array[$k] = strtoupper($v);
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
                break;
            case SORT_DESC:
                arsort($sortable_array);
                break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

function multdimational_array_sort($array, $sorting_attribute, $order=SORT_ASC){
    for($i = 0; $i <= sizeof($array)-1; $i++){
        for($j = 0; $j < sizeof($array); $j++){

            switch ($order){
                case SORT_ASC:
                    if($array[$i]->$sorting_attribute < $array[$j]->$sorting_attribute){
                        $temp = $array[$i];
                        $array[$i] = $array[$j];
                        $array[$j] = $temp;
                    }
                    break;
                case SORT_DESC:
                    if($array[$i]->invoice_date > $array[$j]->invoice_date){
                        $temp = $array[$i];
                        $array[$i] = $array[$j];
                        $array[$j] = $temp;
                    }
                    break;
            }
        }
    }
    return $array;
}

function confidentiality_chain_position($level_name = null){
    $CI =& get_instance();
    $model = 'human_resource/employee_confidentiality_level';
    $CI->load->model($model);
    if(!is_null($level_name)){
        $positions = $CI->employee_confidentiality_level->get(0,0,['level_name'=>$level_name]);
        $position = !empty($positions) ? array_shift($positions) : false;
        if($position) {
            return $position->chain_position;
        } else {
            return false;
        }
    } else {
        return false;
    }

}

function system_log($action = 'Unknown',$description = '',$project_id = null){
    /**
     * This will log any crucial action done in the system
     */
    $CI =& get_instance();
    $CI->load->model('system_log');
    $log = new System_log();
    $log->department_id = $CI->session->userdata('department_id') ? $CI->session->userdata('department_id') : null;
    $log->employee_id = $CI->session->userdata('employee_id') ? $CI->session->userdata('employee_id') : null;
    $log->ip_address = $CI->input->ip_address();
    $log->user_agent = $CI->input->user_agent();
    $log->datetime_logged = datetime();
    $log->action = $action;
    $log->description = $description;
    $log->project_id = $project_id;
    $log->save();
}

function numbers_to_words($number){
    $CI =& get_instance();
    $CI->load->library('numbertowords');
    return $CI->numbertowords->convert_number($number);
}

function session_user_avatr(){
    $CI =& get_instance();
    $CI->load->model('employee');
    $session_user = new Employee();
    $session_user->load($CI->session->userdata('employee_id'));
    return $session_user->avatar_path();
}

function reArrayFiles(&$file_post) {

    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}

function hse_inspection_categories($id = null){
    $CI =& get_instance();
    $CI->load->model('category');
    if(!is_null($id)){
        $categories = $CI->category->get(0,0,['id'=>$id],'id ASC');
        $categories = array_shift($categories);
    } else {
        $categories = $CI->category->get(0,0,[],'id ASC');
    }
    return $categories;
}

function getCapitalLetters($str){

    if(preg_match_all('#([A-Z]+)#',$str,$matches))

        return implode('',$matches[1]);

    else

        return false;

}

function currency_exchange_rate($currency_id, $date = null){
    $CI =& get_instance();
    $CI->load->model('currency');
    $currency = new Currency();
    $currency->load($currency_id);
    return $currency->rate_to_native($date);
}

function get_company_details(){
    $CI =& get_instance();
    $CI->load->model('company_detail');
    $company_details = $CI->company_detail->get(1,0,[],' created_at DESC ');
    return array_shift($company_details);
}

function accountancy_number($number){
    return $number >= 0 ? number_format($number,2) : '('.number_format(-($number),2).')';
}
/**************************************
 *COMMON USEFUL DATE FUNCTIONS
 *************************************/

function custom_standard_date($date){
    return strftime('%d/%m/%Y', strtotime($date));
}

function number_of_days($from,$to){
    if($from && $to) {
        $start = new DateTime($from);
        $end = new DateTime($to);
// otherwise the  end date is excluded (bug?)
        $end->modify('+1 day');

        $interval = $end->diff($start);

// total days
        $days = $interval->days;

// create an iterateable period of date (P1D equates to 1 day)
        $period = new DatePeriod($start, new DateInterval('P1D'), $end);

// best stored as array, so you can add more than one
        $holidays = array();

        foreach ($period as $dt) {
            $curr = $dt->format('D');

            // substract if Saturday or Sunday
            if (0 > 1 && ($curr == 'Sat' || $curr == 'Sun')) {
                $days--;
            } // (optional) for the updated question
            elseif (in_array($dt->format('Y-m-d'), $holidays)) {
                $days--;
            }
        }

        return $days;
    } else {
        return 0;
    }
}

function datetime($timestamp = null){
    $timestamp = $timestamp != null ? $timestamp : time();
    return strftime('%Y-%m-%d %H:%M:%S', $timestamp);
}

function standard_datetime($datetime = null, $withtimestamp = false){
    $timestamp = $datetime != null ? strtotime($datetime) : time();
    if($withtimestamp){
        return strftime('%Y-%b-%d %H:%M', $timestamp);
    } else {
        return strftime('%Y-%b-%d', $timestamp);
    }
}

function remove_commas($number_formatted){
    return str_replace(',', '', $number_formatted);
}

function set_date($date_string){
    $splited_date = explode('-', $date_string);
    $monthNum  = $splited_date[1];
    $dateObj   = DateTime::createFromFormat('!m', $monthNum);
    $monthName = $dateObj->format('F'); //April

    return $splited_date[2].' '.substr($monthName, 0,3).' '.$splited_date[0];
}

function set_duration($time_string)
{
    date_default_timezone_set('Africa/Dar_es_Salaam');
    $time_created = strtotime($time_string);
    $current_time = time();
    $time_difference = $current_time - $time_created;

    $seconds = $time_difference;
    $minutes = round($seconds / 60);
    $hours = round($seconds / 3600);
    $days = round($seconds / 86400);    // 86400 = 24  60 60
    $weeks = round($seconds / 604800);  // 7*24*60*60
    $months = round($seconds / 2629440);  //((365 + 365 + 365 +365 + 366)/5/12)*24*60*60
    $years = round($seconds / 31553280);  //(365 + 365 + 365 + 365 + 366)/5  24  60 * 60 )

    if ($seconds <= 60) {
        return "Now";
    } else if ($minutes <= 60) {
        if ($minutes == 1) {
            return "1 minute ago";
        } else if ($minutes < 30) {
            return $minutes . ' ' . 'minutes ago';
        } else if ($minutes == 30) {
            return 'half an hour ago';
        } else if ($minutes > 30 && $minutes < 60) {
            return $minutes . ' ' . 'minutes ago';
        }
    } else if ($minutes == 60 || $hours < 24) {
        if ($hours == 1) {
            return '1 hour ago';
        } else if ($hours > 1 && $hours < 24) {
            return $hours . ' ' . 'hours ago';
        }

    } else if ($hours == 24) {
        return '1 day ago';
    } else if ($days <= 6) {
        if ($days >= 2) {
            return $days . ' ' . 'days ago';
        }
    }
    if ($days == 7) {
        return '1 week ago';
    } else if ($weeks <= 4.3) //4.3 ==52/12
    {
        if ($weeks == 1) {
            return '1 week ago';
        } else if ($weeks < 4) {
            return $weeks . ' ' . 'weeks ago';
        } else if ($weeks == 4) {
            return '1 month ago';
        }
    } else if ($months <= 12) {
        if ($months == 1) {
            return '1 month ago';
        } else {
            return $months . ' ' . 'months ago';
        }
    } else {
        if ($years == 1) {
            return '1 year ago';
        } else {
            return $years . ' ' . 'years ago';
        }
    }

}

/********************************************
 * SMS FUNCTIONS
 ********************************************/
function sanitize_recipient($recipient){
    $ptn = "/^0/";  // Regex
    $recipient = preg_replace($ptn, '255', $recipient);
    return str_replace(' ', '', $recipient);
}

function send_sms($recipient,$message,$from = 'EPM'){
    include("./vendor/autoload.php");
    $client = new infobip\api\client\SendSingleTextualSms(new infobip\api\configuration\BasicAuthConfiguration("bizytech", "Developers@123"));
    //$client = new infobip\api\client\SendSingleTextualSms(new infobip\api\configuration\BasicAuthConfiguration("prosms-phares", "ks27wJxL"));
    $requestBody = new infobip\api\model\sms\mt\send\textual\SMSTextualRequest();
    $requestBody->setFrom($from);
    $requestBody->setTo(sanitize_recipient($recipient));
    $requestBody->setText($message);
    return $response = $client->execute($requestBody);
}

/**
 * ****************************************************************
 * COMMON DROP DOWNS
 * *****************************************************************
 */

function sys_log_action_types(){
    $raw_action_types = [
        'Unknown',
        'Attempt Access',
        'Login',
        'Logout',
        'Employee Registration',
        'Employee Update',
        'Employee Contract Registration',
        'Employee Contract Update',
        'User Creation',
        'User Update',
        'Client Registration',
        'Client Update',
        'Client Delete',
        'Project Registration',
        'Project Update',
        'Project Manager Assignment',
        'Project Manager Assignment Update',
        'Project Manager Assignment Termination',
        'Activity Registration',
        'Activity Update',
        'Activity Delete',
        'Task Registration',
        'Task Update',
        'Task Delete',
        'Location Registration',
        'Location Update',
        'Location Delete',
        'Sub-Location Registration',
        'Sub-Location Update',
        'Sub-Location Delete',
        'Material Item Registration',
        'Material Item Update',
        'Material Item Delete',
        'Tool-Type Registration',
        'Tool-Type Update',
        'Tool-Type Delete',
        'Equipment-Type Registration',
        'Equipment-Type Update',
        'Equipment-Type Delete',
        'Vendor Registration',
        'Vendor Update',
        'Vendor Delete',
        'Equipment Registration',
        'Equipment Update',
        'Equipment Delete',
        'Tool Registration',
        'Tool Update',
        'Tool Delete',
        'Material Budget Item Addition',
        'Material Budget Item Update',
        'Material Budget Item Delete',
        'Miscellaneous Budget Item Addition',
        'Miscellaneous Budget Item Update',
        'Miscellaneous Budget Item Delete',
        'Tools Budget Item Addition',
        'Tools Budget Item Update',
        'Tools Budget Item Delete',
        'Department Registration',
        'Department Update',
        'Department Delete',
        'Job Position Registration',
        'Job Position Update',
        'Job Position Delete',
        'Material Item Category Addition',
        'Material Item Category Update',
        'Material Item Category Delete',
        'Measurement Unit Category Addition',
        'Measurement Unit Category Update',
        'Measurement Unit Category Delete',
        'Material Opening Stock Update',
        'External Material Transfer Submission',
        'External Material Transfer Update',
        'External Material Transfer Receive',
        'External Material Transfer Cancellation',
        'Generate Audit Report',
        'Print Audit Report',
        'Internal Material Transfer Submission',
        'Requisition Initiation',
        'Requisition Submission',
        'Requisition Update',
        'Requisition Delete',
        'Requisition Declination',
        'Requisition Attachment Upload',
        'Requisition Attachment Delete',
        'Purchase Order Submission',
        'Purchase Order Update',
        'Purchase Order Delete',
        'Purchase Order Close',
        'Purchase Order Receive',
        'Project Team Member Assignment',
        'Project Team Member Update',
        'Project Team Member Delete',
        'Task Progress Update',
        'Task Progress Delete',
        'Task Material Cost Addition',
        'Task Material Cost Update',
        'Project Material Cost Addition',
        'Project Material Cost Update',
        'Project Material Cost Delete',
        'Project Miscellaneous Cost Addition',
        'Project Miscellaneous Cost Update',
        'Project Miscellaneous Cost Delete',
        'Account Creation',
        'Account Update',
        'Account Delete',
        'Contra Entry',
        'Contra Update',
        'Contra Delete',
        'Payment Voucher Entry',
        'Payment Voucher Update',
        'Payment Voucher Delete'
    ];
    $action_types[''] = 'ALL';
    foreach($raw_action_types as $type){
        $action_types[$type] = $type;
    }
    return $action_types;
}

function employee_options($not_grouped = false)
{
    $CI =& get_instance();
    if($not_grouped){
        $CI->load->model('employee');
        $options[''] = '&nbsp;';
        $employees = $CI->employee->get(0, 0, '', 'first_name');
        foreach ($employees as $employee) {
            $options[$employee->{$employee::DB_TABLE_PK}] = $employee->full_name();
        }
    } else {
        $CI->load->model('department');
        $departments = $CI->department->get(0,0,'','department_name');
        $options[''] = ['' => '&nbsp;'];
        foreach($departments as $department){
            $employees = $department->employees();
            if(!empty($employees)){
                foreach($employees as $employee) {
                    $options[$department->department_name][$employee->{$employee::DB_TABLE_PK}] = $employee->full_name();
                }
            }
        }
    }
    return $options;
}

function projects_dropdown_options($not_grouped = false,$with_closed = false, $string = false)
{
    $CI =& get_instance();
    $CI->load->model('project');
    return $CI->project->project_dropdown_options($not_grouped,$with_closed,$string);
}

function outgoing_invoices_dropdown_options()
{
    $CI =& get_instance();
    $CI->load->model('outgoing_invoice');
    return $CI->outgoing_invoice->dropdown_options();
}

function bulk_payment_list(){
    $CI =& get_instance();
    $CI->load->model('payment_voucher');
    return $CI->payment_voucher->bulk_payment_list();
}

function approval_module_dropdown_options()
{
    $CI =& get_instance();
    $CI->load->model('approval_module');
    return $CI->approval_module->approval_module_options();
}

function locations_options($category = null){
    $CI =& get_instance();
    $CI->load->model('inventory_location');
    return $CI->inventory_location->dropdown_options($category);
}

function general_sub_location_options()
{
    $CI =& get_instance();

    $CI->load->model('inventory_location');
    $locations = $CI->inventory_location->get(0,0,'');
    $options[''] = ['' => '&nbsp;'];
    foreach($locations as $location){
        $sub_locations = $location->sub_locations();
        if(!empty($sub_locations)){
            foreach($sub_locations as $sub_location) {
                $options[$location->location_name][$sub_location->{$sub_location::DB_TABLE_PK}] = $location->location_name.' - '.$sub_location->sub_location_name;
            }
        }
    }

    return $options;
}

function equipment_types_options()
{
    $CI =& get_instance();
    $CI->load->model('equipment_type');
    $options[''] = '&nbsp;';
    $types = $CI->equipment_type->get(0,0,'','name');
    foreach($types as $type){
        $options[$type->{$type::DB_TABLE_PK}] = $type->name;
    }
    return $options;
}

function currency_dropdown_options(){
    $CI =& get_instance();
    $CI->load->model('currency');
    return $CI->currency->dropdown_options();
}

function material_item_dropdown_options($project_nature_id = null,$all_of_them = false){
    $CI =& get_instance();
    $CI->load->model('material_item');
    return $CI->material_item->dropdown_options($project_nature_id,$all_of_them);
}

function stakeholder_dropdown_options(){
    $CI =& get_instance();
    $CI->load->model('stakeholder');
    return $CI->stakeholder->dropdown_options();
}

function next_invoice_no(){
    $CI =& get_instance();
    $CI->load->model('outgoing_invoice');
    return $CI->outgoing_invoice->next_invoice_no();
}

function measurement_unit_dropdown_options(){
    $CI =& get_instance();
    $CI->load->model('measurement_unit');
    return $CI->measurement_unit->dropdown_options();
}

function asset_item_dropdown_options(){
    $CI =& get_instance();
    $CI->load->model('asset_item');
    return $CI->asset_item->dropdown_options();
}

function account_dropdown_options($group_natures = []){
    $CI =& get_instance();
    $CI->load->model('account');
    return $CI->account->dropdown_options($group_natures);
}

function account_group_dropdown_options($group_natures = [],$parent_id = null){
    $CI =& get_instance();
    $CI->load->model('account_group');
    return $CI->account_group->account_group_options($group_natures,$parent_id);
}

function stringfy_dropdown_options($dropdown_array = []){
    $option_string = '';

    foreach ($dropdown_array as $key => $value){
        if(is_array($value)) {
            $option_string .= '<optgroup label="' . $key . '">';
            foreach ($dropdown_array[$key] as $value => $option) {
                $option_string .= '<option value="' . $value . '">' . $option . '</option>';
            }
            $option_string .= '</optgroup>';
        } else {
            $option_string .= '<option value="'.$key.'">'.$value.'</option>';
        }
    }

    return $option_string;
}

function material_item_object($item_id){
    $CI =& get_instance();
    $CI->load->model('material_item');
    $material_item = new Material_item();
    $material_item->load($item_id);
    return $material_item;
}


/*****************************************************************************
 * DATATABLE
 ******************************************************************************/

function dataTable_post_params(){
    $CI =& get_instance();
    $params['keyword'] = $CI->input->post('search')['value'];
    $params['start'] = $CI->input->post('start');
    $params['limit'] = $CI->input->post('length');
    $params['order'] = $CI->input->post('order');
    return $params;
}

function dataTable_order_string($columns,$order,$default_column = null){
    //Order Settings
    $order_column = $order[0]['column'];
    $order_dir = $order[0]['dir'];
    $i= 0;
    foreach ($columns as $column){
        if($order_column == $i){
            $order_column = $column;
            break;
        }
        $i++;
    }

    if(!in_array($order_column,$columns)){
        $order_column = !is_null($default_column) ? $default_column : $columns[0];
    }
    return $order_column.' '.$order_dir;
}

function asset_group_dropdown_options(){
    $CI = & get_instance();
    $CI->load->model('Asset_group');
    $groups = $CI->Asset_group->get();
    $options[''] = '&nbsp;';
    foreach ($groups as $group){
        $options[$group->{$group::DB_TABLE_PK}] = $group->group_name;
    }

    return $options;
 }
