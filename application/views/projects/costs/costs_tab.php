<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/24/2016
 * Time: 9:19 AM
 */
?>
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#costs_summary" data-toggle="tab">Summary</a></li>
        <li><a href="#material_costs" data-toggle="tab">Material</a></li>
        <li><a href="#permanent_labour_costs" data-toggle="tab">Permanent Labour</a></li>
        <li><a href="#miscellaneous_tab" data-toggle="tab">Miscellaneous</a></li>
<!--        <li><a href="#equipment_costs_tab" data-toggle="tab">Equipment Costs</a></li>-->
    </ul>
    <div class="tab-content">
        <div class="active tab-pane" id="costs_summary">
            <?php $this->load->view('projects/costs/costs_summary'); ?>
        </div>
        <div class="tab-pane" id="material_costs">
            <?php $this->load->view('projects/costs/material/material_costs'); ?>
        </div>
        <div class="tab-pane" id="permanent_labour_costs">
            <?php $this->load->view('projects/costs/labour/permanent_labour_costs'); ?>
        </div>
        <div class="tab-pane" id="miscellaneous_tab">
            <?php $this->load->view('projects/costs/miscellaneous/miscellaneous_tab'); ?>
        </div>
        <div class="tab-pane" id="equipment_costs_tab">
            <?php //$this->load->view('projects/costs/equipments/equipment_costs_tab'); ?>
        </div>
    </div>
</div>
