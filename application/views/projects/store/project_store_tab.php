<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/7/2016
 * Time: 10:32 AM
 */


    if(!$location){
    ?>
        <div class="alert alert-info">This project is not bounded with any store in the system</div>
    <?php
    } else {
        $data['location'] = $location;
        $this->load->view('inventory/locations/location_workspace',$data);
    }
?>