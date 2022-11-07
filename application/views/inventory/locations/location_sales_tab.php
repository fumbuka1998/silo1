<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/9/2018
 * Time: 8:02 AM
 */

?>
<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#location_sales_<?= $location->{$location::DB_TABLE_PK} ?>" class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> New Sales
                </button>
                <div id="location_sales_<?= $location->{$location::DB_TABLE_PK} ?>" class="modal fade location_sales_form" role="dialog">
                    <?php $this->load->view('inventory/sales/stock_sales_form');?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <table id="sales_table" location_id="<?= $location->{$location::DB_TABLE_PK} ?>"  class="table table-bordered table-hover ">
                    <thead>
                    <tr>
                        <th>Sales No.</th>
                        <th>Sale Date</th>
                        <th>Client</th>
                        <th>Reference</th>
                        <th style="width: 180px"></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
