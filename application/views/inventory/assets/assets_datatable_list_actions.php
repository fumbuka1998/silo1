<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 30/03/2018
 * Time: 16:43
 */

?>
<div class="dropdown">
    <button class="btn btn-xs btn-default dropdown-toggle" type="button" data-toggle="dropdown">Actions &nbsp; <span class="caret"></span></button>
    <ul class="dropdown-menu">
        <li>
            <a class="btn btn-default btn-xs" data-toggle="modal" data-target="#edit_asset_<?= $level.$level_id.$asset->{$asset::DB_TABLE_PK } ?>">
                <i class="fa fa-edit"></i>
                Edit
            </a>
            <a class="btn btn-default btn-xs" data-toggle="modal" data-target="#barcode<?= $level.$level_id.$asset->{$asset::DB_TABLE_PK } ?>">
                <i class="fa fa-barcode"></i>
                Barcode
            </a>
        </li>
    </ul>
    <div id="edit_asset_<?= $level.$level_id.$asset->{$asset::DB_TABLE_PK }?>" class="modal fade" tabindex="-1" role="dialog">
        <?php $this->load->view('inventory/assets/edit_asset_details_form'); ?>
    </div>
    <div id="barcode<?= $level.$level_id.$asset->{$asset::DB_TABLE_PK }?>" class="modal fade" tabindex="-1" role="dialog">
        <?php $this->load->view('inventory/assets/barcode_display'); ?>
    </div>
</div>
