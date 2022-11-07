<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/23/2016
 * Time: 10:48 AM
 */
?>
<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#new_measurement_unit" class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> New Unit
                </button>
                <div id="new_measurement_unit" class="modal fade" tabindex="-1" role="dialog">
                    <?php $this->load->view('inventory/settings/measurement_unit_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table id="measurement_units_list" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Name</th><th>Symbol</th><th>Description</th><th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
