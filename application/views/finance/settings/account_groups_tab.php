<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 3/6/2017
 * Time: 12:06 PM
 */
?>

<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#new_account_group" class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> New Account Group
                </button>
                <div id="new_account_group" class="modal fade" role="dialog">
                    <?php $this->load->view('finance/settings/account_group_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table id="account_groups_list" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>Group Name</th><th>Under</th><th>Description</th><th></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
