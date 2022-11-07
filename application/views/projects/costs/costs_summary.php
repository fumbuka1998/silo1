<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 4/20/2017
 * Time: 11:31 PM
 */

?>

<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="col-xs-12">
                <form class="form-inline">
                    <div class="form-group">
                        <label for="cost_center_id">Cost Center:  </label>
                        <?php
                        echo form_dropdown('cost_center_selector',$summary_cost_center_options,'project_overall',' class="form-control" ')
                        ?>
                        <input type="hidden" name="project_id" value="<?= $project->{$project::DB_TABLE_PK} ?>">
                    </div>&nbsp; &nbsp;&nbsp; &nbsp;
                </form>
            </div>
            <div class="box-tools pull-right">

            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div id="costs_summary_table_container" class="col-xs-12">
                <?php
                    $this->load->view('projects/costs/costs_summary_table',['cost_center' => $project,'general_only' => false]);
                ?>
            </div>
        </div>
    </div>
</div>
