<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/15/2016
 * Time: 5:47 PM
 */
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="col-xs-12">
                    <div class="box-tools pull-right">
                        <button data-toggle="modal" data-target="#new_transfer_form" class="btn btn-default btn-xs">
                            <i class="fa fa-plus"></i> New
                        </button>
                        <div id="new_transfer_form" class="modal fade" role="dialog">
                            <?php $this->load->view('asset_transfer_form');?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12 table-responsive">
                        <table id="asset_transfer_list" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Asset</th>
                                <th>Department</th>
                                <th>Sub Location</th>
                                <th>Under</th>
                                <th>Transfer Date</th>
                                <th>Transfered by</th>
                                <th>Transfer Date</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
