<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 11/04/2018
 * Time: 10:35
 */
?>
<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#new_attachment" class="btn btn-default btn-xs">
                    New Attachment
                </button>
                <div id="new_attachment" class="modal fade" role="dialog">
                    <?php $this->load->view('projects/attachments/project_attachment_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table project_id="<?= $project->{$project::DB_TABLE_PK} ?>" class="table table-bordered table-hover" id="project_attachment_list">
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
