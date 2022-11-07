<?php
    $edit = isset($account) && !is_null($account);
    $has_group = isset($account_group);
    $parent_id = isset($parent) ? $has_group ? $account_group->parent_id : $parent->{$parent::DB_TABLE_PK} : null;
    $account_for_options = [
        'other' => 'Other',
        'project' => 'Project',
        'cost_center' => 'Cost Center'
    ];

    $related_for_options = [];

    if($edit){
        $account_for = $account->account_for();
        if($account_for == 'project'){
            $project = $account->project();
            $account_for_options = ['project' => 'Project'];
            $related_for_options = [$project->{$project::DB_TABLE_PK} => $project->project_name];
        } else if($account_for == 'cost_center'){
            $cost_center = $account->cost_center();
            $account_for_options = ['cost_center' => 'Cost Center'];
            $related_for_options = [$cost_center->{$cost_center::DB_TABLE_PK} => $cost_center->cost_center_name];
        } else {
            $account_for_options = [
                'other' => 'Other'
            ];
        }
    }
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Account Information Form</h4>
        </div>
        <form>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
					<div class="form-group col-xs-12">
						<label for="account_name" class="control-label">Account Name</label>
						<input type="text" class="form-control" required name="account_name" value="<?= $edit ? $account->account_name : '' ?>">
						<input type="hidden" name="account_group" value="<?= $has_group ? strtoupper($account_group) : '' ?>">
						<input type="hidden" name="parent_id" value="<?= $parent_id ?>">
						<input type="hidden" name="account_id" value="<?= $edit ? $account->{$account::DB_TABLE_PK} : '' ?>">
					</div>
					<div class="form-group col-md-6">
						<label for="account_for" class="control-label">Account For</label>
						<?= form_dropdown('account_for',$account_for_options,'','class="form-control searchable"') ?>
					</div>
					<div class="form-group col-md-6">
						<label for="owner" class="control-label">Related To</label>
						<?= form_dropdown('related_to',$related_for_options,'','class="form-control searchable"') ?>
					</div>
					<div class="form-group col-md-6">
						<label for="currency_id" class="control-label">Currency</label>
						<?= form_dropdown('currency_id',$currency_options,$edit ? $account->currency_id : '','class="form-control searchable"') ?>
					</div>
					<?php if(!$has_group || ($has_group && $account_group == "ledger")){ ?>
					<div class="form-group col-md-6">
						<label for="account_group" class="control-label">Account Group</label>
						<?= form_dropdown('account_group_id',
							$edit ? [$account->account_group_id => $account->account_group()->group_name] : account_group_dropdown_options('all',$parent_id),
							$edit ? $account->account_group_id : '',
							' class="form-control searchable"'
						) ?>
					</div>
					<?php }
					if(!$edit){ ?>
						<div class="form-group col-md-6">
							<label for="opening_balance" class="control-label">Opening Balance</label>
							<input type="text" class="form-control number_format" required name="opening_balance" previous value="<?= $edit ? $account->opening_balance : 0 ?>">
						</div>
					<?php } ?>
					<div class="form-group col-md-6">
						<label for="account_code" class="control-label">Account Code</label>
						<input type="text" class="form-control "  name="account_code" placeholder="Optional" value="<?= $edit ? $account->account_code : '' ?>">
					</div>

                    <div <?= (($edit && $account->bank_id != '') || ($has_group && $account_group == "bank")) ? '' : 'style="display: none"' ?> class="form-group col-xs-12" id="bank_options">
                        <label for="description" class="control-label">Bank</label>
                        <?= form_dropdown('bank_id', $bank_options,$edit ? $account->bank_id : '' ,' class="form-control searchable"') ?>
                        <div <?= ($edit && $account->bank_id != '') ? '' : 'style="display: none"' ?> id="bank_details">
                            <br/>
                            <input style="background: #f4f4f4" id="account_number" value="<?= $edit && $account_details ? $account_details->account_number : '' ?>" placeholder="Account Number" class="col-xs-12 margin-bottom">
                            <input style="background: #f4f4f4" id="branch" value="<?= $edit && $account_details ? $account_details->branch : '' ?>" placeholder="Branch" class="col-xs-12 margin-bottom">
                            <input style="background: #f4f4f4" id="swift_code" value="<?= $edit && $account_details ? $account_details->swift_code : '' ?>" placeholder="Swift Code" class="col-xs-12">
                        </div>
                    </div>

                    <div class="form-group col-xs-12">
                        <label for="description" class="control-label">Description</label>
                        <textarea class="form-control" required name="description"><?= $edit ? $account->description : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-default save_account">Save</button>
        </div>
        </form>
    </div>
</div>
