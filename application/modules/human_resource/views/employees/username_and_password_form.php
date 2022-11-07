<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 09/04/2018
 * Time: 18:12
 */
?>
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
