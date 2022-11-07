<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 15/05/2018
 * Time: 12:04
 */

?>

<table
    <?php if($print){
    ?> width="100%" border="1" cellspacing="0"
    style="font-size: 11px"
    <?php
} else {
    ?>
    class="table table-bordered table-hover"
    <?php
} ?>>
    <thead>
        <tr>
                <th>PROJECT</th>
            <?php
                $grand_total = 0;
                foreach ($sub_locations as $sub_location){
                    if($column_totals[$sub_location->{$sub_location::DB_TABLE_PK}] > 0) {
                        ?>
                        <th><?= $sub_location->sub_location_name ?></th>
                        <?php
                    }
                }
            ?>

            <th>TOTAL</th>
        </tr>
    </thead>
    <tbody>
        <?php if(!$project_selected && $row_totals['unassigned'] > 0){ ?>
        <tr>
            <td>UNASSIGNED</td>
            <?php

                $grand_total += $row_totals['unassigned'];
                foreach ($sub_locations as $sub_location) {
                    if ($column_totals[$sub_location->{$sub_location::DB_TABLE_PK}] > 0) {
                        ?>
                        <td style="text-align: right"><?= $quantities['unassigned'][$sub_location->{$sub_location::DB_TABLE_PK}] ?></td>
                        <?php
                    }
                }

            ?>
            <th style="text-align: right"><?= $row_totals['unassigned'] ?></th>
        </tr>
        <?php } ?>
        <?php foreach ($projects as $project) {
            if ($row_totals[$project->{$project::DB_TABLE_PK}] > 0) {
                $grand_total += $row_totals[$project->{$project::DB_TABLE_PK}]
                ?>
                <tr>
                    <td><?= $project->project_name ?></td>
                    <?php
                    foreach ($sub_locations as $sub_location) {
                        if($column_totals[$sub_location->{$sub_location::DB_TABLE_PK}] > 0) {
                            ?>
                            <td style="text-align: right"><?= $quantities[$project->{$project::DB_TABLE_PK}][$sub_location->{$sub_location::DB_TABLE_PK}] ?></td>
                            <?php
                        }
                    }
                    ?>
                    <th style="text-align: right"><?= $row_totals[$project->{$project::DB_TABLE_PK}] ?></th>
                </tr>
            <?php }
        }?>
    <tr>
        <th>TOTAL</th>
        <?php
        foreach ($sub_locations as $sub_location){
            if($column_totals[$sub_location->{$sub_location::DB_TABLE_PK}] > 0) {
                ?>
                <th style="text-align: right"><?= $column_totals[$sub_location->{$sub_location::DB_TABLE_PK}] ?></th>
                <?php
            }
        }
        ?>
        <th style="text-align: right"><?= $grand_total ?></th>
    </tr>

    </tbody>
</table>