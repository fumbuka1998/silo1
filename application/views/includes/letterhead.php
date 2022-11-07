<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/5/2016
 * Time: 9:38 AM
 */

$company_details = get_company_details();
$this->load->view('includes/mpdf_css',['company_details' => $company_details]);
?>
<table style="letter-spacing: 1px;" width="100%">
    <tr>
        <td style="text-align: center">
            <img style="width: 100px" src="<?= base_url('images/company_logo.png')?>"><br/>
            <span style="width:100%; font-size: 11px; text-align: right !important;"><?= $company_details ? $company_details->website : '' ?></span>
        </td>
        <td style="text-align: center; font-size: 10px; color: <?= isset($company_details) ? $company_details->corporate_color : '#870C25' ?>;">
            <h2 style="font-size: 30px"><?= $company_details ? strtoupper($company_details->company_name) : ''  ?></h2><br/>
            <p style="font-size: 20px">
                <?= $company_details ? ucwords(nl2br($company_details->tagline)) : ''  ?>
            </p>
            <p><?= nl2br($company_details->address) ?></p>
            <p>
                <strong>TIN:</strong> <?= $company_details->tin ?>, <strong>VRN:</strong> <?= $company_details->vrn ?>
            </p>
            <p>
                <strong>Tel:</strong> <?= $company_details->telephone ?>
                <strong>Mobile:</strong> <?= $company_details->mobile ?>
                <strong>Email:</strong> <?= $company_details->email ?>
            </p>
        </td>
    </tr>
</table>
<hr class="header_bottom_margin"/>


