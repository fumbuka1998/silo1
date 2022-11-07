<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/26/2018
 * Time: 11:02 PM
 */
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="col-xs-12">
                    <div class="row pull-right">
                        <button data-toggle="modal" data-target="#lumpsum_price_form_<?= $component_id ?>" class="btn btn-default btn-xs">
                            <i class="fa fa-plus"></i> New Lumpsum Price
                        </button>
                        <div id="lumpsum_price_form_<?= $component_id ?>" class="modal fade lumpsum_price_form" role="dialog">
                            <?php $this->load->view('tenders/profile/lumpsum_price/tender_lumpsum_price_form',['tender_component_id' => $component_id]); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12 table-responsive">
                        <table class="table table-bordered table-hover table-striped lumpsum_price_list">
                            <thead>
                            <tr>
                                <th>Description</th><th>Amount</th><th></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
