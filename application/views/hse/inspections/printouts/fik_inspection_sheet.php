<?php
$this->load->view('includes/letterhead');
?>
<br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td style=" width: 30%; text-align: left">
            <strong>FIRST AID KIT CHECKLIST </strong><br/>
        </td>
        <td style=" width: 30%; text-align: right">
            <strong>Project: </strong><?= $inspections->site()->project_name ?>
        </td>
    </tr>

</table>
<br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td style=" width: 30%; text-align: left">
            <strong>SITE ID.: </strong><?= $inspections->site()->generated_project_id() ?>
        </td>
        <td style=" width: 40%; text-align: right">
            <strong>Site Name: </strong><?= $inspections->location ?>
        </td>
    </tr>

</table>
<br/>
<hr/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td style=" width: 40%; text-align: left">Date: ................................................</td>
        <td style=" width: 40%; text-align: left">First Aid Kit Identification No......................</td>
    </tr>

</table>
<hr/>
<br/>

<table style="font-size: 12px" width="100%">
    <tr>
        <th style="width: 60%; text-align: left">Requirement(Availability And Validation)</th>
        <th style="width: 10%; text-align: left">Compliance</th>
        <th style="width: 10%; text-align: left">NC</th>
        <th style="width: 10%; text-align: left">N/A</th>
    </tr>
    <?php
    foreach ($inspections->inspection_category()->inspection_category_parameters() as $param) {
    $parameter = $param->category_parameter();
    ?>
        <tr>
            <td><ul><li><b><u><?= $parameter->name ?></u></b></li></ul></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    <?php foreach ($param->inspection_category_parameter_type() as $param_type) {
        $type = $param_type->parameter_type();
          ?>
        <tr>
            <td><?= $type->name ?></td>
            <td><?= ($param_type->is_checked == 1) ? '<input type="checkbox"/>' : '<input type="checkbox" checked="true"/>' ?></td>
            <td><input type="checkbox"/></td>
            <td><input type="checkbox"/></td>
        </tr>

    <?php
     }
       }
    ?>


</table>

<br/><hr/>
<p>Inspected By: ........................................................................</p>
<p>Date and Signature : ............................................................</p>
<p style="font-size: 08px;">NOTE  : ALL ABOVE A MINIMUM REQUIREMENT FOR ONE FIRST AID KIT ARE REQUIRED TO BE STORED IN A BOX (FIRST AID KIT) ON COOL DRY AREA.</p>


