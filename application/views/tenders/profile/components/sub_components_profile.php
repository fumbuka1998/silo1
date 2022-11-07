<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/27/2018
 * Time: 12:17 AM
 *  $sub_location_id = $sub_location->{$sub_location::DB_TABLE_PK};
 */
?>
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a class="#" href="#sub_component_list_tab" data-toggle="tab">Sub Component</a></li>
    </ul>
    <div class="tab-content">
        <div class="active tab-pane" id="sub_component_list_tab">
            <?php $this->load->view('inventory/material/sub_componets_list_tab'); ?>
        </div>
    </div>
</div>

