<span class="pull-left">
    <a title="View GRN" class="btn btn-default btn-xs" target="_blank" href="<?= base_url('inventory/preview_grn/' . $grn->grn_id) ?>">
        <i class="fa fa-eye"></i>
    </a>
    <button title="GRN Attachments" class="btn btn-xs" data-toggle="modal" data-target="#attachment_<?= $grn->{$grn::DB_TABLE_PK} ?>">
        <i class="fa fa-paperclip"></i>
    </button>
    <div id="attachment_<?= $grn->{$grn::DB_TABLE_PK} ?>" class="modal fade procurement_attachment_modal" role="dialog">
        <?php $this->load->view('attachments/purchase_order_related/index', ['reffering_to' => 'GRN']); ?>
    </div>
</span>