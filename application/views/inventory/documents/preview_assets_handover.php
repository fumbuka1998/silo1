<?php
/**
 * Created by PhpStorm.
 * User: Munyaki
 * Date: 02-Oct-17
 * Time: 1:02 PM
 */


$this->load->view('includes/letterhead');
?>


<h2 style="text-align:center; font-size: 15px">HANDOVER ASSETS</h2>
<hr/>
<table width="100%">
    <tr>
        <th>Handover Date: </th><td><?= custom_standard_date($asset_handover->handover_date) ?></td>
        <th>Handover Date: </th><td><?= $asset_handover->location()->location_name ?></td>
    </tr>
</table>
<br/>

<table style="font-size: 10px" width="100%" border="1" cellspacing="0">
    <thead>
    <tr style="background: #cdcdcd; color: #ed1c24; ">
        <th style="width: 5%">SN</th><th width="20%">Asset Name</th><th width="20%">Remarks</th>
    </tr>

    </thead>
    <tbody>
        <?php
            $sn = 1;
            $items = $asset_handover->items();
            foreach ($items as $item){
                $asset = $item->asset_sub_location_history()->asset();
                ?>
                <tr>
                    <td><?= $sn ?></td>
                    <td><?= $asset->asset_code() ?></td>
                    <td><?= $item->remarks ?></td>
                </tr>
        <?php
                $sn++;
            }
        ?>
    </tbody>
</table>
<br><br>
<div style="font-size: 12px">
    <strong>Comments</strong><br/>
    <?= trim($asset_handover->comments) != '' ? $asset_handover->comments : 'N/A' ?>
</div>
<br/>
<table style="font-size: 12px;" width="100%">
    <tr>
        <td  style=" width:50%; vertical-align: top; text-align: left">
            <strong>Handler Name: </strong><?= $asset_handover->handler()->full_name() ?>
        </td>
        <td  style=" width:50%; vertical-align: top">
            <strong>Assigner Name: </strong><?= $asset_handover->assignor()->full_name() ?>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: bottom"><br/>
            <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span>
        </td>
        <td style="vertical-align: bottom"><br/>
            <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span>
        </td>
    </tr>
</table>
