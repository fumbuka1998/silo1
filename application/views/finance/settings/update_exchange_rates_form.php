<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Update Exchange Rates</h4>
        </div>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-6">
                        <label for="date" class="control-label">Date</label>
                        <input type="text" class="form-control datepicker" required name="date" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="col-xs-12 table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Currency Name</th><th>Symbol</th><th>Rate to Native</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if(!empty($exchange_currencies)) {
                            foreach ($exchange_currencies as $currency) {
                                $exchange_rate = $currency->rate_to_native();
                                ?>
                                <tr>
                                    <td><?= $currency->currency_name ?></td>
                                    <td><?= $currency->symbol ?></td>
                                    <td>
                                        <input name="currency_id" type="hidden"
                                               value="<?= $currency->{$currency::DB_TABLE_PK} ?>">
                                        <input name="exchange_rate" class="form-control exchange_rate"
                                               value="<?= $exchange_rate ? $exchange_rate : '' ?>" placeholder=" <?= $exchange_rate ? '' : 'No updated exchange rates currently!' ?>">
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button id="save_exchange_rates" class="btn btn-sm btn-default">
                Update Rates
            </button>
        </div>
    </div>
</div>