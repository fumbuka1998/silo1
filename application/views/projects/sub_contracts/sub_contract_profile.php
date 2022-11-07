<?php
$sub_contract_id=$sub_contract->{$sub_contract::DB_TABLE_PK};
?>
<div class="modal-dialog " style="width: 90%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"> Contract  details for &nbsp;<span style="text-transform: uppercase"><?= $sub_contract->contract_name ? wordwrap($sub_contract->contract_name,80,'<br/>') : '' ?> </span> </h4>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">
                        <div class="form-horizontal">
                            <div class="form-group col-md-6 col-sm-6">
                                <label  class="col-sm-4 control-label">Contract Name:</label>
                                <div class="form-control-static col-sm-8">
                                    <?= $sub_contract->contract_name ? wordwrap($sub_contract->contract_name,60,'<br/>') : 'N/A' ?>
                                </div>
                            </div>
                            <div class="form-group col-md-3 col-sm-6">
                                <label  class="col-sm-4 control-label">Contract Date :</label>
                                <div class="form-control-static col-sm-8">
                                    <?= $sub_contract->contract_date ? custom_standard_date($sub_contract->contract_date) : 'N/A' ?>
                                </div>
                            </div>
                            <div class="form-group col-md-3 col-sm-6">
                                <label  class="col-sm-4 control-label">Sub-contractor :</label>
                                <div class="form-control-static col-sm-8">
                                    <?= $sub_contract->stakeholder()->stakeholder_name ? $sub_contract->stakeholder()->stakeholder_name : 'N/A' ?>
                                </div>
                            </div>
                            <div class="form-group col-md-3 col-sm-6">
                                <label  class="col-sm-4 control-label">Description:</label>
                                <div class="form-control-static col-sm-8">
                                    <?= $sub_contract->description ? nl2br($sub_contract->description) : 'N/A' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#sub_contract_<?= $sub_contract_id ?>" data-toggle="tab">Sub Contract Works</a></li>
                        <li><a href="#certificate_tab_<?= $sub_contract_id ?>" data-toggle="tab">Certificate</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="active tab-pane" id="sub_contract_<?= $sub_contract_id ?>">
                            <?php
                                $this->load->view('projects/sub_contracts/sub_contract_items/sub_constract_item_form');
                            ?>
                        </div>
                        <div class="tab-pane" id="certificate_tab_<?= $sub_contract_id ?>">
                            <?php
                                $this->load->view('projects/sub_contracts/sub_contract_items/sub_contract_certificate_form');
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button"  class=" btn  btn-default btn-xs " data-dismiss="modal" aria-hidden="true" >
                <i class="fa fa-window-close"></i>&nbsp; close </button>
        </div>
    </div>
</div>
