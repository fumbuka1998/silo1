<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

     function assets_depreciation_report($issue_date,$asset_group_id,$location_id){

                   $CI = & get_instance();

                   $CI->load->model('asset');

                    $asset_list=$CI->asset->asset_list_filter($asset_group_id,$location_id);
                  
                    $assets_depreciation=array();

                    $issue_date=date_create($issue_date);

                    foreach($asset_list as $result){

                        $registration_date=date_create($result['registration_date']);
                        
                        $interval1=date_diff($registration_date,$issue_date);
                      
                        $days= $interval1->format("%a");

                        $then = new DateTime($result['registration_date']);
                         
                        $now = new DateTime();
                         
                        $sinceThen = $then->diff($now);

                        $depreciation=0;
                        $depreciation_rate=10;
                      
                        if($interval1->format("%R%a")>0){

                          $depreciation=($result['book_value']*$depreciation_rate)/36500*$days;
                        }

                        $current_value=$result['book_value']-$depreciation;

                        $assets_depreciation[]=array(
                          
                          'asset_name'=>$result['asset_name'],
                          'group_name'=>$result['group_name'],
                          'book_value'=>$result['book_value'],
                          'registration_date'=>$result['registration_date'],
                          'depreciation_rate'=>$depreciation_rate,
                          'days_passed'=>$days,
                          'depreciation'=>$depreciation,
                          'current_value'=>$current_value

                          );

                 } 


                 return $assets_depreciation;
           
      }


    function asset_chedule_report($from,$to,$asset_group_id,$location_id){

                    $CI = & get_instance();

                    $CI->load->model('asset');

                    $asset_list=$CI->asset->asset_list_filter($asset_group_id,$location_id);

                    $prevdate = strtotime ( '-1 day' , strtotime ($to));       
                    $to = date ( 'Y-m-d' , $prevdate );

                    $total_book_value=0;
                    $total_depreciation=0;
                    $total_current_value=0;
                    $total_balance=0;
                    $net_book_value=0;

                    $date_from=date_create($from);
                    $date_to=date_create($to);
                   
                    $asset_report=array();

                    foreach($asset_list as $result){

                      $registration_date=date_create($result['registration_date']);
                      
                      $interval1=date_diff($registration_date,$date_from);
                      $interval2=date_diff($date_from,$date_to);

                      $days= $interval1->format("%a");

                      $interval_days= $interval2->format("%a");

                      $then = new DateTime($result['registration_date']);
                       
                      $now = new DateTime();
                       
                      $sinceThen = $then->diff($now);
                     
                      $total_book_value=$total_book_value+$result['book_value'];

                      $accumulated_dep=0;
                      $depreciation_rate=10;
                      
                      if($interval1->format("%R%a")>0){

                          $accumulated_dep=($result['book_value']*$depreciation_rate)/36500*$days;

                       }

                        $balance=$result['book_value']-$accumulated_dep;

                        $depreciation=0;
                      
                        if($interval2->format("%R%a")>0){

                            $depreciation=($balance*$depreciation_rate)/36500*$interval_days;

                        } 

                           $total_depreciation=$total_depreciation+$depreciation;

                           $net_book_value=$net_book_value+($balance-$depreciation);


                         $asset_report[]=array(
                          
                          'asset_name'=>$result['asset_name'],
                          'group_name'=>$result['group_name'],
                          'book_value'=>$result['book_value'],
                          'registration_date'=>$result['registration_date'],
                          'depreciation_rate'=>$depreciation_rate,
                          'days_passed'=>$days,
                          'accumulated_dep'=>$accumulated_dep,
                          'balance'=>$balance,
                          'depreciation'=>$depreciation,
                          'net_book_value'=>$balance-$depreciation

                          );


                 } 


               return $asset_report;
           
      }
  