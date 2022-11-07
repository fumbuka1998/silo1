<?php
$this->load->view('includes/letterhead');
?>
    <h2 style="text-align: center"><?= $project_name ?> PAYMENTS</h2>
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
        <th>Payment Nature</th>
        <th>Reference</th>
        <th>Amount</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $bg = '#efefef';
    $total_amount = 0;
    foreach($project_payments as  $payment) {
        ?>
        <tr style="background: <?= $bg ?>">
            <td style="text-align: left"><?= $payment['approved_date'] ?></td>
            <td style="text-align: left"><?= $payment['comments'] ?></td>
            <td style="text-align: left"><?= $payment['reference_without_anchor'] ?></td>
            <td style="text-align: right"><?= 'TSH '.number_format($payment['amount']) ?></td>
        </tr>
        <?php
        $total_amount += $payment['amount'];
        $bg = $bg == '#efefef' ? '#ffffff' : '#efefef';
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <td style="font-weight: bold; text-align: left" colspan="3">TOTAL</td>
        <td style="font-weight: bold; text-align: right"><?= number_format($total_amount,2)?></td>
    </tr>
    </tfoot>
</table>