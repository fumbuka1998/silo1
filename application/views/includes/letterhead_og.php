<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/5/2016
 * Time: 9:38 AM
 */

    $company_details = get_company_details();
?>
<table width="100%">
    <tr>
        <td style="text-align: center">
            <img style="height: 70px" src="<?= base_url('images/company_logo.png')?>">
        </td>
        <td style="text-align: center; font-size: 10px; color: #870C25;">
            <h2><?= $company_details['company_name'] ?></h2><br/>
            <p>
                <?= nl2br($company_details['address']) ?><br/>
                <?= intval($company_details['telephone']) != '' ? 'Tel: '.$company_details['telephone'].',' : '' ?>
                <?= intval($company_details['fax']) != '' ? ' Fax: '.$company_details['fax'].',' : '' ?>
                <?= intval($company_details['mobile']) != 0 ? ' Mobile: '.$company_details['mobile'] : '' ?><br/>
                <?= 'Email: '.$company_details['email'] ?>
            </p>
        </td>
    </tr>
</table>
