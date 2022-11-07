<?php
switch ($type) {
    case 'sales':
        $has_receipt_or_pv = $invoice->has_receipt();
        $reffering_to = 'O-INV';
        break;
    case 'purchases':
        $has_receipt_or_pv = $invoice->payment_voucher();
        $reffering_to = 'P-INV';
        break;
}
if (check_privilege('Finance Actions')) {
?>
    <span class="pull-left">
        <a title="View Invoice" class="btn btn-default btn-xs" target="_blank" href="<?= base_url('finance/preview_invoice/' . $invoice->{$invoice::DB_TABLE_PK} . '/' . $type) ?>">
            <i class="fa fa-eye"></i>
        </a>
        <button title="Attachments" class="btn btn-xs" data-toggle="modal" data-target="#attachment_<?= $invoice->{$invoice::DB_TABLE_PK} ?>">
            <i class="fa fa-paperclip"></i>
        </button>
        <div id="attachment_<?= $invoice->{$invoice::DB_TABLE_PK} ?>" class="modal fade procurement_attachment_modal" role="dialog">
            <?php $this->load->view('attachments/purchase_order_related/index', ['reffering_to' => $reffering_to]); ?>
        </div>
        <?php if ($this->session->userdata('employee_id') == $invoice->created_by && !$has_receipt_or_pv) { ?>
            <button title="Edit" data-toggle="modal" data-target="#edit_invoice_<?= $invoice->{$invoice::DB_TABLE_PK} ?>" class="btn btn-xs">
                <i class="fa fa-edit"></i>
            </button>
            <div id="edit_invoice_<?= $invoice->{$invoice::DB_TABLE_PK} ?>" class="modal fade invoice_form" role="dialog">
                <?php $this->load->view('finance/invoices/invoice_form'); ?>
            </div>
            <button title="Delete" style="color: white" invoice_id="<?= $invoice->{$invoice::DB_TABLE_PK} ?>" type="<?= $type ?>" class="btn btn-xs btn-danger delete_invoice">
                <i class="fa fa-trash"></i>
            </button>
        <?php } ?>
    </span>
<?php } ?>