<?php
/**
 * Created by PhpStorm.
 * User: bizytechlimited
 * Date: 12/10/2018
 * Time: 16:08
 */
 
?>
<div style="height: 120px !important; overflow-y: scroll; background-color: rgba(77,193,252,0.71)">
    <p>
        <h4  style="text-align: center; color: white">In-Store Existence</h4>
    </p>

        <?php
        $table = '<table class="table table-bordered table-hover" style=" background-color: rgba(76,191,249,0.55); color: white">
                <thead>
                <tr>
                    <th>Store</th><th>Quantity</th>
                </tr>';
        $overall_quantity = 0;
        foreach($locations_options as $location){
            $project_materials = $location->total_material_item_quantity('all', $material_item);
//            $unassigned_materials = $location->total_material_item_quantity(null, $material_item);
//            $material_quantity = $project_materials + $unassigned_materials;
            $material_quantity = $project_materials;

            if($material_quantity > 0) {
                $table .= '<tr>
                            <td>' . $location->location_name . '</td><td>' . $material_quantity . '</td>
                         </tr>';
                $overall_quantity += $material_quantity;
            }
        }

        if($curl_response && $curl_response->available_quantity > 0){
            $table .= '<tr style="background-color: #0000FF">
                            <td>' . $curl_response->location_name . '</td><td>' . $curl_response->available_quantity . '</td>
                         </tr>';
            $overall_quantity += $curl_response->available_quantity;
        }

        $table .= '</thead>
                <tfoot>
                     <td style="text-align: left"><strong>TOTAL<strong></td><td><strong>'.$overall_quantity.'<strong></td> 
                </tfoot>
                </table>';

        if($overall_quantity > 0) {
            echo $table;
        } else { ?>
            <div style="text-align: center" class="alert alert-info col-xs-12">
                Item Unavailable
            </div>
        <?php
        }
        ?>
    </table>
</div>