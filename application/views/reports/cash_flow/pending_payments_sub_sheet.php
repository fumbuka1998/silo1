<?php
$this->load->view('includes/letterhead');
?>
    <h2 style="text-align: center"><?= $project_name ?> APPROVED PAYMENTS</h2>
    <br/>
    <table style="font-size: 12px" width="100%">
        <tr>
            <td style="  vertical-align: top">
                <strong>From: </strong><?= custom_standard_date($from) ?>
            </td>
            <td style="  vertical-align: top">
                <strong>To: </strong><?= custom_standard_date($to) ?>
            </td>
        </tr>
    </table>
    <br/>
<table width="100%" border="1" cellspacing="0" style="font-size: 12px">
    <thead>
    <tr>
        <th>Approval Date</th>
        <th>Correspondence Number</th>
        <th>Approved Amount</th>
        <th>Approved By</th>
    </tr>
    <tbody>
    <?php
    $total_other_commitments_amount = 0;
    foreach ($other_commitments as $other_commitment){
        $total_other_commitments_amount += $other_commitment['approved_amount'];
        if($other_commitment['approved_amount'] > 0) {
            ?>
            <tr>
                <td style="text-align: left"><?= custom_standard_date($other_commitment['approval_date']) ?></td>
                <td style="text-align: left"><?= $other_commitment['approval_requisition_number'] ?></td>
                <td style="text-align: right"><?= $currency_symbol . ' ' . number_format($other_commitment['approved_amount'], 2) ?></td>
                <td style="text-align: left"><?= $other_commitment['approver_name'] ?></td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="2">TOTAL</th>
        <th style="text-align: right"><?= $currency_symbol.' '. number_format($total_other_commitments_amount) ?></th>
        <th></th>
    </tr>
    </tfoot>
</table>