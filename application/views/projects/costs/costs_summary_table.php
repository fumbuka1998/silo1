<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 4/23/2017
 * Time: 4:18 AM
 */
?>
<div class="col-xs-12 table-responsive">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Cost Type</th>
                <?php $show_budgets = check_permission('Budgets');
                if($show_budgets){ ?><th>Budgeted Amount</th><?php } ?>
                <th>Actual Cost</th>
                <?php if($show_budgets){ ?><th>Percentage Used</th><?php } ?>
            </tr>
        </thead>
        <tbody>
        <?php
            $cost_types = ['material','equipment','miscellaneous','permanent_labour','casual_labour','sub_contract'];
            $total_budget = $total_actual_cost = $total_percentage_used = 0;
            foreach ($cost_types as $cost_type){
                if($show_budgets){ $total_budget += $budgeted_amount = $cost_center->budget_figure($cost_type,$general_only); }
                $total_actual_cost += $actual_cost = $cost_center->actual_cost($cost_type,null,null,$general_only);
                if($show_budgets){ $total_percentage_used += $percentage_used = round($actual_cost*100/$budgeted_amount,5); }
                ?>
                <tr>
                    <th><?= ucwords(str_replace('_', ' ', $cost_type)) ?></th>
                    <?php if($show_budgets){ ?><td style="text-align: right"><?= number_format($budgeted_amount) ?></td> <?php } ?>
                    <td style="text-align: right"><?= number_format($actual_cost) ?></td>
                    <?php if($show_budgets){ ?><td style="text-align: right"><?= $percentage_used ?>%</td> <?php } ?>
                </tr>
        <?php
            }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th>TOTAL</th>
                <?php if($show_budgets){ ?><th style="text-align: right"><?= number_format($total_budget) ?></th> <?php } ?>
                <th style="text-align: right"><?= number_format($total_actual_cost) ?></th>
                <?php if($show_budgets){ ?><th style="text-align: right"><?= $cost_center->budget_spending_percentage($cost_types,$general_only) ?>%</th> <?php } ?>
            </tr>
        </tfoot>
    </table>
</div>
