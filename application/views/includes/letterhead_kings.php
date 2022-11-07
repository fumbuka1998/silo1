<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/5/2016
 * Time: 9:38 AM
 */

$this->load->view('includes/mpdf_css');
$company_details = get_company_details();
?>
<table cellpadding="3px" style="letter-spacing: 1px;" width="100%">
    <tr>
        <td style="text-align: center">
            <img style="width: 200px" src="<?= base_url('images/company_logo.png')?>"><br/>
        </td>
        <td style="text-align: center; vertical-align: top; font-size: 10px; color: #3c57a7;">
            <p style="font-size: 10px">
                <?= ucwords(nl2br($company_details->address)) ?><br/>
                Telephone: <?= nl2br($company_details->telephone) ?>
                Mobile: <?= nl2br($company_details->mobile) ?><br/>
                Email: <?= nl2br($company_details->email) ?>
                Website: <?= $company_details->website ?>
            </p><br/>
            <span style="color: #de8f26 !important; font-size: 11px; text-align: right !important;"><?= $company_details->tagline ?></span>
        </td>
    </tr>
</table>
<hr class="header_bottom_margin"/>

