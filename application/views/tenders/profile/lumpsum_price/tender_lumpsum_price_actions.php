<?php

$tender_lumpsum_price_no = $lumpsum_price->{$lumpsum_price::DB_TABLE_PK};
if($lumpsum_price->created_by == $this->session->userdata('employee_id')) {
    ?>

    <span class="pull-right">
    <button data-toggle="modal" data-target="#edit_lumpsum_price<?= $tender_lumpsum_price_no ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_lumpsum_price<?= $tender_lumpsum_price_no ?>" class="modal fade" role="dialog">
        <?php
        $data['lumpsum_price'] = $lumpsum_price;
        $this->load->view('tenders/profile/lumpsum_price/edit_lumpsum_price_form'); ?>
    </div>

    <button class="btn btn-danger btn-xs delete_lumpsum_price" lumpsum_price_number="<?= $tender_lumpsum_price_no ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>
    <?php
}

