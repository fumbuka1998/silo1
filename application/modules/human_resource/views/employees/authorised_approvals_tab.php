<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 07/04/2018
 * Time: 17:34
 */
?>

<div class="row">
    <div class="col-xs-12">
        <table class="table table-bordered table-hover approval_levels_table">
            <thead>
                <tr>
                    <th>Approval Module</th><th>Level</th><th></th>
                </tr>
                <tr style="display: none" class="row_template">
                    <td>
                        <?= form_dropdown('approval_module_id',approval_module_dropdown_options(),'',' class="form-control " ') ?>
                    </td>
                    <td>
                        <?= form_dropdown('approval_chain_level_id', [], '', ' class="form-control" '); ?>
                    </td>
                    <td>
                        <button type="button" class="btn btn-xs btn-danger row_remover">
                            <i class="fa fa-close"></i>
                        </button>
                    </td>
                </tr>
            </thead>
            <?php    $levels = $employee->approval_chain_levels(); ?>
            <tbody has_levels="<?= !empty($levels) ? 'true' : 'false' ?>">
                <?php
                    if(!empty($levels)){
                        foreach ($levels as $level){
                            $module = $level->approval_module();
                           ?>
                            <tr>
                                <td>
                                    <?= form_dropdown('approval_module_id',[$level->approval_module_id => $module->module_name],$level->approval_module_id,' class="form-control"') ?>
                                </td>
                                <td>
                                    <?= form_dropdown('approval_chain_level_id',[$level->{$level::DB_TABLE_PK} => $level->level_name],$level->{$level::DB_TABLE_PK},' class="form-control"') ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-xs btn-danger row_remover">
                                        <i class="fa fa-close"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                <tr>
                    <td colspan="3">
                        <div class="alert alert-info">This user has no approval authorities</div>
                    </td>
                </tr>
                <?php
                    }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td colspan="2">
                        <button type="button" class="add_approval_level_row btn btn-default btn-xs pull-right">
                            Add Level
                        </button>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
