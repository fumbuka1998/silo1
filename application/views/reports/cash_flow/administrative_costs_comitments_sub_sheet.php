<?php
$this->load->view('includes/letterhead');
?>
    <h2 style="text-align: center"><?= strtoupper($cost_center_name). ' APPROVED PAYMENTS' ?></h2>
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
<?php
$this->load->view('reports/cash_flow/approved_payments_table');
?>