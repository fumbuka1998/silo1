<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 8/22/2017
 * Time: 3:48 PM
 */
?>

<table  <?php if($print){ ?> style="font-size: 11px" width="100%" border="1" cellspacing="0"  <?php } else { ?> class="table table-bordered table-hover" <?php } ?> >
    <thead>
        <tr>
            <th>SN</th><th>Item Name</th><th>UOM</th>
            <?php if($projectwise){ ?>
             <th>Project(s)</th>
            <?php } if($sub_locationwise){ ?>
             <th>Sub Location(s)</th>
            <?php } ?>
            <th>Balance</th>
            <?php
                if($allow_rates){
                    $total_amount = 0;
            ?>
                    <th>Rate</th>
                    <th>Amount</th>
            <?php
                }
            ?>
        </tr>
    </thead>
    <tbody>
<?php
    $sn = 0;
    foreach ($table_items as $item){
        $sn++;
        if($allow_rates) {
            $total_amount += $amount = $item['rate'] * $item['balance'];
        }
    ?>
        <tr>
            <td><?= $sn ?></td>
            <td><?= $item['item_name'] ?></td>
            <td><?= $item['unit'] ?></td>
            <?php
            if($projectwise){
            ?>
            <td>
                <table <?php if($print){ ?> style="font-size: 11px" width="100%" border="1" cellspacing="0"  <?php } else { ?> class="table table-bordered" <?php } ?> >
                    <tbody>
                    <?php
                    $material_item = material_item_object($item['item_id']);
                    $projects = $material_item->projects_with_this_item();
                    foreach ($projects as $project){
                        $per_project_balance = $material_item->sub_location_balance($sub_location_ids, $project->project_id, $to, $transfer_type);
                        $project_name = ($project->project_name == '' || $project->project_name == null) ? "UNASSIGNED" : $project->project_name;
                        if($per_project_balance > 0) {
                            ?>
                            <tr>
                                <td><?= $project_name ?></td>
                                <td style="text-align: right" width="50px"><?= $per_project_balance ?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </td>
            <?php
            } if($sub_locationwise && $item['balance'] > 0){ ?>
            <td>
                <table <?php if($print){ ?> style="font-size: 11px" width="100%" border="1" cellspacing="0"  <?php } else { ?> class="table table-bordered" <?php } ?> >
                    <tbody>
                    <?php foreach($item['sub_locations_array'] as $sub_location) { ?>
                    <tr>
                        <td><?= $item['sub_location_balances'][$item['item_id']][$sub_location->sub_location_name][0] ?></td>
                        <td style="text-align: right" width="50px"><?= $item['sub_location_balances'][$item['item_id']][$sub_location->sub_location_name][1] ?></td>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </td>
            <?php }
            ?>
            <td style="text-align: right"><?= $item['balance'] ?></td>
            <?php
                if($allow_rates){
            ?>
            <td style="text-align: right"><?= number_format($item['rate'],2) ?>
            <td style="text-align: right"><?= number_format($amount,2) ?>
            <?php
                }
            ?>
        </tr>
    <?php
    }
?>
    </tbody>
    <?php
        if($allow_rates){
            ?>
            <tfoot>
                <tr>
                    <?php if($projectwise){ ?><th colspan="6">TOTAL</th><?php } else { ?><th colspan="5">TOTAL</th><?php } ?>
                    <th style="text-align: right"><?= number_format($total_amount, 2) ?></th>
                </tr>
            </tfoot>
    <?php
        }
    ?>
</table>


