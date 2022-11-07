<?php
if(!empty($sub_contract_items)) {
    ?>
    <table class="table table-hover table-bordered ">
        <tbody>
        <?php
        $total_sum = 0;
        foreach($sub_contract_items as $sub_contract_item){
            $total_sum+= $sub_contract_item->contract_sum;
            ?>
            <tr>
                <td style="width:25%;">
                    <?= $sub_contract_item->task()->task_name ?: 'Project Shared'; ?>
                </td>
                <td style="width:15%;">
                    <?= custom_standard_date($sub_contract_item->start_date); ?>
                </td>
                <td style="width:15%;">
                    <?= custom_standard_date($sub_contract_item->end_date); ?>
                </td>
                <td style="width:20%;"  >
                    <?= $sub_contract_item->description; ?>
                </td>
                <td style="width:15%;">
                    <span class="pull-right"><?= number_format($sub_contract_item->contract_sum); ?></span>
                </td>
                <td>
					<span class="pull-right">
					<button type="button" sub_contract_item_id="<?= $sub_contract_item->id ?>" class="btn btn-xs btn-danger delete_sub_contract_item" >
						<i class="fa fa-trash"></i> Delete
					</button>
					</span>
                </td>
            </tr>
        <?php } ?>
        <tbody>
        <tfoot>
        <tr style="background-color: #f0f0f0">
            <th colspan="4">Total</th><th class="total_sub_contract_amount_display" style="text-align: right">  <?= number_format($total_sum) ?> </th><th colspan="1"></th>
        </tr>
        </tfoot>
    </table>
    <?php
} else {
    ?>
    <div class="alert alert-warning">No Contract Items Found for this Contract</div>
    <?php
}
?>
