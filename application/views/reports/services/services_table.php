<?php
/**
 * Created by PhpStorm.
 * User: zeus
 * Date: 08/05/2019
 * Time: 13:33
 */

?>

<br/>
<table
    <?php if ($print) {
        ?> width="100%" border="1" cellspacing="0"

        <?php
    } else {
        ?>
        class="table table-bordered table-hover table-striped"
        <?php
    } ?> style="font-size: 12px" >

    <thead>
      <tr>
          <th style="text-align: center">Service Date</th>
          <th style="text-align: center">Service No:</th>
          <th style="text-align: center">Description</th>
          <th style="text-align: center">Client</th>
          <th style="text-align: center">Location</th>
          <th style="text-align: center">Status</th>
          <th style="text-align: center">Currency</th>
          <th style="text-align: center">Base Currency</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $total_native_cost = 0;
        foreach ($table_data as $item){
            $total_native_cost += $item['native_cost'];
            ?>
            <tr>
                <td nowrap="nowrap"><?= set_date($item['service_date']) ?></td>
                <td nowrap="nowrap">
                    <?php
                    if(!$print){
                    ?>
                    <form method="post" target="_blank" action="<?= base_url('projects/preview_services/'.$item['service_id']) ?>">
                        <button style="color: #458bb9; font-weight: bold" data-toggle="modal" data-target="#print<?= $item['service_id'] ?>" class="btn btn-basic btn-xs">
                            <?=
                            $item['service_number']
                            ?>
                        </button>
                    </form>
                    <?php
                    }else{
                        ?>
                        <?=
                        $item['service_number']
                        ?>
                        <?php
                    }
                    ?>
                </td>
                <td nowrap="nowrap"><?= $item['remarks'] ?></td>
                <td nowrap="nowrap"><?= $item['client_name'] ?></td>
                <td nowrap="nowrap"><?= $item['location'] ?></td>
                <td nowrap="nowrap">
                    <?php
                    if(!$print){

                        if($item['paid_invoice']){
                            ?>
                            <form method="post" target="_blank" action="<?= base_url('finance/preview_receipt/'.$item['paid_invoice']) ?>">
                                <button style="width: 55px" class="btn btn-success btn-xs">
                                    Paid
                                </button>
                            </form>
                            <?php
                        }else if($item['invoice_number'] && !$item['paid_invoice'] ){
                            ?>
                            <form method="post" target="_blank" action="<?= base_url('finance/preview_outgoing_invoice/'.$item['invoice_number']) ?>">
                                <button style="width: 55px" class="btn btn-yahoo btn-xs">
                                    Invoiced
                                </button>
                            </form>
                            <?php
                        }else{
                            ?>
                            <button style="width: 55px" class="btn btn-info btn-xs">
                                    Pending
                                </button>
                            <?php
                        }

                    }else{

                        if($item['paid_invoice']){
                           echo 'RC'.add_leading_zeros( $item['invoice_number']);
                        }else if($item['invoice_number'] && !$item['paid_invoice'] ){
                            echo 'INV/'.add_leading_zeros( $item['invoice_number']);
                        }else{
                             echo 'Pending';
                        }
                    }
                    ?></td>
                <td nowrap="nowrap" style="text-align: right"><?= $item['currency_symbol'].' '.number_format($item['cost'], 2) ?></td>
                <td nowrap="nowrap" style="text-align: right"><?= 'TSH '.number_format($item['native_cost'], 2) ?></td>
            </tr>
          <?php
      }
      ?>
    </tbody>
    <tfoot>
       <tr>
           <td nowrap="nowrap" style="font-weight: bold" colspan="7">TOTAL</td>
           <td nowrap="nowrap" style="font-weight: bold; text-align: right"><?= 'TSH '.number_format($total_native_cost,2) ?></td>
       </tr>
    </tfoot>
</table>
