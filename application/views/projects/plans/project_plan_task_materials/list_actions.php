<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 7/4/2018
 * Time: 12:38 AM
 */

if (check_privilege('Project Actions')) {

    ?>
    <div style="width: 80%">
        <button type="button" style="color: white" class="btn btn-danger btn-xs delete_plan_material_budget"
                plan_material_budget_id="<?= $plan_material_budget->{$plan_material_budget::DB_TABLE_PK} ?>">
            <i class="fa fa-trash"></i> Delete
        </button>
    </div>

    <?php
}