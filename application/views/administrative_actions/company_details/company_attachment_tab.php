<?php
/**
 * Created by PhpStorm.
 * User: josephie
 * Date: 6/9/2018
 * Time: 12:11 PM
 */
$company_details = get_company_details();
?>

<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#attachment_form" class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> New Attachment
                </button>
                <div id="attachment_form" class="modal fade" tabindex="-1" role="dialog">
                    <?php $this->load->view('administrative_actions/company_details/company_attachment'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table id="company_attachment_list" company_detail_id="<?= $company_details->{$company_details::DB_TABLE_PK} ?>" class="table table-bordered table-hover table-striped">
                    <thead>
                    <tr>
                        <th>DateTime Attached</th><th>Caption</th><th></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
