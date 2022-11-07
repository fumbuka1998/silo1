<?php
$edit = isset($sub_contractor);
$modal_heading = $edit ? $sub_contractor->name : 'New Sub-contractor';
$action = 'sub_contractors/save_sub_contractor/'.($edit ? $sub_contractor->{$sub_contractor::DB_TABLE_PK} : '');
//inspect_object($sub_contractor);
?>
<div class="modal-dialog">
    <div class="modal-content">
        <form method="post" action="<?= base_url($action) ?>">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= $modal_heading ?></h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-6">
                            <label for="name" class="control-label">Sub-contractor Name</label>
                            <input type="text" class="form-control" required name="name" value="<?= $edit ? $sub_contractor->name : '' ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email" class="control-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?= $edit ? $sub_contractor->email : '' ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="name" class="control-label">Phone</label>
                            <input type="text" class="form-control" name="phone" value="<?= $edit ? $sub_contractor->phone : '' ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="alternative_phone" class="control-label">Alternative Phone</label>
                            <input type="text" class="form-control" name="alternative_phone" value="<?= $edit ? $sub_contractor->alternative_phone : '' ?>">
                        </div>
                        <?php if(!$edit){ ?>

                            <div class="form-group col-md-6">
                                <label for="account_opening_balance" class="control-label">Account Opening Balance</label>
                                <input type="text" class="form-control number_format" name="account_opening_balance" value="0">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="currency_id" class="control-label">Currency</label>
                                <?= form_dropdown('currency_id',$currency_options,'',' class="form-control" ') ?>
                            </div>
                        <?php } ?>
                        
                        <div class="form-group col-xs-12">
                            <label for="address" class="control-label">Address</label>
                            <textarea name="address" rows="5" class="form-control"><?= $edit ? $sub_contractor->address : '' ?></textarea>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-default btn-sm">Save</button>
            </div>
        </form>
    </div>
</div>