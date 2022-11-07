<?php
    $edit = isset($account);
    $account_for_options = [
        'other' => 'Other',
        'project' => 'Project',
        'contractor' => 'Contractor'
    ];

    $related_for_options = [];

    if($edit){
        $account_for = $account->account_for();
        if($account_for == 'project'){
            $project = $account->project();
            $account_for_options = ['project' => 'Project'];
            $related_for_options = [$project->{$project::DB_TABLE_PK} => $project->project_name];
        } else if($account_for == 'contractor'){
            $contractor = $account->contractor();
            $account_for_options = ['contractor' => 'Contractor'];
            $related_for_options = [$contractor->{$contractor::DB_TABLE_PK} => $contractor->contractor_name];
        } else {
            $account_for_options = [
                'other' => 'Other'
            ];
        }
    }


    if($loan_account_status) {

        ?>
        <br/><div style='text-align: center; font-size: 20px; font-weight: bold' class='alert alert-info'><i class='fa fa-warning'></i> Loan Account Aready Exist.!!</div>
        <?php
    }else {


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
                                    <input readonly type="text" class="form-control" required name="account_name"
                                           value="<?= $edit ? $account->account_name : strtoupper($employee->full_name()) . ' Loan Account' ?>">
                                    <input type="hidden" name="account_id"
                                           value="<?= $edit ? $account->{$account::DB_TABLE_PK} : '' ?>">
                                    <input type="hidden" name="employee_id"
                                           value="<?= $employee->{$employee::DB_TABLE_PK} ?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="account_for" class="control-label">Account For</label>
                                    <?= form_dropdown('account_for', $account_for_options, '', ' class="form-control searchable"') ?>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="owner" class="control-label">Related To</label>
                                    <?= form_dropdown('related_to', $edit ? $related_for_options : [], '', ' disabled="disabled" class="form-control searchable"') ?>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="account_group" class="control-label">Account Group</label>
                                    <?= form_dropdown('account_group_id',
                                        $edit ? [$account->account_group_id => $account->account_group()->group_name] : $account_group_options,
                                        $loan_account_group_id,
                                        ' class="form-control searchable"'
                                    ) ?>
                                </div>
                                <?php //if(!$edit){
                                ?>
                                <div class="form-group col-md-6">
                                    <label for="opening_balance" class="control-label">Opening Balance</label>
                                    <input type="text" class="form-control number_format" required
                                           name="opening_balance" value="">
                                </div>
                                <?php //}
                                ?>

                                <div <?= ($edit && $account->bank_id != '') ? '' : 'style="display: none"' ?>
                                        class="form-group col-xs-12" id="bank_options">
                                    <label for="description" class="control-label">Bank</label>
                                    <?= form_dropdown('bank_id', $edit ? $bank_options : [], $edit ? $account->bank_id : '', ' class="form-control searchable"') ?>
                                    <div <?= ($edit && $account->bank_id != '') ? '' : 'style="display: none"' ?>
                                            id="bank_details">
                                        <br/>
                                        <input style="background: #f4f4f4" id="account_number"
                                               value="<?= $edit && $account_details ? $account_details->account_number : '' ?>"
                                               placeholder="Account Number" class="col-xs-12 margin-bottom">
                                        <input style="background: #f4f4f4" id="branch"
                                               value="<?= $edit && $account_details ? $account_details->branch : '' ?>"
                                               placeholder="Branch" class="col-xs-12 margin-bottom">
                                        <input style="background: #f4f4f4" id="swift_code"
                                               value="<?= $edit && $account_details ? $account_details->swift_code : '' ?>"
                                               placeholder="Swift Code" class="col-xs-12">
                                    </div>
                                </div>

                                <div class="form-group col-xs-12">
                                    <label for="description" class="control-label">Description</label>
                                    <textarea class="form-control" required
                                              name="description"><?= $edit ? $account->description : '' ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button employee_id="<?= $employee->{$employee::DB_TABLE_PK} ?>" type="button" class="btn btn-sm btn-default save_account_human_resource">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <?php
    }