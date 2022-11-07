<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 5/12/2017
 * Time: 12:40 PM
 */

if($employee_id || check_permission('Administrative Actions')) {
    ?>
    <span class="pull-right">
    <button class="btn btn-xs btn-danger cost_item_delete" item_id="<?= $item_id ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>
    <?php
}
