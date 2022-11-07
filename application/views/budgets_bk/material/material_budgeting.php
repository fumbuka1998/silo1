<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/17/2016
 * Time: 7:46 PM
 */
?>
<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="col-xs-12">
                <form class="form-inline">
                    <div class="form-group">
                        <label for="cost_center_id">Cost Center:  </label>
                        <?= form_dropdown('cost_center_selector',$cost_center_options,'',' class="form-control" ') ?>
                    </div>&nbsp; &nbsp;&nbsp; &nbsp;
                    <div class="form-group pull-right">
                        <label for="activities_excel">Excel File:  </label>
                        <input type="file" name="activities_excel" project_id="<?= $project->{$project::DB_TABLE_PK} ?>" class="form-control">
                        <button type="button" excel_type="budget" class="btn btn-default btn-sm upload_project_excel">Upload Excel</button>
                    </div>
                </form>
            </div>
            <div class="box-tools pull-right">
                <br/>
                <a target="_blank" class="btn btn-xs btn-default" href="<?= base_url('budgets/download_excel_material_budget_template/'.$project->{$project::DB_TABLE_PK}) ?>">Download Excel Template</a>
                <button data-toggle="modal" data-target="#new_material_budget" class="btn btn-xs btn-default">
                    <i class="fa fa-plus"></i> Add Material
                </button>
                <div id="new_material_budget" class="modal fade material_budget_form" role="dialog">
                    <?php
                    $data['cost_center_level'] = 'project';
                    $data['cost_center_id'] = $project->{$project::DB_TABLE_PK};
                    $this->load->view('budgets/material/material_budget_form',$data);
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table class="table table-bordered table-hover material_budget_items" project_id="<?= $project->{$project::DB_TABLE_PK} ?>">
                    <thead>
                        <tr>
                            <th>Material</th><th>Quantity</th><th>Unit</th><th>Rate</th><th>Amount</th><th>Description</th><th></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="4">Total</th><th id="total_budget_amount_display" style="text-align: right"></th><th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
