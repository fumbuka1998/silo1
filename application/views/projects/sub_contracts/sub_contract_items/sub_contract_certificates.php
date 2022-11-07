<?php
if(!empty($sub_contract_certificates)) {
    ?>


    <table class="table table-hover table-bordered ">
        <tbody>
        <?php
        $total_sum = 0;
        foreach($sub_contract_certificates as $sub_contract_certificate){
            $total_sum+= $sub_contract_certificate->certified_amount;
            ?>


            <tr>

                <td style="width:30%;">
                    <?= $sub_contract_certificate->certificate_number; ?>
                </td>
                <td style="width:25%;">
                    <?= number_format($sub_contract_certificate->certified_amount); ?>
                </td>
                <td style="width:20%;"  >
                    <?= $sub_contract_certificate->remarks; ?>
                </td>
                <td style="width: 15%"></td>
                <td >
                            <span class="pull-right">
                            <button type="button" sub_contract_certificate_id="<?= $sub_contract_certificate->id ?>" class="btn btn-xs btn-danger delete_sub_contract_certificate" >
                                <i class="fa fa-trash"></i> Delete
                            </button>
                            </span>
                </td>
            </tr>

        <?php } ?>
        <tbody>
        <tfoot>
        <tr style="background-color: #f0f0f0">
            <th colspan="4">Total</th><th id="total_budget_amount_display" style="text-align: right">  <?= number_format($total_sum) ?> </th><th colspan="1"></th>
        </tr>
        </tfoot>
    </table>

    <?php
}else {
    ?>
    <div class="alert alert-warning">No Contract Items Found for this Contract</div>

    <?php
}
?>