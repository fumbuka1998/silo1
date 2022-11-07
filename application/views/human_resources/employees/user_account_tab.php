<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/9/2016
 * Time: 7:13 PM
 */
$user = $employee->user();
?>
<div class="box">
        <form>
            <div class="box-header">

                <div class="form-group col-md-3">
                    <label  class="control-label">Username:</label>
                    <input type="text" class="form-control" name="username" <?= !empty($user) ? 'readonly' : '' ?> value="<?= !empty($user) ? $user->username : '' ?>">
                    <input type="hidden" name="employee_id" value="<?= $employee->{$employee::DB_TABLE_PK} ?>">
                    <input type="hidden" name="user_id" value="<?= !empty($user) ? $user->{$user::DB_TABLE_PK} : '' ?>">
                </div>
                <div class="form-group col-md-3">
                    <label  class="control-label">Password:</label>
                    <input class="form-control" type="password" name="password">
                </div>

                <div class="form-group col-md-3">
                    <label  class="control-label">Confirm Password:</label>
                    <input class="form-control" type="password" name="confirm_password">
                </div>

                <div class="form-group col-sm-3">
                    <label  class="control-label">Active:</label>
                    <div class="form-control-static">
                        <input class="" type="checkbox" <?= check_permission('Human Resources') ? '' : 'disabled' ?> name="active" <?= !empty($user) && $user->active == 1 ? 'checked' : '' ?>>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <?php
                if(check_permission('Human Resources')) {
                    ?>
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#user_permissions" data-toggle="tab">Contracts</a></li>
                            <li><a href="#user_account_details" data-toggle="tab">Authorised Approvals</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="active tab-pane" id="user_permissions">

                            </div>
                            <!-- /.tab-pane -->
                            <div class=" tab-pane" id="user_account_details">

                            </div>
                            <!-- /.tab-pane -->
                        </div>
                    </div>

                    <div class="col-xs-12 bg-gray" style="text-align: center">
                        <strong>Select user permissions</strong>
                    </div>
                    <hr/>
                    <?php
                    $employee_permissions = !empty($user) ? $user->permission_ids() : [];
                    foreach ($permissions as $permission) {
                        $permission_id = $permission->{$permission::DB_TABLE_PK};
                        ?>
                        <div class="form-group col-sm-4">
                            <label class="col-sm-10 control-label"><?= $permission->name ?>:</label>

                            <div class="form-control-static col-sm-2">
                                <input class="employee_permissions" type="checkbox"
                                       value="<?= $permission_id ?>" <?= in_array($permission_id, $employee_permissions) ? 'checked' : '' ?>>
                            </div>
                        </div>
                        <?php
                    }
                }

                ?>
                <div class="col-xs-12">
                    <hr/>
                    <button type="button" class="btn btn-sm btn-default pull-right">
                        <i class="fa fa-save"></i> Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>