<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 06/04/2018
 * Time: 10:23
 */


?>

<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#new_assets_handover" class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> New Handover
                </button>
                <div id="new_assets_handover" class="modal fade handover_form" role="dialog">
                    <?php $this->load->view('inventory/assets/handovers/assets_handover_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <table id="assets_handover_list" class="table table-bordered table-hover ">
                    <thead>
                        <tr>
                            <th>Handover No.</th><th>Handover Date</th><th>Handler</th><th>Assigner</th><th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
