<?php
?>
<a data-toggle="modal" data-target="#certificates_details_pop_up_<?= $vendor_with_order->{$vendor_with_order::DB_TABLE_PK}.'_'.$currency->{$currency::DB_TABLE_PK} ?>" style="cursor: pointer">
    <?= $currency->symbol.' '.number_format($vendors_table_items[$vendor_with_order->stakeholder_name][$currency->symbol]['balance_per_currency'],2) ?>
</a>
<div id="certificates_details_pop_up_<?= $vendor_with_order->{$vendor_with_order::DB_TABLE_PK}.'_'.$currency->{$currency::DB_TABLE_PK} ?>" class="modal fade" role="dialog">
    <div style="width: 80%" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= wordwrap($vendor_with_order->stakeholder_name.' LIABILITIES(Confirmed Orders) IN '.strtoupper($currency->currency_name),100,'<br/>') ?></h4>
            </div>
            <form>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 table_container"  style="max-height: 420px; overflow-y: scroll">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <?php
                                        foreach($projects as $project) {
                                            ?>
                                            <th style="text-align: center;"><?= wordwrap($project->project_name,15,'<br/>') ?></th>
                                            <?php
                                        } ?>
                                    <th style="text-align: center;">TOTAL</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    <?php
                                        foreach ($projects as $project) {
                                            ?>
                                            <td style="text-align: right;"><?= $currency->symbol.' '.number_format($vendors_table_items[$vendor_with_order->stakeholder_name][$currency->symbol][$project->project_name],2)  ?></td>
                                            <?php
                                        }
                                    ?>
                                        <td  style="text-align: right;"><?= $currency->symbol.' '.number_format($vendors_table_items[$vendor_with_order->stakeholder_name][$currency->symbol]['balance_per_currency'],2) ?></td>
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
