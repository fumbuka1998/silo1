<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 10/5/2017
 * Time: 8:58 AM
 */


$material_cost_center_assignment_id=$material_cost_center_assignments->{$material_cost_center_assignments::DB_TABLE_PK};
?>

<span class="pull-right">

    <a target="_blank" href="<?= base_url('inventory/preview_material_cost_center_assignment/'.$material_cost_center_assignment_id)?>"
       class="btn btn-xs btn-default"> <i class="fa fa-eye"></i> Preview</a>

</span>