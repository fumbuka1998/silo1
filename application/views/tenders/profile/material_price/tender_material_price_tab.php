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
                        <button data-toggle="modal" data-target="#material_price_form_<?= $component_id ?>" class="btn btn-default btn-xs">
                            <i class="fa fa-plus"></i> New Material Price
                        </button>
                        <div id="material_price_form_<?= $component_id ?>" class="modal fade material_price_form" role="dialog">
                            <?php $this->load->view('tenders/profile/material_price/tender_material_price_form',['tender_component_id' => $component_id]); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12 table-responsive">
                        <table id="material_price_list" class="table table-bordered table-hover table-striped">
                            <thead>
                            <tr>
                                <th>Material Name</th><th>Quantity</th><th>Price</th><th>Description</th><th></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
