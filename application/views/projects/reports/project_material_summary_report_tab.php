<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 11/8/2016
 * Time: 6:00 PM
 */
?>

<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <form method="post" target="_blank" action="<?= base_url('projects/project_material_summary') ?>">
                    <input type="hidden" name="project_id" value="<?= $project->{$project::DB_TABLE_PK} ?>">
                    <input type="hidden" name="print" value="true">
                    <button class="btn btn-xs btn-default">
                        <i class="fa fa-print"></i> Print
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div id="material_summary_report_container" class="col-xs-12">

            </div>
        </div>
    </div>
</div>
