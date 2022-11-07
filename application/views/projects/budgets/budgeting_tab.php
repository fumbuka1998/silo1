<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/17/2016
 * Time: 5:32 PM
 */
?>
<div class="row">
    <div class="col-xs-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#material_budgeting" data-toggle="tab">Material</a></li>
                <li><a href="#miscellaneous_budgeting_tab" data-toggle="tab">Miscellaneous</a></li>
                <li><a href="#labour_budgeting_tab" data-toggle="tab">Labour</a></li>
                <!--<li><a href="#tools_budgeting_tab" data-toggle="tab">Tools</a></li>-->
                <li><a href="#equipment_budgeting_tab" data-toggle="tab">Equipment</a></li>
                <li><a href="#sub_contract_budgeting_tab" data-toggle="tab">Sub-contact budget</a></li>
            </ul>
            <div class="tab-content">
                <div class="active tab-pane" id="material_budgeting">
                    <?php $this->load->view('projects/budgets/material/material_budgeting'); ?>
                </div>
                <div class="tab-pane" id="miscellaneous_budgeting_tab">
                    <?php $this->load->view('projects/budgets/miscellaneous/miscellaneous_budget_tab'); ?>
                </div><!--
                <div class="tab-pane" id="tools_budgeting_tab">
                    <?php /*$this->load->view('projects/budgets/tools/tools_budgeting'); */?>
                </div>-->
                <div class="tab-pane fade" id="equipment_budgeting_tab">
                    <?php $this->load->view('projects/budgets/equipment/equipment_budgeting'); ?>
                </div>
                <div class="tab-pane fade" id="labour_budgeting_tab">
                    <?php $this->load->view('projects/budgets/labour/labour_budgeting_tab'); ?>
                </div>

                <div class="tab-pane fade" id="sub_contract_budgeting_tab">
                    <?php $this->load->view('projects/budgets/sub_contracts/sub_contract_budgeting_tab'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
