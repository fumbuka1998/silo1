<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 7/20/2018
 * Time: 1:20 PM
 */

?>

<table  <?php if($print){ ?> style="font-size: 11px" width="100%" border="1" cellspacing="0"  <?php } else { ?> class="table table-bordered table-hover" <?php } ?> >
    <thead>
    <tr>
        <th>SN</th><th>Asset Name</th><?php if($projectwise){?><th>Project(s)</th><?php } ?><th>Quantity</th><th style="width: 150px">Status</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $sn = 0;
    foreach ($table_items as $item){
        $sn++;
        ?>
        <tr>
            <td><?= $sn ?></td>
            <td><?= wordwrap($item['asset_name'],120,'<br/>') ?></td>
            <?php
            if($projectwise){ ?>
                <td>
                    <table <?php if($print){ ?> style="font-size: 11px" width="100%" border="1" cellspacing="0"  <?php } else { ?> class="table table-bordered table-hover" <?php } ?>>
                    <?php
                    foreach($item['projects'] as $project){
                        $project_id = !is_null($project->project_name) ? $project->{$project::DB_TABLE_PK} : null;
                        $quantity_available = $item['asset_item']->sub_location_available_stock($item['sub_location_ids'], $project_id,true, null, $item['asset_group_id']);
                        if($quantity_available > 0) {
                            ?>
                            <tr>
                                <td style="text-align: left"><?= !is_null($project->project_name) ? $project->project_name : "UNASSIGNED" ?></td>
                                <td style="text-align: right; width: 100px"><?= $quantity_available ?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </table>
                </td>
            <?php } ?>
            <td><?= $item['balance'] ?></td>
            <td><?= $item['status'] ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>