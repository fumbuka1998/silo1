<?php
/**
 * Created by PhpStorm.
 * User: stunna
 * Date: 20/11/2017
 * Time: 12:44
 */
?>

<table style="font-size: 12px" cellspacing="0" border="1">
    <thead>
        <tr>
            <th>Date</th><th>Item Name</th><th>Quantity</th><th>Rate</th><th>Average Prices</th><td>Material Costs</td>
        </tr>
    </thead>
    <tbody>
    <?php
        foreach ($material_stocks as $material_stock){
            $material_item = $material_stock->material_item();
            $material_costs = $material_stock->material_costs();
            ?>
            <tr>
                <td><?= $material_stock->date_received ?></td>
                <td><?= $material_item->item_name ?></td>
                <td><?= $material_stock->quantity ?></td>
                <td><?= number_format($material_stock->price,3) ?></td>
                <td>
                    <?php
                        $average_prices = $material_stock->average_prices();
                    ?>
                    <table width="100%">
                        <?php
                            foreach ($average_prices as $average_price){
                                /*if($average_price->average_price == $material_stock->price*2){
                                    $material_stock->price = $average_price->average_price;
                                    $material_stock->save();
                                }*/

                                /*if($average_price->average_price == $material_stock->price/2){
                                    $average_price->average_price = $material_stock->price;
                                    $average_price->save();
                                }*/

                                /*if($average_price->average_price <= $material_stock->price/2){
                                    $average_price->average_price = $material_stock->price;
                                    $average_price->save();
                                }*/

                                /*if($average_price->average_price < $material_stock->price){
                                    $average_price->average_price = $material_stock->price;
                                    $average_price->save();
                                }*/


                                ?>
                                <tr>
                                    <td><?= custom_standard_date($average_price->datetime_updated) ?></td>
                                    <td><?= number_format($average_price->average_price,3) ?></td>
                                </tr>
                        <?php
                            }
                        ?>
                    </table>
                </td>
                <td>
                    <table width="100%">
                        <tr>
                            <th>Rate</th><th>Quantity</th>
                        </tr>
                        <?php
                            foreach ($material_costs as $material_cost){
                                /*if($material_cost->rate == ($material_stock->price/2)){
                                    $material_cost->rate = $material_stock->price;
                                    $material_cost->save();
                                }*/
                                ?>
                                <tr>
                                    <td><?= custom_standard_date($material_cost->cost_date) ?></td>
                                    <td><?= number_format($material_cost->rate,3) ?></td>
                                    <td><?= $material_cost->quantity ?></td>
                                </tr>
                        <?php
                            }
                        ?>
                    </table>
                </td>
            </tr>
    <?php
        }
    ?>
    </tbody>
</table>
