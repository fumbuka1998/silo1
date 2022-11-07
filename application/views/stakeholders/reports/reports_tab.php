<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 19/02/2018
 * Time: 10:31
 */
$month_string = explode('-',date('Y-m-d'))[1] - 1 > 0 ? explode('-',date('Y-m-d'))[1] - 1 : 12 ;
$privious_month = explode('-', date('Y-m-d'))[0].'-'.add_leading_zeros($month_string,2).'-'.explode('-',date('Y-m-d'))[2];
$report_type_options = [
    'orders_statement' => 'Orders Statement',
    'supplied_items_report' => 'Supplied Items'
];
?>
<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools">
                <form method="post" target="_blank" action="<?= base_url('procurements/stakeholder_reports') ?>">
                    <div class="form-group col-md-3">
                        <label for="" class="control-label">Report Type</label>
                        <?= form_dropdown('report_type',$report_type_options,'',' class="form-control stakeholder_report_type searchable"') ?>
                        <input name="stakeholder_id" type="hidden" value="<?= $stakeholder->{$stakeholder::DB_TABLE_PK} ?>">
                        <input name="print" type="hidden" value="true">
                    </div>
                    <div class="form-group col-md-2 currency_fg">
                        <label for="currency_id" class="control-label">Currency</label>
                        <?= form_dropdown('currency_id', currency_dropdown_options(), '', ' class="form-control searchable" ') ?>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="" class="control-label">From</label>
                        <input class="form-control datepicker" name="from" value="<?= $privious_month ?>">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="" class="control-label">To</label>
                        <input class="form-control datepicker" name="to" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div style="display: none;" class="form-group col-md-2 report_category_fg">
                        <label for="" class="control-label">Bulk/Itemwise</label>
                        <?= form_dropdown('report_category',[
                            'itemwise'=>'Itemwise',
                            'in_bulk'=>'Bulk'
                        ],'',' class="form-control searchable"') ?>
                    </div>
                    <div class="form-group col-md-2">
                        <br/>
                        <button type="button" id="generate_stakeholder_report" class="btn btn-default btn-xs">
                            Generate
                        </button>
                        <button name="pdf" value="true" class="btn btn-default btn-xs">
                            <i class="fa fa-file-pdf-o"></i> PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <div id="stakeholder_report_container" class="col-xs-12 table-responsive">

                </div>
            </div>
        </div>
    </div>
</div>
