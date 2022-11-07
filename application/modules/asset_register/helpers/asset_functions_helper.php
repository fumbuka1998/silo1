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
                      
                        if($interval1->format("%R%a")>0){

                          $depreciation=($result['book_value']*$result['depreciation_rate'])/36500*$days;
                        }

                        $current_value=$result['book_value']-$depreciation;
                        $assets_depreciation[]=array(
                          
                          'asset_name'=>$result['asset_name'],
                          'asset_group_name'=>$result['asset_group_name'],
                          'book_value'=>$result['book_value'],
                          'registration_date'=>$result['registration_date'],
                          'depreciation_rate'=>$result['depreciation_rate'],
                          'days_passed'=>$days,
                          'depreciation'=>$depreciation,
                          'current_value'=>$current_value

                          );

                 } 


                 return $assets_depreciation;
           
      }
  