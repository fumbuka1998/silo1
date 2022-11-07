
<?php $this->load->view('includes/letterhead'); ?>
<h2 style="text-align: center">LOCATION MATERIAL DISPOSAL</h2>
<br />

<table cellspacing="10" width="100%">
    <tr>
        <td style="width: 40%; vertical-align: top">
            <strong>Location: </strong><br /><?= $location_name ?>
        </td>
        <td style="vertical-align: top">
            <strong>From: </strong><br /><?= custom_standard_date($from) ?>
        </td>
        <td style="vertical-align: top">
            <strong>To: </strong><br /><?= custom_standard_date($to) ?>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <hr />
        </td>
    </tr>
</table>
<br />
<?php $this->load->view('inventory/reports/material_disposal_report_table');
