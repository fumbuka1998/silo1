<?php
    $receipt_number = $receipt->{$receipt::DB_TABLE_PK};
?>
<span class="pull-right">
    <?php if($receipt->created_by == $this->session->userdata('employee_id')){ ?>
    <button data-toggle="modal" data-target="#edit_receipt_<?=$receipt_number ?>" class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_receipt_<?=$receipt_number ?>" class="modal fade receipt_form" role="dialog">
        <?php
            $this->load->view('finance/transactions/receipts/receipt_form');
        ?>
    </div>

    <button class="btn btn-danger btn-xs delete_receipt" receipt_id="<?=$receipt_number ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
    <?php } ?>

    <a target="_blank" href="<?= base_url('Finance/preview_receipt/'.$receipt_number)?>"
       class="btn btn-xs btn-default"> <i class="fa fa-eye"></i> Preview
    </a>
</span>



