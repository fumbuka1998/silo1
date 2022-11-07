<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 09/04/2018
 * Time: 18:50
 */
?>
<div class="row">
    <div class="col-xs-12">
        <br/>
        <?php
        $employee_permissions = !empty($user) ? $user->permission_ids() : [];
        $employee_privileges = !empty($user) ? $user->permission_privilege_ids() : [];
        foreach ($permissions as $permission) {
            $permission_id = $permission->{$permission::DB_TABLE_PK};
            ?>
            <div class="form-group col-sm-12 mb-3">
                <div class="row col-xs-12">
                    <button type="button" class="btn btn-default col-xs-12 employee_permission mb-1" data-toggle="collapse" data-target="#permission_privilege_<?= $permission->{$permission::DB_TABLE_PK} ?>"><?= $permission->name ?></button>

                    <div id="permission_privilege_<?= $permission->{$permission::DB_TABLE_PK} ?>" class="collapse mt-2 permission_privileges">
                        <div class="col-xs-12 mb-1 mt-1">
                            <div class="col-xs-6">
                                <div class="form-group col-sm-12">
                                    <label class="col-sm-10 control-label"><?= $permission->name ?> :</label>
                                    <div class="form-control-static col-sm-2">
                                        <input class="user_permission" type="checkbox"
                                               value="<?= $permission_id ?>" <?= in_array($permission_id, $employee_permissions) ? 'checked' : '' ?>>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group col-sm-12">
                                    <label class="col-sm-10 control-label">Grant all privileges :</label>
                                    <div class="form-control-static col-sm-2">
                                        <input type="checkbox" class="checkAll">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 table-responsive">
                            <table id="user_previlege_table" permission_id = "<?= $permission->{$permission::DB_TABLE_PK} ?>" class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th><span>Privilege</span></th>
                                    <th>Grant/Revoke</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $sn = 0;
                                $permisions_privileges = $permission->permission_privileges();
                                foreach($permisions_privileges as $permisions_privilege){
                                    $sn++;
                                    $permisions_privilege_id = $permisions_privilege->{$permisions_privilege::DB_TABLE_PK};
                                    ?>
                                    <tr>
                                        <td><?= $sn ?></td>
                                        <td><?= $permisions_privilege->privilege ?></td>
                                        <td>
                                            <div class="form-control-static col-sm-2">
                                                <input class="permission_privilege" type="checkbox" value="<?= $permisions_privilege_id ?>" <?= in_array($permisions_privilege_id, $employee_privileges) ? 'checked' : '' ?>>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        } ?>
    </div>
</div>
