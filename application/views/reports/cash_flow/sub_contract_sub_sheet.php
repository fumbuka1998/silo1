<?php
$this->load->view('includes/letterhead');
?>
    <h2 style="text-align: center"><?= $project_name ?> SUB-CONTRACTORS CERTIFICATES</h2>
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
<table width="100%" border="1" cellspacing="0" style="font-size: 11px">
    <thead>
    <tr>
        <th>Date</th>
        <th>Certificate No</th>
        <th>Subcontract Descr:</th>
        <th>Certified Amount</th>
        <th>Paid Amount</th>
        <th>Balance</th>
    </tr>
    <tbody>
    <?php
    $total_certified_amount = 0;
    $total_certificate_paid_amount = 0;
    foreach ($sub_contractor_wit_certificate as $certificate){
        $total_certified_amount = $total_certified_amount + $certificate['certified_amount'];
        $total_certificate_paid_amount = $total_certificate_paid_amount + $certificate['amount_paid'];
        ?>
        <tr>
            <td style="text-align: left"><?= $certificate['certificate_date'] ?></td>
            <td style="text-align: left"><?= $certificate['certificate_number'] ?></td>
            <td style="text-align: left"><?= $certificate['subcontract_description'] ?></td>
            <td style="text-align: right"><?= $currency_symbol.' '.number_format($certificate['certified_amount'],2) ?></td>
            <td style="text-align: right"><?= $currency_symbol.' '.number_format($certificate['amount_paid'],2) ?></td>
            <td style="text-align: right"><?= $currency_symbol.' '.number_format($certificate['current_certificate_balance'],2) ?></td>
        </tr>

        <?php
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="3">TOTAL</th>
        <th style="text-align: right"><?= $currency_symbol.' '. number_format($total_certified_amount) ?></th>
        <th style="text-align: right"><?= $currency_symbol.' '. number_format($total_certificate_paid_amount) ?></th>
        <th style="text-align: right"><?= $currency_symbol.' '. number_format($sub_contracts_commitments) ?></th>
    </tr>
    </tfoot>
</table>