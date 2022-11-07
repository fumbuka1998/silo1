<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/26/2018
 * Time: 10:55 PM
 */
?>

<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#attachment_form" class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> New Attachment
                </button>
                <div id="attachment_form" class="modal fade" tabindex="-1" role="dialog">
                    <?php $this->load->view('tenders/profile/attachments/tender_attachment_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table id="tender_attachment_list" tender_id="<?= $tender->{$tender::DB_TABLE_PK} ?>" class="table table-bordered table-hover table-striped">
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
