<?php
/**
 * Created by PhpStorm.
 * User: bizytechlimited
 * Date: 22/10/2018
 * Time: 13:16
 */
?>

<table
    <?php if($print){
    ?> width="100%" border="1" cellspacing="0"
    style="font-size: 10px"
    <?php
} else {
    ?>
    class="table table-bordered table-hover"
    <?php
} ?>>
    <thead>
    <tr>
        <th>Location Name</th><th>Quantity</th>
    </tr>
    </thead>
    <tbody>

    <?php
    $row = '';
    $overal_total_unassigned = $overal_total_project = $overall_total = 0;
     foreach($inventory_locations as $location){

         if($project_selected){
             $total_in_location = $location->total_material_item_quantity($project->{$project::DB_TABLE_PK}, $material_item, $to);
         } else {
             $total_in_location = $location->total_material_item_quantity('all', $material_item, $to);
         }
         $overall_total += $total_in_location;

         if($total_in_location > 0){
             $row .= '<tr>
                          <td>'. $location->location_name .'</td>
                          <td style="text-align: right">'. $total_in_location .'</td>
                      </tr>';
         }
     }
    echo $row;
    ?>
    </tbody>
    <tfoot>
        <tr>
            <td style="text-align: left">TOTAL</td>
            <td style="text-align: right"><?= $overall_total ?></td>
        </tr>
    </tfoot>
</table>