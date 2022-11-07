<?php
$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">COST CENTER PAYMENT(S) REPORT</h2>
<br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td  style=" vertical-align: top">
            <strong>From: </strong><?= $from ?>
        </td>
        <td  style=" vertical-align: top">
            <strong>To: </strong><?= $to ?>
        </td>
    </tr>
    <tr>
        <td colspan="3"><hr/></td>
    </tr>
    <?php
    if($cost_center != false) {
        ?>
        <tr>
            <td style="width: 40%;vertical-align: top">
                <strong>Cost Center: </strong><?= $cost_center->cost_center_name ?>
            </td>
        </tr>
        <?php
    } else {
        ?>
        <tr>
            <td style="width: 40%;vertical-align: top">
                <strong>Cost Center: </strong><?= 'ALL' ?>
            </td>
        </tr>
        <?php
    }
    ?>
</table>
<br/>
<?php
$this->load->view('reports/cost_center_payments_table');
?>
