<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 10/5/2017
 * Time: 8:58 AM
 */


$cost_center_assignment_id = $cost_center_assignment->{$cost_center_assignment::DB_TABLE_PK};
if($assignment_type == "material"){
  $url = 'inventory/preview_material_cost_center_assignment/';
} else {
  $url = 'assets/preview_asset_cost_center_assignment/';
}
?>

<span class="pull-right">

    <a target="_blank" href="<?= base_url($url.$cost_center_assignment_id)?>"
       class="btn btn-xs btn-default"> <i class="fa fa-eye"></i> Preview</a>

</span>