<?php
$company_details = get_company_details();
?>

<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#hse_dertificate_form" class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> New Certificate
                </button>
                <div id="hse_dertificate_form" class="modal fade" tabindex="-1" role="dialog">
                    <?php $this->load->view('hse/certificates/certificate_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table id="hse_certificates_list" class="table table-bordered table-hover table-striped" style="table-layout: fixed">
                    <thead>
                    <tr>
                        <th style="width: 20%"> Name </th><th> Type </th><th style="width: 70%"> Description </th><th style="width: 15%"></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
