<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 7/8/2018
 * Time: 5:59 PM
 */

?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#view_project_execution_summary_<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>" class="btn btn-default btn-xs">
        <i class="fa fa-eye"></i> Details
    </button>
    <div id="view_project_execution_summary_<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>" class="modal fade project_execution_summary" role="dialog">
        <?php    $this->load->view('projects/executions/project_execution_summary');   ?>
    </div>
    <a  class="btn btn-default btn-xs" target="_blank" href="<?= base_url('projects/preview_project_plan_execution/'.$project_plan->{$project_plan::DB_TABLE_PK}) ?>">
        <i class="fa fa-file-pdf-o"></i> PDF
    </a>

</span>