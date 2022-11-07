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
                <button data-toggle="modal" data-target="#new_revision" class="btn btn-default btn-xs">
                    <i class="fa fa-plus-circle"></i>Extension/Revision
                </button>
                <div id="new_revision" class="modal fade revision_form" role="dialog">
                    <?php $this->load->view('projects/contract_reviews/revision_form'); ?>
                </div>
                <a  class="btn btn-default btn-xs" target="_blank" href="<?= base_url('projects/preview_project_revisions/'.$project->{$project::DB_TABLE_PK} ) ?>">
                    <i class="fa fa-file-pdf-o"></i> PDF
                </a>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table project_id="<?= $project->{$project::DB_TABLE_PK} ?>" class="table table-bordered table-hover" id="revision_list">
                    <thead>
                        <tr>
                            <th>Date</th><th>Revision Type</th><th>Description</th><th>Extension/No of Tasks Revised</th><th>Revision Cost</th><th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
