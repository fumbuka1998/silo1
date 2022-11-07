<div class="row">
    <div class="col-xs-12">
         <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#permanent_labour_budgeting_tab" data-toggle="tab">Permanent Staff</a></li>
                <li><a href="#casual_labour_budgeting_tab" data-toggle="tab">Casual Labour</a></li>
            </ul>
            <div class="tab-content">
                <div class="active tab-pane fade in" id="permanent_labour_budgeting_tab">
                    <?php $this->load->view('budgets/labour/permanent_labour/permanent_labour_budgeting'); ?>
                </div>
                <div class="tab-pane fade" id="casual_labour_budgeting_tab">
                    <?php $this->load->view('budgets/labour/casual_labour/casual_labour_budgeting'); ?>
                </div>
        </div>
    </div>
</div>
</div>

