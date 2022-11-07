<?php
/**
 * Created by PhpStorm.
 * User: bizytechlimited
 * Date: 24/10/2018
 * Time: 12:23
 */
?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#task_budget_details_<?= $task->{$task::DB_TABLE_PK} ?>"
            class="btn btn-xs btn-default">
        <i class="fa fa-briefcase"></i> Budget
    </button>
    <div id="task_budget_details_<?= $task->{$task::DB_TABLE_PK} ?>" class="modal fade task_budget_form"
         role="dialog">
        <?php $this->load->view('projects/budgets/task_budget_form');?>
    </div>
</span>
