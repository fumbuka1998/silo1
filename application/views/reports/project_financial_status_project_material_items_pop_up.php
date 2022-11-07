<?php
?>
<a data-toggle="modal" data-target="#project_material_items_pop_up_<?= $project->{$project::DB_TABLE_PK} ?>" style="cursor: pointer">
    <?= number_format($project_material_items[$project->project_name]['material_balance_value'],2) ?>
</a>
<div id="project_material_items_pop_up_<?= $project->{$project::DB_TABLE_PK} ?>" class="modal fade" role="dialog">
    <div style="width: 90%" class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<form method="post" target="_blank" action="<?= base_url('reports/print_financial_status_report_pop_ups') ?>">
					<input type="hidden" name="pop_up_type" value="material_movement">
					<input type="hidden" name="as_of" value="<?= $as_of ?>">
					<input type="hidden" name="project" value="<?= urlencode(serialize($project)) ?>">
					<input type="hidden" name="items" value="<?= urlencode(serialize($items)) ?>">
					<input type="hidden" name="project_material_items" value="<?= urlencode(serialize($project_material_items)) ?>">
					<button type="submit" style="margin-right: 20px" class="button btn-default pull-right btn-xs"><i class="fa fa-file-pdf-o"> PDF</i></button>
				</form>
                <h4 class="modal-title"><?= $project->project_name ?> MATERIAL MOVEMENT</h4>
            </div>
            <form>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 table_container"  style="max-height: 440px; overflow-y: scroll">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th style="width: 2%">S/N</th>
                                    <th style="width: 40%">Item Name</th>
                                    <th>UOM</th>
                                    <th>Opening Stock</th>
                                    <th>Ordered</th>
                                    <th>Received From GRN</th>
                                    <th>Assigned In</th>
                                    <th>Used</th>
                                    <th>Sold</th>
                                    <th>Disposed</th>
                                    <th>Assigned Out</th>
                                    <th>On Transit</th>
                                    <?php foreach($project_material_items[$project->project_name]['locations'] as $location){ ?><th><?= strtoupper($location->location_name) ?></th><?php } ?>
                                    <th>Total Balance</th>
                                    <th style="width: 8%">Average Price</th>
                                    <th style="width: 8%">Installed Value</th>
                                    <th style="width: 8%">Balance Value</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $sn = 0;
                                foreach($items as $item){
                                    $sn++;
                                    ?>
                                    <tr>
                                        <td><?= $sn ?></td>
                                        <td style="text-align: left"><?= $project_material_items[$project->project_name][$item->item_name]['item']['item_name'] ?></td>
                                        <td style="text-align: left"><?= $project_material_items[$project->project_name][$item->item_name]['item']['uom'] ?></td>
                                        <td style="text-align: right"><?= $project_material_items[$project->project_name][$item->item_name]['item']['material_opening_stock'] ?></td>
                                        <td style="text-align: right"><?= $project_material_items[$project->project_name][$item->item_name]['item']['material_ordered'] ?></td>
                                        <td style="text-align: right"><?= $project_material_items[$project->project_name][$item->item_name]['item']['material_received_quantity'] ?></td>
                                        <td style="text-align: right"><?= $project_material_items[$project->project_name][$item->item_name]['item']['material_assigned_in_quantity'] ?></td>
                                        <td style="text-align: right"><?= $project_material_items[$project->project_name][$item->item_name]['item']['material_used_quantity'] ?></td>
                                        <td style="text-align: right"><?= $project_material_items[$project->project_name][$item->item_name]['item']['material_sold_quantity'] ?></td>
                                        <td style="text-align: right"><?= $project_material_items[$project->project_name][$item->item_name]['item']['material_disposed_quantity'] ?></td>
                                        <td style="text-align: right"><?= $project_material_items[$project->project_name][$item->item_name]['item']['material_assigned_out_quantity'] ?></td>
                                        <td style="text-align: right"><?= $project_material_items[$project->project_name][$item->item_name]['item']['material_on_transit'] ?></td>
                                        <?php foreach($project_material_items[$project->project_name]['locations'] as $location){ ?>
                                            <td><?= $project_material_items[$project->project_name][$item->item_name][$location->location_name] ?></td>
                                        <?php } ?>
                                        <td style="text-align: right"><?= $project_material_items[$project->project_name][$item->item_name]['item']['balance'] ?></td>
                                        <td style="text-align: right"><?= number_format($project_material_items[$project->project_name][$item->item_name]['item']['average_price'],2) ?></td>
                                        <td style="text-align: right"><?= number_format($project_material_items[$project->project_name][$item->item_name]['used_value'],2) ?></td>
                                        <td style="text-align: right"><?= number_format($project_material_items[$project->project_name][$item->item_name]['balance_value'],2) ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <tr style="background-color: #91e8e1;">
                                    <?php $colspan = 13 + count($project_material_items[$project->project_name]['locations']) ?>
                                    <td colspan="<?= $colspan ?>" style="text-align: left"><strong>GRAND TOTAL</strong></td>
                                    <td style="text-align: right;"><strong><?= number_format($project_material_items[$project->project_name]['material_cost_value'],2) ?></strong></td>
                                    <td style="text-align: right;"><strong><?= number_format($project_material_items[$project->project_name]['material_balance_value'],2) ?></strong></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
