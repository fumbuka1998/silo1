<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/30/2018
 * Time: 11:25 AM
 */
$tender_material_price_no = $material_price->{$material_price::DB_TABLE_PK};
?>

<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_material_price<?= $tender_material_price_no  ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_material_price<?= $tender_material_price_no  ?>" class="modal fade" role="dialog">
        <?php
        $data['material_price'] = $material_price;
        $this->load->view('tenders/profile/material_price/edit_material_price_form');?>
    </div>

    <button class="btn btn-danger btn-xs delete_material_price" material_price_number = "<?= $tender_material_price_no  ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>

