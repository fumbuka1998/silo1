<?php if($print){
    $this->load->view('includes/mpdf_css');
    $this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">REQUISITION REPORT</h2>
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
		    <th>Request Date</th><th width="15%">Requisition Number</th><th>Required Date</th><th>Currency</th><th>Amount</th><th>Amount(TSH)</th><th>Status</th>
		</tr>
    </thead>
    <tbody>

		<?php 

		       $total_amount_in_base_currency = 0;

		        foreach ($requisitions as $requisition) {
		            $total_amount_in_base_currency += $amount_in_base_currency = $requisition->total_amount_in_base_currency();
                ?>

		        <tr>

		            <td><?= custom_standard_date($requisition->request_date) ?></td>
		            <td><?= $print ? $requisition->requisition_number() : anchor(base_url('requisitions/preview_requisition/'.$requisition->{$requisition::DB_TABLE_PK}),$requisition->requisition_number(),' target="_blank"') ?></td>
		            <td><?= $requisition->required_date != null ? custom_standard_date($requisition->required_date) : 'N/A' ?></td>
                    <td><?= $requisition->currency()->name_and_symbol() ?></td>
                    <td style="text-align: right"><?= number_format($requisition->total_requested_amount(),2) ?></td>
                    <td style="text-align: right"><?= number_format($amount_in_base_currency,2) ?></td>
		            <td><?=  $requisition->progress_status_label() ?></td>
                </tr>

		      <?php  } ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5">TOTAL</th><th style="text-align: right"><?= number_format($total_amount_in_base_currency,2) ?></th><td></td>
        </tr>
    </tfoot>

</table>