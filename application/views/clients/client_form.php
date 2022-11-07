<?php
$edit = isset($client);
$modal_heading = $edit ? $client->client_name : 'New Client';
$action = 'clients/save/'.($edit ? $client->{$client::DB_TABLE_PK} : '');
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
                            <label for="client_name" class="control-label">Client Name</label>
                            <input type="text" class="form-control" required name="client_name" value="<?= $edit ? $client->client_name : '' ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email" class="control-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?= $edit ? $client->email : '' ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="client_name" class="control-label">Phone</label>
                            <input type="text" class="form-control" name="phone" value="<?= $edit ? $client->phone : '' ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="alternative_phone" class="control-label">Alternative Phone</label>
                            <input type="text" class="form-control" name="alternative_phone" value="<?= $edit ? $client->alternative_phone : '' ?>">
                        </div>
                        <?php if(!$edit){ ?>
                            <div class="form-group col-md-6">
                                <label for="account_opening_balance" class="control-label">Account Opening Balance</label>
                                <input type="text" class="form-control number_format" name="account_opening_balance" value="0">
                            </div>
                        <?php } ?>
                        <div class="form-group <?= $edit ? 'col-xs-12' : 'col-md-6' ?>">
                            <label for="address" class="control-label">Address</label>
                            <textarea name="address" class="form-control"><?= $edit ? $client->address : '' ?></textarea>
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