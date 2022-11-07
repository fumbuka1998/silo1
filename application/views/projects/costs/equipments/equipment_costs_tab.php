<div class="row">
    <div class="col-xs-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#owned_equipment_costs" data-toggle="tab">Owned Equipment costs</a></li>
                <li><a href="#hired_equipment_costs" data-toggle="tab">Hired Equipment Costs</a></li>
            </ul>
            <div class="tab-content">
                <div class="active tab-pane fade in" id="owned_equipment_costs">
                    <?php $this->load->view('projects/costs/equipments/owned_equipments/owned_equipment_cost_tab'); ?>
                </div>
                <div class="tab-pane fade" id="hired_equipment_costs">
                    <?php $this->load->view('projects/costs/equipments/hired_equipments/hired_equipment_cost_tab'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

