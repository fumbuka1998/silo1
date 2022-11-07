<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 11/25/2016
 * Time: 1:28 PM
 */
?>
<div class="row">
    <div class="col-xs-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#account_statement" data-toggle="tab">Statement</a></li>
                <?php
                $has_payment_vouchers = $account_group_name == 'BANK' || $account_group_name == 'CASH IN HAND';

                $has_contras = $account_group_name == 'BANK' || $account_group_name == 'CASH IN HAND';

                if($has_payment_vouchers){
                    ?>
                    <li><a href="#payment_vouchers" data-toggle="tab">Payment Vouchers</a></li>
                <?php }

                if($account_group_name == 'ACCOUNT RECEIVABLE'){
                    ?>
                    <li><a href="#receipts" data-toggle="tab">Receipts</a></li>
                    <?php
                }

                if($has_contras) {
                    ?>
                    <li><a href="#contras" data-toggle="tab">Contras</a></li>
                    <?php
                }?>

                 <!--<li><a href="#imprest_vouchers" data-toggle="tab">Imprest Vouchers</a></li>-->
            </ul>
            <div class="tab-content">
                <div class="active tab-pane" id="account_statement">
                    <?php $this->load->view('finance/account_profile/statement_tab'); ?>
                </div>

                <?php
                if($has_payment_vouchers) { ?>
                    <div class="tab-pane" id="payment_vouchers">
                        <?php $this->load->view('finance/account_profile/payment_vouchers_tab'); ?>
                    </div>
                    <?php
                }

                if($account_group_name == 'ACCOUNT RECEIVABLE'){
                    ?>
                    <div class="tab-pane" id="receipts">
                        <?php $this->load->view('finance/account_profile/receipts_tab'); ?>
                    </div>
                    <?php
                }

                if($has_contras) {
                    ?>
                    <div class="tab-pane" id="contras">
                        <?php $this->load->view('finance/account_profile/contras_tab'); ?>
                    </div>
                    <?php
                }?>

                <div class="tab-pane" id="imprest_vouchers">

                </div>

               <?php  if($account_group_name == 'CASH IN HAND' || $account_group_name == 'BANK'){?>
                    <div class="tab-pane" id="approved_cash">

                    </div>
                    <?php
                }

                ?>
            </div>
        </div>
    </div>
</div>
