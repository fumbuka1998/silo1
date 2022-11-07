<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 2/3/2017
 * Time: 1:29 PM
 */
?>
<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">

                <button data-toggle="modal" data-target="#update_exchange_rates" class="btn btn-default btn-xs">
                    <i class="fa fa-refresh"></i> Update Rates
                </button>
                <div id="update_exchange_rates" class="modal fade" tabindex="-1" role="dialog">
                    <?php $this->load->view('finance/settings/update_exchange_rates_form'); ?>
                </div>

                <button data-toggle="modal" data-target="#new_currency" class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> New Currency
                </button>
                <div id="new_currency" class="modal fade" role="dialog">
                    <?php $this->load->view('finance/settings/currency_form');?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table id="currencies_table" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>Currency Name</th><th>Symbol</th><th>Rate</th><th></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
