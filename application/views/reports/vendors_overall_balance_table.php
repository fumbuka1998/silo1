<?php
/**
 * Created by PhpStorm.
 * User: bizytechlimited
 * Date: 22/10/2018
 * Time: 08:04
 */

    if(!empty($vendors)) {
    $invoies = 'true';
        ?>
        <table <?php if(isset($print)){
            ?>
            width="100%" border="1" cellspacing="0" style="font-size: 10px"
            <?php
        } else { ?> class="table table-bordered table-hover"
        <?php } ?>
        >
            <thead>
            <tr style="width: 400px; font-weight: bold; background-color: #dfdfdf">
                <td>Vendor Name</td><td style="text-align: right">Balance</td>
            </tr>
            <thead>
            <tbody>
            <?php
            $total_value = 0;
            foreach ($vendors as $vendor) {
                $balance = $vendor->overall_balance(null, $as_of);
                if($balance > 0){
                ?>
                    <tr>
                        <td><?= isset($print) ? $vendor->vendor_name : anchor(base_url('procurements/vendor_profile/' . $vendor->{$vendor::DB_TABLE_PK} .'/'. $invoies ),$vendor->vendor_name)  ?></td><td style="text-align: right">TSH <?= number_format($balance,2) ?></td>
                    </tr>
            <?php
                }

            }

            ?>


            </tbody>
        </table>

        <table <?php if(isset($print)){
            ?>
            width="100%" border="1" cellspacing="0" style="font-size: 10px"
            <?php
        } else { ?> class="table table-bordered table-hover"
        <?php } ?>>
            <thead>
                <tr style="background-color: #dfdfdf">
                    <th colspan="2">Exchange Rates</th>
                </tr>
                <?php
                    foreach ($currencies as $currency){
                        ?>
                        <tr>
                            <td><?= $currency->name_and_symbol() ?></td><td style="text-align: right"><?= number_format($currency->rate_to_native(),3) ?></td>
                        </tr>
                <?php
                    }
                ?>
            </thead>
        </table>
        <?php

    } else {
        echo 'No vendors found';
    }
?>