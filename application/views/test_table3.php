<table border="1" cellspacing="0" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>GRN No</th><th>CPF</th><th>RDL</th><th>IMPORT DUTY</th><th>VAT</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ($order_grns as $order_grn){
                $grn = $order_grn->grn();
                $cif = $grn->fob() + $order_grn->freight+$order_grn->insurance+$order_grn->other_charges;
                /*$cpf = $cif*0.01*$order_grn->cpf*$order_grn->exchange_rate;
                $rdl = $cif*0.01*$order_grn->rdl*$order_grn->exchange_rate;
                $import_duty = $cif*0.01*$order_grn->import_duty*$order_grn->exchange_rate;*/
                ?>
                <!--<tr>
                    <td><?/*= $order_grn->goods_received_note_id */?></td>
                    <td style="text-align: right"><?/*= number_format($cpf,2) */?></td>
                    <td style="text-align: right"><?/*= number_format($rdl,2) */?></td>
                    <td style="text-align: right"><?/*= number_format($import_duty,2) */?></td>
                    <td style="text-align: right"><?/*= number_format(((($cif*$order_grn->exchange_rate)+$cpf+$rdl+$import_duty)*$order_grn->vat/100),2) */?></td>
                </tr>-->
                <tr>
                    <td><?= $order_grn->goods_received_note_id ?></td>
                    <td style="text-align: right"><?= number_format($order_grn->cpf,2) ?></td>
                    <td style="text-align: right"><?= number_format($order_grn->rdl,2) ?></td>
                    <td style="text-align: right"><?= number_format($order_grn->import_duty,2) ?></td>
                    <td style="text-align: right"><?= number_format($order_grn->vat,2) ?></td>
                </tr>
        <?php
            }
        ?>
    </tbody>
</table>