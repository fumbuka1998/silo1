<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/26/2018
 * Time: 11:47 PM
 */

?>
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tender_sub_components_<?= $component_id?>" data-toggle="tab">Sub Components</a></li>
        <li><a href="#tender_lumpsum_price_<?= $component_id?>" data-toggle="tab">Lumpsum Price</a></li>
        <li><a href="#tender_material_price_<?= $component_id?>" data-toggle="tab">Material Price</a></li>
    </ul>
    <div class="tab-content">
        <div class="active tab-pane" id="tender_sub_components_<?= $component_id?>">
            <?php $this->load->view('tenders/profile/components/tender_sub_components_tab',['component_id'=>$component_id]); ?>
        </div>
        <div class=" tab-pane" id="tender_material_price_<?= $component_id?>">
            <?php $this->load->view('tenders/profile/material_price/tender_material_price_tab',['component_id'=>$component_id]); ?>
        </div>
        <div class=" tab-pane" id="tender_lumpsum_price_<?= $component_id?>">
            <?php $this->load->view('tenders/profile/lumpsum_price/tender_lumpsum_price_tab',['component_id'=>$component_id]); ?>
        </div>
    </div>
</div>














