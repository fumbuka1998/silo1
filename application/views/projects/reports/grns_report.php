<?php if($print){
    $this->load->view('includes/mpdf_css');
    $this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">GRNs REPORT</h2>
<br/>

<table style="font-size: 12px" width="100%">
    <tr>
        <td style=" width:50%">
            <strong>Project: </strong><?= $project->project_name ?>
        </td>
        <td style=" width:25%">
            <strong>From: </strong><?= custom_standard_date($from) ?>
        </td>
        <td style=" width:25%">
            <strong>To: </strong><?= custom_standard_date($to) ?>
        </td>
    </tr>
</table>
<br/>
<?php }?>

<table <?php if($print){ ?> style="font-size: 11px" width="100%" border="1" cellspacing="0"  <?php } ?> class="table table-bordered table-hover">
    <thead>
		 <tr>
		    <th>GRN Date</th><th>GRN No.</th><th>Vendor</th><th>Value</th>
		</tr>
    </thead>
    <tbody>

		<?php

		       $total_received_value = 0;

		        foreach ($grns as $grn) {
		            $total_received_value += $received_value = $grn->material_value();
                ?>
		        <tr>
		            <td><?= custom_standard_date($grn->receive_date) ?></td>
                    <td><?= $print ? $grn->grn_number() : anchor(base_url('inventory/preview_grn/'.$grn->{$grn::DB_TABLE_PK}),$grn->grn_number(),' target="_blank"') ?></td>
		            <td><?= $grn->source_name() ?></td>
		            <td style="text-align: right"><?= number_format($received_value,2) ?></td>
                </tr>
		      <?php  } ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3">TOTAL</th>
            <th style="text-align: right"><?= number_format($total_received_value,2) ?></th>
        </tr>
    </tfoot>

</table>