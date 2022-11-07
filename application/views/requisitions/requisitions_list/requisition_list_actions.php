<?php
if (check_privilege('Requisition Actions')) {
?>
    <span>
        <div class="btn-group">
            <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                Actions
            </button>
            <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li>
                    <a class="btn btn-block btn-xs" data-toggle="modal" style="text-align: left" data-target="#requisition_transaction_documents_<?= $requisition->{$requisition::DB_TABLE_PK} ?>">
                        <i class="fa fa-bookmark"></i> Documents
                    </a>
                </li>
                <?php if (check_privilege('Approval Chain')) { ?>
                    <li>
                        <a target="_blank" href="<?= base_url('requisitions/preview_requisition_approved_chains/' . $requisition->{$requisition::DB_TABLE_PK}) ?>">
                            <i class="fa fa-chain"></i> Approval Chain
                        </a>
                    </li>
                <?php } ?>
                <li>
                    <a data-toggle="modal" data-target="#requisition_attachments_<?= $requisition->{$requisition::DB_TABLE_PK} ?>" href="#">
                        <i class="fa fa-paperclip"></i> Attachments
                    </a>
                </li>

                <?php

                if ($requisition->status == 'PENDING' || $requisition->status == 'INCOMPLETE') {
                    $data['last_approval'] = $last_approval = $requisition->last_approval();
                    $data['current_approval_level'] = $current_approval_level = $requisition->current_approval_level();
                    $can_edit = ($requisition->requester_id == $this->session->userdata('employee_id') && !$data['last_approval']);

                    if ($can_edit) { ?>
                        <li>
                            <button type="button" style="margin-left: 1px; text-align: left; color: #777777; padding: 3px 20px;" modal-head="Requisition Form" button-link="<?= base_url('requisitions/edit_requisition_form/' . $requisition->requisition_id) ?>" title="Edit <?= $requisition->requisition_number() ?>" data-toggle="modal" id="edit_button_requisition_<?= $requisition->requisition_id ?>" data-target="#edit_requisition_<?= $requisition->requisition_id ?>" class="openRequisitionModal btn btn-block btn-default btn-xs"><i class="fa fa-edit"></i> Edit</button>
                        </li>
                        <li>
                            <a style="color: white" requisition_id="<?= $requisition->{$requisition::DB_TABLE_PK} ?>" class="btn  btn-block btn-xs btn-danger delete_requisition"><i class="fa fa-trash"></i> Delete
                            </a>
                        </li>
                    <?php
                    }

                    if ($can_approve) { ?>
                        <li>
                            <button type="button" style="margin-left: 1px; text-align: left; color: white; padding: 3px 20px;" modal-head="Requisition Approval Form" button-link="<?= base_url('requisitions/approve_requisition_form/' . $requisition->requisition_id) ?>" title="Approve <?= $requisition->requisition_number() ?>" data-toggle="modal" id="approve_button_requisition_<?= $requisition->requisition_id ?>" data-target="#approve_requisition_<?= $requisition->requisition_id ?>" class="openRequisitionModal btn btn-block btn-success btn-xs">
                                <i class="fa fa-check-square-o"></i> Act</button>
                        </li>
                <?php
                    }
                }
                ?>
            </ul>
        </div>
        <div id="requisition_attachments_<?= $requisition->{$requisition::DB_TABLE_PK} ?>" class="modal fade" tabindex="-1" role="dialog">
            <?php $this->load->view('requisitions/requisitions_list/requisition_attachments_modal'); ?>
        </div>
        <?php
        if ($requisition->status == 'PENDING' || $requisition->status == 'INCOMPLETE') {
            if ($can_edit) { ?>
                <div requisition_id="<?= $requisition->requisition_id ?>" id="edit_requisition_<?= $requisition->requisition_id ?>" class="modal preloaded_modal fade edit_requisition_form" role="dialog">
                    <div class="modal-dialog" style="width: 93%;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title"></h4>
                            </div>
                            <form autocomplete="off" class="form-horizontal" method="post" action="javascript:void(0)" enctype="multipart/form-data">
                                <div class="modal-body" style="padding: 15px">


                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php }
            if ($can_approve) { ?>
                <div requisition_id="<?= $requisition->requisition_id ?>" id="approve_requisition_<?= $requisition->requisition_id ?>" class="modal preloaded_modal fade approve_requisition_form" role="dialog">
                    <div class="modal-dialog" style="width: 93%;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title"></h4>
                            </div>
                            <form autocomplete="off" class="form-horizontal" method="post" action="javascript:void(0)" enctype="multipart/form-data">
                                <div class="modal-body" style="padding: 15px">


                                </div>
                            </form>
                        </div>
                    </div>
                </div>
        <?php
            }
        }
        ?>
        <div id="requisition_transaction_documents_<?= $requisition->{$requisition::DB_TABLE_PK} ?>" class="modal fade " role="dialog">
            <?php $this->load->view('requisitions/requisitions_list/approved_requisition_transactions_document_modal'); ?>
        </div>
    </span>
<?php } ?>
<script>
    function initialize_form_amount_calculator(form, amount_decimal_places) {
        var amount_decimal_places = typeof amount_decimal_places !== 'undefined' ? amount_decimal_places : '';
        form.delegate(' input[name="rate"],  input[name="quantity"]', 'change keyup', function() {
            var rate = form.find(' input[name="rate"]').unmask();
            var quantity = form.find(' input[name="quantity"]').val();
            var amount = parseFloat(rate) * parseFloat(quantity);
            form.find(' input[name="amount"]').val(amount).priceFormat();
        });
    }

    function calculate_table_total_amount(table) {
        var total_amount = 0;
        table.find('tbody input[name="amount"]').each(function() {
            $(this).priceFormat();
            var amount = $(this).val();
            amount = amount != '' ? parseFloat($(this).unmask()) : 0;
            total_amount += amount;
        });
        table.find('.total_amount_display').html((total_amount).toFixed(2)).priceFormat();
    }

    function initialize_requisition_form(modal) {

        var items_table = modal.find('table');
        var vat_inclusive = modal.find('input[name="vat_inclusive"]');
        var vat_percentage_selector = modal.find('select[name="vat_percentage"]');

        var calculate_vat_amount = function() {
            var total_table_amount = parseFloat(items_table.find('.total_amount_display').unmask());
            var freight = parseFloat(modal.find('input[name="freight"]').unmask());
            var other_charges = parseFloat(modal.find('input[name="inspection_and_other_charges"]').unmask());
            total_table_amount = !isNaN(total_table_amount) ? total_table_amount : 0;
            freight = !isNaN(freight) ? freight : 0;
            other_charges = !isNaN(other_charges) ? other_charges : 0;
            var overall_total_amount = (total_table_amount + freight + other_charges);
            var vat_percentage = vat_percentage_selector.val();
            var vat_amount = (parseFloat(vat_percentage) / 100) * (overall_total_amount-other_charges);
            var grand_total_amount = parseFloat(overall_total_amount) + parseFloat(vat_amount);
            items_table.find('input[name="vat"]').val((vat_amount).toFixed(2)).priceFormat();
            modal.find('.vat_amount_display').val((vat_amount).toFixed(2)).priceFormat();
            items_table.find('.grand_total_display').html((grand_total_amount).toFixed(2)).priceFormat();
        };

        var check_store_available_stock = function(source_id_selector) {
            var row = source_id_selector.closest('tr');
            var modal = row.closest('.modal');
            if (row.attr('initialized') != 'true') {
                row.delegate(' select[name="source_id"], select[name="material_id"],select[name="asset_item_id"],  input[name="quantity"] ', 'change keyup', function() {
                    var approval_module_id = modal.find('select[name="approval_module_id"]').val();
                    var source_type = row.find('select[name="source_type"]').val().trim();
                    var source_id = row.find('select[name="source_id"]').val().trim();
                    var item_type = row.find('select[name="asset_item_id"]').length ? 'asset' : 'material';
                    var item_id = item_type == 'material' ? row.find('select[name="material_id"]').val() : row.find('select[name="asset_item_id"]').val();
                    var quantity_field = row.find('input[name="quantity"]');
                    var project_id = modal.find('select[name="requisition_cost_center_id"]').val();

                    var validate_typed_quantity = function() {
                        var available_quantity = parseFloat(quantity_field.attr('available_quantity'));
                        var typed_quantity = parseFloat(quantity_field.val());
                        if (available_quantity < typed_quantity) {
                            toast('error', 'Only ' + available_quantity + ' is available at the selected store');
                            quantity_field.val(available_quantity);
                        }
                    };

                    if (source_type == 'store' && source_id != '' && item_type == 'material' && item_id.trim() != '') {
                        var send_request = $(this).is('select');
                        if (send_request) {
                            start_spinner();
                            $.post(
                                base_url + "inventory/check_store_available_material_quantity", {
                                    material_id: item_id,
                                    location_id: source_id,
                                    approval_module_id: approval_module_id,
                                    project_id: project_id
                                },
                                function(data) {
                                    toast('info', data + ' of them available at the selected store', 'Info:');
                                    quantity_field.attr('available_quantity', data);
                                    validate_typed_quantity();
                                    stop_spinner();
                                })
                        } else {
                            validate_typed_quantity();
                        }
                    } else if (source_type == 'store' && source_id != '' && item_type == 'asset' && item_id.trim() != '') {
                        var send_request = $(this).is('select');
                        if (send_request) {
                            start_spinner();
                            $.post(
                                base_url + "assets/check_store_available_asset_item_quantity", {
                                    asset_item_id: item_id,
                                    location_id: source_id,
                                    approval_module_id: approval_module_id,
                                    project_id: project_id
                                },
                                function(data) {
                                    toast('info', data + ' of them available at the selected store', 'Info:');
                                    quantity_field.attr('available_quantity', data);
                                    validate_typed_quantity();
                                    stop_spinner();
                                })
                        } else {
                            validate_typed_quantity();
                        }
                    }
                });
                row.attr('initialized', 'true');
            }
        };

        var load_level_to_approve_requisition = function(approval_module_id, container) {
            var foward_to_options = container.find('.foward_to_options');
            start_spinner();
            $.post(
                base_url + "requisitions/load_level_to_approve_requisition", {
                    approval_module_id: approval_module_id
                },
                function(data) {
                    foward_to_options.html(data);
                    stop_spinner();
                }
            )

        };

        vat_inclusive.change(function() {
            var total_table_amount = parseFloat(items_table.find('.total_amount_display').unmask());
            var freight = parseFloat(modal.find('input[name="freight"]').unmask());
            var other_charges = parseFloat(modal.find('input[name="inspection_and_other_charges"]').unmask());
            total_table_amount = !isNaN(total_table_amount) ? total_table_amount : 0;
            freight = !isNaN(freight) ? freight : 0;
            other_charges = !isNaN(other_charges) ? other_charges : 0;
            var overall_total_amount = (total_table_amount + freight + other_charges);
            var vat_percentage_selector = modal.find('select[name="vat_percentage"]');
            var vat_percentage_selector_form_group = vat_percentage_selector.closest('.form-group');
            var vat_inclusive = $(this);
            if (vat_inclusive.is(':checked')) {
                vat_percentage_selector_form_group.show();
                calculate_vat_amount();
            } else {
                vat_percentage_selector_form_group.hide();
                vat_percentage_selector.val(0).trigger('change');
                items_table.find('input[name="vat"]').val((0).toFixed(2));
                items_table.find('.grand_total_display').html((overall_total_amount).toFixed(2)).priceFormat();
            }
        });

        modal.find('tbody select[name="material_id"]').each(function() {
            var select_element = $(this);
            load_material_last_approved_price(select_element);
            select_element.select2({
                width: '300px'
            });
        });

        modal.find('select[name="approval_module_id"]').each(function() {
            var requisition_type_field = $(this);
            if (requisition_type_field.attr('initialized') != 'true') {
                var requisition_cost_center_field = requisition_type_field.closest('.modal-body').find('select[name="requisition_cost_center_id"]');
                var general_options = modal.find('select[name="material_id"]:first ').html();

                requisition_type_field.change(function() {
                    var module_id = requisition_type_field.val();
                    var foward_to_options = modal.find('.foward_to_options');

                    if (module_id == '2') {
                        load_project_dropdown_options(requisition_cost_center_field);
                        modal.find('.cost_center_form_group').show();
                        load_level_to_approve_requisition(module_id, modal);
                    } else if (module_id == '1') {
                        load_general_cost_centers_options(requisition_cost_center_field);
                        modal.find('.cost_center_form_group').hide();
                        modal.find('select[name="material_id"]').html(general_options).trigger('change');
                        load_level_to_approve_requisition(module_id, modal);
                    } else {
                        requisition_cost_center_field.html('');
                        foward_to_options.val('').trigger('change');
                    }
                });
                requisition_type_field.attr('initialized', 'true');
            }
        });

        function initialize_source_type(container) {
            container.find('select[name="source_type"]').each(function() {
                var type_selector = $(this);
                if (type_selector.attr('initialized') != 'true') {
                    type_selector.change(function() {

                        var source_id_selector = type_selector.closest('tr').find('select[name="source_id"]');
                        var payee_input_div = type_selector.closest('tr').find('.payee_input_div');
                        var source_selector = type_selector.closest('tr').find('.source_selector');
                        var vendor_options = modal.find('select[name="stakeholder_selector_template"]').html();
                        var main_location_options = modal.find('select[name="main_location_selector_template"]').html();
                        var account_options = modal.find('select[name="account_selector_template"]').html();
                        var selected_source = type_selector.val();

                        if (selected_source == 'vendor') {
                            payee_input_div.hide();
                            source_selector.show();
                            source_id_selector.html(vendor_options).val('');
                        } else if (selected_source == 'store') {
                            payee_input_div.hide();
                            source_selector.show();
                            source_id_selector.html(main_location_options).val('');
                            source_id_selector.change(function() {
                                if (source_id_selector.val().trim() != '') {
                                    check_store_available_stock(source_id_selector);
                                }
                            });
                        } else if (selected_source == 'cash') {
                            payee_input_div.show();
                            source_selector.hide();
                        }

                        source_id_selector.select2('val', '');
                        type_selector.attr('initialized', 'true');
                    });
                }
            });
        };

        var row = modal.find('tbody tr');
        initialize_source_type(row);

        var locations_with_particular_item = function(material_selector, container) {
            var tbody = container.closest('tbody');
            var modal = tbody.closest('.modal');

            var item_type = container.find('select[name="asset_item_id"]').length ? 'asset' : 'material';
            var item_id = item_type == 'material' ? container.find('select[name="material_id"]').val() : container.find('select[name="asset_item_id"]').val();

            if (item_id != '') {
                start_spinner();
                $.post(
                    base_url + "inventory/locations_with_particular_item", {
                        item_id: item_id,
                        item_type: item_type

                    },
                    function(data) {
                        tbody.find('.item_display_availability_row').remove();
                        var temporary_row = container.closest('table').find('.row_display_item_availability').clone().removeAttr('style').removeClass('row_display_item_availability').addClass('item_display_availability_row');
                        temporary_row.find('.item_display').html(data.table_view);
                        container.after(temporary_row);
                        stop_spinner();
                    }, 'json'
                );
            }
        };

        modal.find('tbody select[name="source_id"]').each(function() {
            $(this).select2({
                width: '200px'
            });
        });

        modal.find('tbody tr').each(function() {
            initialize_form_amount_calculator($(this));
            $(this).find('.row_remover').click(function() {
                var table = $(this).closest('table');
                $(this).closest('tr').remove();
                calculate_table_total_amount(table);
                calculate_vat_amount();
            });
        });

        modal.find('tbody select[name="material_id"]').each(function() {
            var select_element = $(this);
            select_element.select2({
                width: '300px'
            });
            select_element.change(function() {
                locations_with_particular_item(select_element, select_element.closest('tr'));
                load_material_unit(select_element, 'tr');
            });
        });

        modal.find('input, select, textarea').each(function() {
            $(this).attr('style', 'min-width :100px !important');
        });

        modal.find('.material_row_adder').each(function() {
            var button = $(this);
            if (button.attr('initialized') != 'true') {
                button.click(function() {
                    var tbody = $(this).closest('.row').find('tbody');
                    var new_row = tbody.closest('table').find('.material_row_template').clone().removeAttr('style').removeClass('material_row_template').addClass('artificial_row').appendTo(tbody);

                    tbody.find('select[name="material_id"]').each(function() {
                        var select_element = $(this);

                        load_material_last_approved_price(select_element);

                    });

                    new_row.find('select[name="material_id"]').each(function() {
                        var select_element = $(this);

                        select_element.select2({
                            width: '300px'
                        });

                        select_element.change(function() {
                            load_material_unit(select_element, 'tr');
                        });
                    });

                    if (modal.hasClass('edit_requisition_form')) {
                        new_row.find('select[name="source_id"]').each(function() {
                            var select_element = $(this);

                            select_element.select2({
                                width: '200px'
                            });

                        });

                        new_row.find('select[name="material_id"]').each(function() {
                            var select_element = $(this);

                            select_element.change(function() {
                                locations_with_particular_item($(this), new_row);
                            });
                        });
                        initialize_source_type(new_row);
                    }

                    new_row.find('.row_remover').click(function() {
                        var table = $(this).closest('table');
                        $(this).closest('tr').remove();
                        calculate_table_total_amount(table);
                        calculate_vat_amount();
                    });
                    initialize_form_amount_calculator(new_row);
                    initialize_common_js();
                    new_row.find('.number_format').priceFormat();

                });
                $(this).attr('initialized', 'true');
            }
        });

        modal.find('.asset_row_adder').each(function() {
            var button = $(this);
            if (button.attr('initialized') != 'true') {
                button.click(function() {
                    var tbody = $(this).closest('.row').find('tbody');
                    var new_row = tbody.closest('table').find('.asset_row_template').clone().removeAttr('style').removeClass('asset_row_template').addClass('artificial_row').appendTo(tbody);

                    new_row.find('select[name="asset_item_id"]').each(function() {
                        var select_element = $(this);
                        select_element.select2({
                            width: '300px'
                        });
                    });

                    if (modal.hasClass('edit_requisition_form')) {
                        new_row.find('select[name="source_id"]').each(function() {
                            var select_element = $(this);

                            select_element.select2({
                                width: '200px'
                            });

                        });

                        new_row.find('select[name="asset_item_id"]').each(function() {
                            var select_element = $(this);

                            select_element.change(function() {
                                locations_with_particular_item(select_element, new_row);
                            });
                        });

                        initialize_source_type(new_row);
                    }

                    new_row.find('.row_remover').click(function() {
                        var table = $(this).closest('table');
                        $(this).closest('tr').remove();
                        calculate_table_total_amount(table);
                        calculate_vat_amount();
                    });
                    initialize_form_amount_calculator(new_row);
                    initialize_common_js();
                    new_row.find('.number_format').priceFormat();
                });
                $(this).attr('initialized', 'true');
            }
        });

        modal.find('.cash_row_adder').each(function() {
            if ($(this).attr('initialized') != 'true') {
                $(this).click(function() {
                    var tbody = $(this).closest('.row').find('tbody');
                    var new_row = tbody.closest('table').find('.cash_row_template').clone().removeAttr('style')
                        .removeClass('cash_row_template').addClass('artificial_row').appendTo(tbody);

                    new_row.find('select').addClass('searchable');
                    new_row.find('.row_remover').click(function() {
                        var table = $(this).closest('table');
                        $(this).closest('tr').remove();
                        calculate_table_total_amount(table);
                        calculate_vat_amount();
                    });
                    initialize_form_amount_calculator(new_row);
                    initialize_common_js();
                    new_row.find('.number_format').priceFormat();

                });
                $(this).attr('initialized', 'true');
            }
        });

        modal.find('.service_row_adder').each(function() {
            if ($(this).attr('initialized') != 'true') {
                $(this).click(function() {
                    var tbody = $(this).closest('.row').find('tbody');
                    var new_row = tbody.closest('table').find('.service_row_template').clone().removeAttr('style')
                        .removeClass('service_row_template').addClass('artificial_row').appendTo(tbody);

                    new_row.find('select').select2();

                    new_row.find('.row_remover').click(function() {
                        var table = $(this).closest('table');
                        $(this).closest('tr').remove();
                        calculate_table_total_amount(table);
                        calculate_vat_amount();
                    });

                    new_row.find('select[name="source_id"]').each(function() {
                        var select_element = $(this);
                        select_element.select2({
                            width: '170px'
                        });
                    });

                    if (modal.hasClass('edit_requisition_form')) {
                        initialize_source_type(new_row);
                    }
                    initialize_form_amount_calculator(new_row);
                    initialize_common_js();
                    new_row.find('.number_format').priceFormat();

                });
                $(this).attr('initialized', 'true');
            }
        });

        modal.find('.number_format').priceFormat();

        modal.delegate(' input[name="rate"],  input[name="quantity"], input[name="freight"], input[name="inspection_and_other_charges"], input[name="vat_inclusive"], select[name="vat_percentage"] ', 'change keyup', function() {
            calculate_table_total_amount(items_table);
            calculate_vat_amount();
        });
    }

    function initialize_requisition_approval_forms(modal) {
        var material_sources_row_template = modal.find('.material_source_row_template');
        var cash_sources_row_template = modal.find('.cash_source_row_template');
        var items_table = modal.find('table');
        var vat_inclusive = modal.find('input[name="vat_inclusive"]');
        var vat_percentage_selector = modal.find('select[name="vat_percentage"]');

        var get_table_total_amount = function() {
            var total_amount = 0;
            items_table.find('tbody input[name="amount"]').each(function() {
                $(this).priceFormat();
                var amount = $(this).val();
                amount = amount != '' ? parseFloat($(this).unmask()) : 0;
                total_amount += amount;
            });
            return total_amount;
        };

        var calculate_vat_amount = function() {
            var total_table_amount = get_table_total_amount();
            var freight = parseFloat(modal.find('input[name="freight"]').unmask());
            var other_charges = parseFloat(modal.find('input[name="inspection_and_other_charges"]').unmask());
            total_table_amount = !isNaN(total_table_amount) ? total_table_amount : 0;
            freight = !isNaN(freight) ? freight : 0;
            other_charges = !isNaN(other_charges) ? other_charges : 0;
            var overall_total_amount = (total_table_amount + freight + other_charges);
            var vat_percentage = vat_percentage_selector.val();
            var vat_amount = (parseFloat(vat_percentage) / 100) * parseFloat((overall_total_amount-other_charges));
            var grand_total_amount = parseFloat(overall_total_amount) + parseFloat(vat_amount);
            items_table.find('input[name="vat"]').val((vat_amount).toFixed(2)).priceFormat();
            modal.find('.vat_amount_display').val((vat_amount).toFixed(2)).priceFormat();
            items_table.find('.grand_total_display').html((grand_total_amount).toFixed(2)).priceFormat();
        }

        vat_inclusive.change(function() {
            var total_table_amount = get_table_total_amount();
            var freight = parseFloat(modal.find('input[name="freight"]').unmask());
            var other_charges = parseFloat(modal.find('input[name="inspection_and_other_charges"]').unmask());
            total_table_amount = !isNaN(total_table_amount) ? total_table_amount : 0;
            freight = !isNaN(freight) ? freight : 0;
            other_charges = !isNaN(other_charges) ? other_charges : 0;
            var overall_total_amount = (total_table_amount + freight + other_charges);
            var vat_percentage_selector = modal.find('select[name="vat_percentage"]');
            var vat_percentage_selector_form_group = vat_percentage_selector.closest('.form-group');
            var vat_inclusive = $(this);
            if (vat_inclusive.is(':checked')) {
                vat_percentage_selector_form_group.show();
                calculate_vat_amount();
            } else {
                vat_percentage_selector_form_group.hide();
                vat_percentage_selector.val(0).trigger('change');
                items_table.find('input[name="vat"]').val((0).toFixed(2));
                items_table.find('.grand_total_display').html((overall_total_amount).toFixed(2)).priceFormat();
            }
        });

        modal.find('.sources_table').each(function() {
            var sources_table = $(this);

            sources_table.find('tr').each(function() {
                initialize_form_amount_calculator($(this), 3);
            });

            var initialize_source_change = function() {
                var cashbook_options = modal.find('.sources_options_templates select[name="cashbook_options"]').html();
                var vendor_options = modal.closest('table').find('.sources_options_templates select[name="vendor_options"]').html();
                var main_location_options = modal.closest('table').find('.sources_options_templates select[name="main_location_options"]').html();
                sources_table.find('select[name="source_type"]').each(function() {
                    if ($(this).attr('initialized') != 'true') {
                        $(this).change(function() {
                            var source_selector = $(this).closest('tr').find('select[name="source"]');
                            if ($(this).val() == 'cash') {
                                source_selector.html(cashbook_options).select2('val', '');
                                source_selector.attr('disabled', 'disabled');
                            } else if ($(this).val() == 'store') {
                                source_selector.html(main_location_options).select2('val', '');
                                source_selector.removeAttr('disabled');
                            } else {
                                source_selector.html(vendor_options).select2('val', '');
                                source_selector.removeAttr('disabled');
                            }
                        });
                        $(this).attr('initialized', 'true');
                    }
                });
            };

            initialize_source_change();


            sources_table.find(' .material_source_adder').each(function() {
                var button = $(this);
                if (button.attr('initialized') != 'true') {
                    var tbody = button.closest('table').find('tbody');
                    button.click(function() {
                        var new_row = material_sources_row_template.clone().removeAttr('style').removeClass('material_source_row_template').addClass('artificial_row').prependTo(tbody);
                        new_row.find('select[name="source"]').select2();
                        new_row.find('.number_format').priceFormat();
                        initialize_form_amount_calculator(new_row, 3);
                        new_row.find('.row_remover').click(function() {
                            $(this).closest('tr').remove();
                            calculate_vat_amount();
                        });
                        initialize_source_change();
                    });
                    button.attr('initialized', 'true');
                }
            });

            sources_table.find(' .cash_source_adder').each(function() {
                var button = $(this);
                if (button.attr('initialized') != 'true') {
                    var tbody = button.closest('table').find('tbody');
                    button.click(function() {
                        var new_row = cash_sources_row_template.clone().removeAttr('style').removeClass('cash_source_row_template').addClass('artificial_row').prependTo(tbody);
                        new_row.find('select[name="source"]').select2();
                        new_row.find('.number_format').priceFormat();
                        initialize_form_amount_calculator(new_row, 3);
                        new_row.find('.row_remover').click(function() {
                            $(this).closest('tr').remove();
                            calculate_vat_amount();
                        });
                        initialize_source_change();
                    });
                    button.attr('initialized', 'true');
                }
            });
        });

        modal.find('input[name="set_final"]').change(function() {
            var set_final_checkbox = $(this);
            if (set_final_checkbox.is(':checked')) {
                modal.find('select[name="forward_to"]').attr('disabled', true);
            } else {
                modal.find('select[name="forward_to"]').selectedIndex = -1;
                modal.find('select[name="forward_to"]').attr('disabled', false);
            }

        });

        modal.delegate('  ' +
            '.major_table_tbody input[name="rate"], .major_table_tbody input[name="quantity"],' +
            ' input[name="freight"], input[name="inspection_and_other_charges"], input[name="vat_inclusive"],' +
            ' select[name="vat_percentage"]  ', 'change keyup', function() {
            calculate_vat_amount();
        });
    }

    $(document).ready(function() {
        $(".openRequisitionModal").off('click').on("click", function() {
            start_spinner();
            let modal = $(this).closest('span').find('.preloaded_modal');
            modal.find(".modal-title").html($(this).attr("modal-head"));
            modal.find(".modal-body").load($(this).attr("button-link"), function() {
                console.log(modal);
                initialize_common_js();
                if (modal.hasClass('edit_requisition_form')) {
                    initialize_requisition_form($('.edit_requisition_form'));
                } else {
                    initialize_requisition_approval_forms($('.approve_requisition_form'));
                }
                stop_spinner();
            });
        });
    });
</script>