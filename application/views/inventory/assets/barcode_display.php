<div class="modal-dialog">
    <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= $asset->asset_code() ?></h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12 barcode_container">
                        <?= $asset->barcode() ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default print_barcode">Print</button>
            </div>
    </div>
</div>