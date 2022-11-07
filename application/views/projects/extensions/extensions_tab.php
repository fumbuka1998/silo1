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
                <button data-toggle="modal" data-target="#new_time_extension" class="btn btn-default btn-xs">
                    New Time Extension
                </button>
                <div id="new_time_extension" class="modal fade" role="dialog">
                    <?php $this->load->view('projects/extensions/project_extension_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table project_id="<?= $project->{$project::DB_TABLE_PK} ?>" class="table table-bordered table-hover" id="project_extension_list">
                    <thead>
                        <tr>
                            <th>Extension Date</th><th>Extension </th><th>Cost</th><th>Reason</th><th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
