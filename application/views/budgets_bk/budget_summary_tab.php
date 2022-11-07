<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/17/2016
 * Time: 5:29 PM
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
                    </div>&nbsp; &nbsp;&nbsp; &nbsp;
                </form>
            </div>
            <div class="box-tools pull-right">

            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <?php
                $this->load->view('projects/costs/costs_summary_table',['cost_center' => $project]);
                ?>
            </div>
        </div>
    </div>
</div>
