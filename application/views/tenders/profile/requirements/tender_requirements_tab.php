<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/26/2018
 * Time: 10:57 PM
 */
?>

<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#requirement_form" class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> New Requirements
                </button>
                <div id="requirement_form" class="modal fade" tabindex="-1" role="dialog">
                    <?php $this->load->view('tenders/profile/requirements/tender_requirement_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table id="requirements_list" class="table table-bordered table-hover table-striped">
                    <thead>
                    <tr>
                        <th>Requirement No.</th><th>Requirement Name</th><th>Tender Name</th><th>Description</th><th></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

