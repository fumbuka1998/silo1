/***********************************************
 * COMMON FUNCTIONS
 ***********************************************/

var delay = (function(){
    //To be used for delaying some functions
    var timer = 0;
    return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
})();

function initialize_save_buttons(){

    $('.save_sub_location_button').each(function(){
        var button = $(this);
        if(button.attr('active') != 'true') {
            button.click(function(){
                save_sub_location(button);
            });
            button.attr('active','true');
        }
    });

    $('.save_tools_and_equipment_type_button').each(function(){
        var button = $(this);
        if(button.attr('active') != 'true') {
            button.click(function () {
                save_tools_and_equipment_type($(this));
            });
            button.attr('active', 'true');
        }
    });

    $('.save_material_opening_stock').each(function(){
        var button = $(this);
        if(button.attr('active') != 'true') {
            button.click(function () {
                save_material_opening_stock($(this));
            });
            button.attr('active', 'true');
        }
    });

    $('.save_external_material_transfer').each(function(){
        var button = $(this);
        if(button.attr('active') != 'true') {
            button.click(function () {
                save_external_material_transfer($(this));
            });
            button.attr('active', 'true');
        }
    });

    $('.receive_external_material_transfer').each(function(){
        var button = $(this);
        if(button.attr('active') != 'true') {
            button.click(function () {
                receive_external_material_transfer($(this));
            });
            button.attr('active', 'true');
        }
    });

    $('.save_internal_material_transfer').each(function(){
        var button = $(this);
        if(button.attr('active') != 'true') {
            button.click(function () {
                save_internal_material_transfer($(this));
            });
            button.attr('active', 'true');
        }
    });

    $('.save_requisition, .suspend_requisition').each(function(){
        var button = $(this);
        if(button.attr('active') != 'true') {
            button.click(function () {
                save_requisition($(this));
            });
            button.attr('active', 'true');
        }
    });

    $('.approve_requisition').each(function(){
        var button = $(this);
        if(button.attr('active') != 'true') {
            button.click(function () {
                approve_requisition($(this));
            });
            button.attr('active', 'true');
        }
    });

    $('.revert_requisition_btn').each(function(){
        var button = $(this);
        if(button.attr('active') != 'true') {
            button.click(function () {

                var modal= button.closest('.modal');
                var chain_levels = modal.find('.revert_form');

                chain_levels.show();

                button.hide();
            });

            button.attr('active', 'true');
        }
    });

    $('.save_purchase_order').each(function(){
        var button = $(this);
        if(button.attr('active') != 'true') {
            button.click(function () {
                save_purchase_order($(this));
            });
            button.attr('active', 'true');
        }
    });

    $('.save_pre_ordered_purchase_order').each(function(){
        var button = $(this);
        if(button.attr('active') != 'true') {
            button.click(function () {
                save_pre_ordered_purchase_order($(this));
            });
            button.attr('active', 'true');
        }
    });

    $('.receive_purchase_order').each(function(){
        var button = $(this);
        if(button.attr('active') != 'true') {
            button.click(function () {
                receive_purchase_order($(this));
            });
            button.attr('active', 'true');
        }
    });

    $('.save_task_progress_update').each(function(){
        var button = $(this);
        if(button.attr('active') != 'true') {
            button.click(function () {
                save_task_progress_update($(this));
            });
            button.attr('active', 'true');
        }
    });


    $('.save_project_miscellaneous_cost').each(function(){
        var button = $(this);
        if(button.attr('active') != 'true') {
            button.click(function () {
                save_project_miscellaneous_cost($(this));
            });
            button.attr('active', 'true');
        }
    });

    $('.delete_material_cost').each(function(){
        var button = $(this);
        if(button.attr('active') != 'true') {
            button.click(function () {
                delete_material_cost($(this));
            });
            button.attr('active', 'true');
        }
    });

    $('.delete_miscellaneous_cost').each(function(){
        var button = $(this);
        if(button.attr('active') != 'true') {
            button.click(function () {
                delete_miscellaneous_cost($(this));
            });
            button.attr('active', 'true');
        }
    });

    $('.upload_project_excel').each(function(){
        var button = $(this);
        if(button.attr('active') != 'true') {
            button.click(function () {
                upload_project_excel($(this));
            });
            button.attr('active', 'true');
        }
    });

    $('.save_bulk_material_cost').each(function(){
        var button = $(this);
        if(button.attr('active') != 'true') {
            button.click(function () {

                save_bulk_material_cost($(this));
            });
            button.attr('active', 'true');
        }
    });
}

function initialize_common_js(){

    $(function () {
        var viewer = ImageViewer();
        $('.gallery-items').click(function () {
            var imgSrc = this.src,
                highResolutionImage = $(this).data('high-res-img');

            viewer.show(imgSrc, highResolutionImage);
        });
    });

    $('.datepicker').datepicker({ format: 'yyyy-mm-dd'});

    $(' .datetime_picker').datetimepicker({
        todayBtn:  1
    });

    $('.searchable').select2({width:'100%'});
    $('.number_format').priceFormat();

    $('#activity_keyword').keyup(function() {
        var project = $(this).attr('project');
        project = project == 'true';
        delay(function(){
            load_project_activities(project);
        }, 500 );
    });

    $('#sub_location_keyword').keyup(function() {
        delay(function(){
            load_location_sub_locations();
        }, 500 );
    });

    initialize_save_buttons();

    $(' .dataTables_wrapper').removeClass(' form-inline');
    $('.table').css('width','100%');

    $('.specific_modal_hide').click(function(){
        $(this).closest('.modal').modal('hide');
    });

    $('.specific_modal_hide').each(function(){
        var inner_modal = $(this).closest('.modal .fade');

        var outer_modal = inner_modal.parent().parent().parent().closest('.modal');

        outer_modal.on('hidden.bs.modal', function (e) {
            inner_modal.modal('hide');
        });
    });

    $('.money').priceFormat();

}

var toast = function(type,message,title) {
    if(type == 'error'){
        title = typeof title !== 'undefined' ? title : 'Error';
        iziToast.error({
            title: title,
            message: message,
            position: 'topRight'
        });
    } else if(type == 'warning') {
        typeof title !== 'undefined' ? title : 'Caution';
        iziToast.warning({
            title: title,
            message: message,
            position: 'topRight'
        });
    } else if(type == 'success'){
        title = typeof title !== 'undefined' ? title : 'Success';
        iziToast.success({
            title: title,
            message: message,
            position: 'topRight'
        });
    } else if(type == 'info'){
        title = typeof title !== 'undefined' ? title : 'Hi';
        iziToast.info({
            title: title,
            message: message,
            position: 'topRight'
        });
    } else {
        iziToast.show({
            title: title,
            message: message,
            position: 'topRight'
        });
    }
};

var display_form_fields_error = function () {
    toast('error','Make sure all fields are correctly filled');
};

var successfully_saved_message = function () {

};

initialize_common_js();

$('.save_company_details').click(function(){
    var container_box = $(this).closest('.box');

    var company_name = container_box.find('input[name="company_name"]').val();
    var telephone = container_box.find('input[name="telephone"]').val();
    var address = container_box.find('textarea[name="address"]').val();
    var email = container_box.find('input[name="email"]').val();
    if(company_name != '' && email != '' && address != '') {
        start_spinner();
        var mobile = container_box.find('input[name="mobile"]').val();
        var fax = container_box.find('input[name="fax"]').val();
        var tin = container_box.find('input[name="tin"]').val();
        var vrn = container_box.find('input[name="vrn"]').val();
        var website = container_box.find('input[name="website"]').val();
        var tagline = container_box.find('textarea[name="tagline"]').val();
        var captured = container_box.find('input[name="company_logo"]')[0];
        var file = captured.files[0], form_data = false;

        if (window.FormData) {
            form_data = new FormData();
            if (form_data) {
                form_data.append("company_logo", file);
                form_data.append("company_name", company_name);
                form_data.append("telephone", telephone);
                form_data.append("address", address);
                form_data.append("mobile", mobile);
                form_data.append("fax", fax);
                form_data.append("email", email);
                form_data.append("tin", tin);
                form_data.append("vrn", vrn);
                form_data.append("vrn", vrn);
                form_data.append("tagline", tagline);

                $.ajax({
                    url: base_url + 'administrative_actions/save_company_details/',
                    type: "POST",
                    timeout: 250000,
                    cache: false,
                    data: form_data,
                    processData: false,
                    contentType: false,
                    complete: function () {
                        container_box.find('.box-header').html(
                            '<div class=" col-xs-12 alert bg-green-gradient alert-dismissable">' +
                            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+
                            'Your Company Details have saved' +
                            '</div>'
                        );
                        stop_spinner();
                    }
                });
            }
        }
    }
});

$('#audit_trail_report_generator').click(function(){

    var table_container = $('#audit_trail_report_container');
    table_container.html('<div class="alert alert-info">Generating Report..</div>');
    var form_container = $(this).closest('form');
    var action_type = form_container.find(' select[name="action_type"]').val();
    var project_id = form_container.find(' select[name="project_id"]').val();
    var from = form_container.find(' input[name="from"]').val();
    var to = form_container.find(' input[name="to"]').val();

    $.post(
        base_url + "administrative_actions/audit_trail_report",
        {
            action_type:action_type,
            project_id:project_id,
            from:from,
            to:to
        }, function (data) {
            table_container.html(data)
        }
    );


});

function initialize_form_amount_calculator(form,amount_decimal_places){
    var amount_decimal_places = typeof amount_decimal_places !== 'undefined' ? amount_decimal_places : '';
    form.delegate(' input[name="rate"],  input[name="quantity"]','change keyup',function() {
        var rate = form.find(' input[name="rate"]').unmask();
        var quantity = form.find(' input[name="quantity"]').val();
        var amount = parseFloat(rate) * parseFloat(quantity);
        form.find(' input[name="amount"]').val(amount).priceFormat();
    });
}

function calculate_table_total_amount(table){
    var total_amount = 0;
    table.find('tbody input[name="amount"]').each(function(){
        $(this).priceFormat();
        var amount = $(this).val();
        amount = amount != '' ? parseFloat($(this).unmask()) : 0;
        total_amount += amount;
    });
    table.find('.total_amount_display').html(total_amount).priceFormat();
}

/****************************************************
 * HUMAN RESOURCES
 ****************************************************/

$('#employees_list').DataTable({
    colReorder: true,
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "human_resources/employees_list/",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching employees found</div>",
        "emptyTable":     "<div class='alert alert-info'>No employees found</div>"
    }
});

function save_employee_credentials(){
    var user_account_details_tab = $('#user_account_details');

    var password = user_account_details_tab.find('  input[name="password"]').val();
    var confirm_password = user_account_details_tab.find('  input[name="confirm_password"]').val();
    if(password == confirm_password) {
        var username = user_account_details_tab.find('  input[name="username"]').val();
        var user_id = user_account_details_tab.find('  input[name="user_id"]').val();
        var employee_id = user_account_details_tab.find('  input[name="employee_id"]').val();
        var permissions_ids = Array();
        var active = user_account_details_tab.find('  input[name="active"]').is(":checked") ? 1 : 0;

        var i = 0;
        user_account_details_tab.find(' .employee_permissions').each(function () {
            if ($(this).is(":checked")) {
                permissions_ids[i] = $(this).val();
                i++;
            }
        });
        $.post(
            base_url + "human_resources/save_user/",
            {
                username: username,
                user_id: user_id,
                employee_id: employee_id,
                password: password,
                active: active,
                permission_ids: permissions_ids
            }, function (data) {
                user_account_details_tab.html(data);
            }
        ).complete();
    }
}

$('a[href="#job_positions"]').on('shown.bs.tab', function (e){
    $('#job_positions_list').DataTable().draw('page');
});

$('a[href="#casual_labour_types"]').on('shown.bs.tab', function (e){
    $('#casual_labour_type_list').each(function () {
        var table = $(this);
        if(table.attr('initialized') != 'true'){

            table.DataTable({
                colReorder: true,
                "processing": true,
                "serverSide": true,
                "ajax" : {
                    url: base_url + "human_resources/casual_labour_types_list/",
                    type: 'POST'
                },
                "columns": [
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": false}
                ],
                "language": {
                    "zeroRecords":     "<div class='alert alert-info'>No matching types found</div>",
                    "emptyTable":     "<div class='alert alert-info'>No types found</div>"
                },"drawCallback": function () {
                    //Save Category
                    $('.save_casual_labour_type_button').each(function(){
                        var button = $(this);
                        if(button.attr('active') != 'true') {
                            var modal = button.closest('.modal');
                            var type_id = modal.find('input[name="type_id"]').val();

                            button.click(function (){
                                var name = modal.find('input[name="name"]').val();

                                if(name != '') {
                                    modal.modal('hide');
                                    var description = modal.find('textarea[name="description"]').val();

                                    $.post(
                                        base_url + "human_resources/save_casual_labour_type",
                                        {
                                            type_id: type_id,
                                            name: name,
                                            description: description
                                        }, function () {
                                            modal.find('form')[0].reset();
                                            table.DataTable().draw('page');
                                        }
                                    );
                                }
                            });
                            button.attr('active','true');
                        }
                    });

                    //Delete Category
                    $('.delete_casual_labour_type').each(function () {
                        var button = $(this);
                        var type_id = button.attr('type_id');
                        if(button.attr('initialized') != 'true'){
                            button.click(function () {
                                if(confirm('Are you sure?')) {
                                    $.post(
                                        base_url + "human_resources/delete_casual_labour_type/",
                                        {
                                            type_id: type_id
                                        }
                                    ).complete(function () {
                                        table.DataTable().draw('page');
                                    });
                                }
                            });
                        }
                    });

                    initialize_common_js();
                }
            });

            table.attr('initialized','true');
        } else {
            table.DataTable().draw('page');
        }
    });
});

/***************************************************
 * CLIENTS
 ***************************************************/

$('#clients_list').DataTable({
    colReorder: true,
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "clients/",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching clients found</div>",
        "emptyTable":     "<div class='alert alert-info'>No clients found</div>"
    }
});

$('#clients_projects').each(function(){
    var client_id = $(this).attr('client_id');
    $(this).DataTable({
        colReorder: true,
        "processing": true,
        "serverSide": true,
        "ajax" : {
            url: base_url + "clients/projects_list/"+client_id,
            type: 'POST'
        },
        "columns": [
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": false}
        ],
        "language": {
            "zeroRecords":     "<div class='alert alert-info'>No matching projects found for this client</div>",
            "emptyTable":     "<div class='alert alert-info'>No projects found for this client</div>"
        }
    });
});

/***************************************************
 * PROJECTS
 ***************************************************/

$('#projects_list').each(function () {
    var table = $(this);
    var category_id = table.attr('category_id');
    table.DataTable({
        colReorder: true,
        "processing": true,
        "serverSide": true,
        "ajax" : {
            url: base_url + "projects/projects_list/"+category_id,
            type: 'POST'
        },
        "columns": [
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": false}
        ],
        "language": {
            "zeroRecords":     "<div class='alert alert-info'>No matching projects found</div>",
            "emptyTable":     "<div class='alert alert-info'>No projects found</div>"
        }
    });
});

$('a[href="#project_details"]').on('shown.bs.tab', function (e){
    start_spinner();
    var project_id = $(this).attr('project_id');
    $.post(
        base_url + "projects/project_summary",
        {
            project_id : project_id
        }, function (data) {
            $('#project_summary').html(data);
        }
    ).complete(function () {
        stop_spinner();
    });
});

$('a[href="#project_budgets"]').on('shown.bs.tab', function (e){
    var controller = base_url + "budgets/budget_items_list/";
    var initialize_budget_datatable = function (budget_type,table_columns,additional_call_back_function) {
        var table_class = $('.'+budget_type+'_budget_items');
        table_class.each(function () {
            if($(this).attr('initialized') != 'true') {
                var table = $(this);
                var container_box = table.closest('.box');
                var cost_center_selector = container_box.find('select[name="cost_center_selector"]');
                var cost_center_id = cost_center_selector.val().trim();
                var cost_center_level = cost_center_id == '' ? 'project' : 'task';
                cost_center_id = cost_center_id == '' ? $(this).attr('project_id') : cost_center_id;
                var url = controller + cost_center_level + '/' + cost_center_id;

                table.DataTable({
                    colReorder: true,
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        url: url,
                        type: 'POST',
                        data:{'budget_type':budget_type},
                    },
                    "columns": table_columns,
                    "language": {
                        "zeroRecords": "<div class='alert alert-info'>No matching budget items found</div>",
                        "emptyTable": "<div class='alert alert-info'>No budget items found</div>"
                    }, "drawCallback": function (settings) {

                        //Update budget total at the footer
                        table.find('#total_budget_amount_display').text(settings.json.budget_total).priceFormat();

                        //Update datatable
                        var update_datatable = function () {
                            cost_center_id = cost_center_selector.val().trim();
                            container_box.find('select[name="cost_center_selector"],select[name="cost_center_id"]').val(cost_center_id);
                            cost_center_level = cost_center_id == '' ? 'project' : 'task';
                            cost_center_id = cost_center_id == '' ? table.attr('project_id') : cost_center_id;
                            url = controller + cost_center_level + '/' + cost_center_id;
                            table.DataTable().ajax.url(url).load();
                        };


                        //initialize_cost_center_selectors
                        cost_center_selector.each(function () {
                            if($(this).attr('initialized') != 'true'){
                                container_box.find('select[name="cost_center_selector"]').change(update_datatable);
                                $(this).attr('initialized','true');
                                container_box.find('select[name="cost_center_id"]').change(function () {
                                    cost_center_selector.val($(this).val());
                                    $(this).closest('.modal').on('hidden.bs.modal', update_datatable);
                                });
                            }
                        });

                        //Initialize material budget form
                        additional_call_back_function();

                        //Delete material budget item delete
                        table.find('.budget_item_delete').each(function () {
                            var button = $(this);
                            if (button.attr('active') != 'true') {
                                button.click(function () {
                                    if (confirm('Are you sure?')) {
                                        start_spinner();
                                        $.post(
                                            base_url + "budgets/budget_item_delete/",
                                            {
                                                budget_type:budget_type,
                                                item_id: button.attr('item_id')
                                            }
                                        ).complete(function () {
                                            stop_spinner();
                                            table.DataTable().draw('page');
                                        });
                                    }
                                    button.attr('active', true);
                                });
                            }
                        });

                        table.find('tr').each(function () {
                            $(this).find('td:last-child').attr('nowrap', 'nowrap');
                        });
                        initialize_common_js();

                    }
                });

                initialize_common_js();
                $(this).attr('initialized','true');
            } else {
                $(this).DataTable().draw('page');
            }
        });
    };

    var material_form_initializer = function () {
        $('.material_budget_form').each(function () {
            var modal = $(this);
            if(modal.attr('initialized') != 'true') {
                modal.on('show.bs.modal', function (e) {
                    var container_box = modal.closest('.box');
                    var cost_center_selector = container_box.find('select[name="cost_center_selector"]');
                    var form_cost_center_selector = modal.find('select[name="cost_center_id"]');
                    var material_selector = modal.find('.budget_material_selector');

                    var load_material_options = function () {
                        form_cost_center_selector = modal.find('select[name="cost_center_id"]');
                        var cost_center_id = form_cost_center_selector.val();
                        var cost_center_level = cost_center_id.trim() != '' ? 'task' : 'project';
                        cost_center_id = cost_center_id.trim() != '' ? cost_center_id : modal.find('input[name="project_id"]').val();

                        if (material_selector) {
                            start_spinner();
                            material_selector.val('');
                            $.post(
                                base_url + "budgets/budget_material_options",
                                {
                                    cost_center_level: cost_center_level,
                                    cost_center_id: cost_center_id
                                }, function (data) {
                                    material_selector.html(data);
                                }
                            ).complete(function () {
                                stop_spinner();
                            });
                        }
                    };

                    load_material_options();

                    if(form_cost_center_selector.attr('initialized') != 'true'){
                        form_cost_center_selector.change(load_material_options);
                        form_cost_center_selector.attr('initialized','true');
                    }

                    initialize_form_amount_calculator(modal.find('form'));

                    if(material_selector.attr('initialized') != 'true') {
                        material_selector.change(function () {
                            load_material_unit($(this), 'form');
                        });
                        material_selector.attr('initialized','true');
                    }

                    //Save material budget item

                    modal.find('.save_material_budget_item').each(function(){
                        var button = $(this);
                        if(button.attr('initialized') != 'true') {
                            button.click(function () {
                                var modal = button.closest('.modal');
                                var material_item_id = modal.find('select[name="material_item_id"]').val();
                                var quantity = modal.find('input[name="quantity"]').val();
                                var project_id = modal.find('input[name="project_id"]').val();
                                if(quantity > 0 && material_item_id != '') {
                                    start_spinner();
                                    modal.modal('hide');
                                    var item_id = modal.find('input[name="item_id"]').val();
                                    var rate = modal.find('input[name="rate"]').unmask();
                                    var description = modal.find('textarea[name="description"]').val();
                                    var cost_center_id = form_cost_center_selector.val().trim();

                                    $.post(
                                        base_url + "budgets/save_material_budget_item/",
                                        {
                                            item_id: item_id,
                                            project_id: project_id,
                                            cost_center_id: cost_center_id,
                                            material_item_id: material_item_id,
                                            rate: rate,
                                            quantity: quantity,
                                            description: description
                                        }
                                    ).complete(function(){
                                        //reset form
                                        var form = button.closest('form');
                                        form[0].reset();
                                        if(item_id.trim() == '') {
                                            material_selector.select2('val', '');
                                        }
                                        container_box.find('select[name="cost_center_id"]').val(cost_center_id);
                                        cost_center_selector.val(cost_center_id);

                                        //Notify
                                        toast('success','Material Budget has been successfully saved');

                                        //Redraw table
                                        var cost_center_level = cost_center_id == '' ? 'project' : 'task';
                                        var table = container_box.find('.material_budget_items');
                                        cost_center_id = cost_center_id == '' ? table.attr('project_id') : cost_center_id;
                                        var url = controller + cost_center_level + '/' + cost_center_id;
                                        table.DataTable().ajax.url(url).load();
                                        stop_spinner();
                                    });
                                } else {
                                    display_form_fields_error();
                                }
                            });
                            button.attr('initialized', 'true');
                        }
                    });
                });
                modal.attr('initialized','true');
            }
        });
    };

    initialize_budget_datatable(
        'material',
        [
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": false}
        ],
        material_form_initializer
    );

    $('a[href="#material_budgeting"]').on('shown.bs.tab', function (e){
        initialize_budget_datatable(
            'material',
            [
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": false}
            ],
            material_form_initializer
        );
    });

    $('a[href="#miscellaneous_budgeting_tab"]').on('shown.bs.tab', function (e){

        initialize_budget_datatable(
            'miscellaneous',
            [
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": false}
            ],
            function () {
                $('.miscellaneous_budget_form').each(function () {
                    var modal = $(this);
                    if(modal.attr('initialized') != 'true') {
                        modal.on('show.bs.modal', function (e) {
                            var container_box = modal.closest('.box');
                            var cost_center_selector = container_box.find('select[name="cost_center_selector"]');
                            var form_cost_center_selector = modal.find('select[name="cost_center_id"]');
                            var rate_mode_selector = modal.find('select[name="rate_mode"]');
                            var expense_selector = modal.find('.budget_expense_account_selector');

                            var load_expense_account_options = function () {
                                form_cost_center_selector = modal.find('select[name="cost_center_id"]');
                                var cost_center_id = form_cost_center_selector.val();
                                var cost_center_level = cost_center_id.trim() != '' ? 'task' : 'project';
                                cost_center_id = cost_center_id.trim() != '' ? cost_center_id : modal.find('input[name="project_id"]').val();

                                if (expense_selector) {
                                    start_spinner();
                                    expense_selector.val('');
                                    $.post(
                                        base_url + "budgets/budget_expense_account_options",
                                        {
                                            cost_center_level: cost_center_level,
                                            cost_center_id: cost_center_id
                                        }, function (data) {
                                            expense_selector.html(data);
                                        }
                                    ).complete(function () {
                                        stop_spinner();
                                    });
                                }
                            };

                            load_expense_account_options();

                            if(form_cost_center_selector.attr('initialized') != 'true'){
                                form_cost_center_selector.change(load_expense_account_options);
                                form_cost_center_selector.attr('initialized','true');
                            }

                            //Save casual miscellaneous budget item
                            $('.save_miscellaneous_budget_item').each(function(){
                                var button = $(this);
                                if(button.attr('initialized') != 'true') {

                                    //Initialize form essentials
                                    var modal = button.closest('.modal');
                                    var container_box = modal.closest('.box');
                                    var cost_center_selector = container_box.find('select[name="cost_center_selector"]');
                                    var form_cost_center_selector = modal.find('select[name="cost_center_id"]');

                                    button.click(function () {
                                        var modal = button.closest('.modal');
                                        var expense_account_id = modal.find('select[name="expense_account_id"]').val();
                                        var amount = modal.find('input[name="amount"]').unmask();
                                        var cost_center_id = form_cost_center_selector.val().trim();
                                        if(amount > 0 && expense_account_id != '') {
                                            start_spinner();
                                            modal.modal('hide');
                                            var item_id = modal.find('input[name="item_id"]').val();
                                            var project_id = modal.find('input[name="project_id"]').val();
                                            var description = modal.find('textarea[name="description"]').val();

                                            $.post(
                                                base_url + "budgets/save_miscellaneous_budget_item/",
                                                {
                                                    project_id: project_id,
                                                    cost_center_id : cost_center_id,
                                                    expense_account_id: expense_account_id,
                                                    amount: amount,
                                                    item_id: item_id,
                                                    description: description
                                                }
                                            ).complete(function(){
                                                toast('success','Miscellaneous Budget has been successfully saved');
                                                modal.find('form')[0].reset();
                                                container_box.find('select[name="cost_center_id"]').val(cost_center_id);
                                                cost_center_selector.val(cost_center_id);
                                                var cost_center_level = cost_center_id == '' ? 'project' : 'task';
                                                var table = container_box.find('.miscellaneous_budget_items');
                                                cost_center_id = cost_center_id == '' ? table.attr('project_id') : cost_center_id;
                                                var url = controller + cost_center_level + '/' + cost_center_id;
                                                table.DataTable().ajax.url(url).load();
                                                stop_spinner();
                                            });
                                        }
                                    });

                                    button.attr('initialized', 'true');
                                }
                            });
                        });
                        modal.attr('initialized','true');
                    }
                });
            }
        );
    });

    $('a[href="#labour_budgeting_tab"]').on('shown.bs.tab', function (e){

        var initialize_permanent_labour_tab = function () {
            initialize_budget_datatable(
                'permanent_labour',
                [
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": false}
                ],
                function () {
                    $('.permanent_labour_budget_form').each(function () {
                        var modal = $(this);
                        if(modal.attr('initialized') != 'true') {
                            modal.on('show.bs.modal', function (e) {
                                var container_box = modal.closest('.box');
                                var cost_center_selector = container_box.find('select[name="cost_center_selector"]');
                                var form_cost_center_selector = modal.find('select[name="cost_center_id"]');
                                var rate_mode_selector = modal.find('select[name="rate_mode"]');
                                var job_position_selector = modal.find('.budget_job_position_selector');

                                var load_job_position_options = function () {
                                    form_cost_center_selector = modal.find('select[name="cost_center_id"]');
                                    var cost_center_id = form_cost_center_selector.val();
                                    var cost_center_level = cost_center_id.trim() != '' ? 'task' : 'project';
                                    cost_center_id = cost_center_id.trim() != '' ? cost_center_id : modal.find('input[name="project_id"]').val();

                                    if (job_position_selector) {
                                        start_spinner();
                                        job_position_selector.val('');
                                        $.post(
                                            base_url + "budgets/budget_job_position_options",
                                            {
                                                cost_center_level: cost_center_level,
                                                cost_center_id: cost_center_id,
                                                rate_mode: rate_mode_selector.val()
                                            }, function (data) {
                                                job_position_selector.html(data);
                                            }
                                        ).complete(function () {
                                            stop_spinner();
                                        });
                                    }
                                };

                                load_job_position_options();

                                if(form_cost_center_selector.attr('initialized') != 'true'){
                                    form_cost_center_selector.change(load_job_position_options);
                                    form_cost_center_selector.attr('initialized','true');
                                }

                                if(rate_mode_selector.attr('initialized') != 'true'){
                                    rate_mode_selector.change(load_job_position_options);
                                    rate_mode_selector.attr('initialized','true');
                                };

                                job_position_selector.change(function () {
                                    start_spinner();

                                    $.post(
                                        base_url + "human_resources/job_position_average_salary",
                                        {
                                            job_position_id : $(this).val()
                                        }, function (data) {
                                            modal.find('input[name="salary_rate"]').val(data).priceFormat();
                                            stop_spinner();
                                        }
                                    );
                                });

                                modal.on('change keyup',
                                    'input[name="allowance_rate"], input[name="salary_rate"],' +
                                    ' input[name="duration"], input[name="no_of_staff"]',
                                    function(){
                                        var duration = parseFloat(modal.find('input[name="duration"]').val());
                                        var no_of_staff = parseFloat(modal.find('input[name="no_of_staff"]').val());
                                        var allowance_rate = modal.find('input[name="allowance_rate"]').unmask();
                                        var salary_rate = modal.find('input[name="salary_rate"]').unmask();
                                        var base_amount = duration*no_of_staff;
                                        var salary_amount = base_amount*salary_rate;
                                        var allowance_amount = allowance_rate*base_amount;
                                        modal.find('input[name="allowance_amount"]').val(allowance_amount).priceFormat();
                                        modal.find('input[name="salary_amount"]').val(salary_amount).priceFormat();
                                        modal.find('input[name="total_amount"]').val((salary_amount+allowance_amount)).priceFormat();
                                    });

                                //Save permanent labour budget item

                                modal.find('.save_permanent_labour_budget_item').each(function(){
                                    var button = $(this);
                                    if(button.attr('initialized') != 'true') {
                                        button.click(function () {
                                            var modal = button.closest('.modal');
                                            var job_position_id = modal.find('select[name="job_position_id"]').val();
                                            var rate_mode = rate_mode_selector.val();
                                            var no_of_staff = parseFloat(modal.find('input[name="no_of_staff"]').val());
                                            var project_id = modal.find('input[name="project_id"]').val();
                                            var duration = parseFloat(modal.find('input[name="duration"]').val());
                                            if(duration > 0 && no_of_staff > 0 && job_position_id != '') {
                                                start_spinner();
                                                modal.modal('hide');
                                                var item_id = modal.find('input[name="item_id"]').val();
                                                var salary_rate = modal.find('input[name="salary_rate"]').unmask();
                                                var allowance_rate = modal.find('input[name="allowance_rate"]').unmask();
                                                var description = modal.find('textarea[name="description"]').val();
                                                var cost_center_id = form_cost_center_selector.val().trim();

                                                $.post(
                                                    base_url + "budgets/save_permanent_labour_budget_item/",
                                                    {
                                                        item_id: item_id,
                                                        project_id: project_id,
                                                        cost_center_id: cost_center_id,
                                                        job_position_id: job_position_id,
                                                        duration: duration,
                                                        rate_mode: rate_mode,
                                                        salary_rate: salary_rate,
                                                        allowance_rate: allowance_rate,
                                                        no_of_staff: no_of_staff,
                                                        description: description
                                                    }
                                                ).complete(function(){
                                                    //reset form
                                                    var form = button.closest('form');
                                                    form[0].reset();
                                                    if(item_id.trim() == '') {
                                                        job_position_selector.select2('val', '');
                                                    }
                                                    container_box.find('select[name="cost_center_id"]').val(cost_center_id);
                                                    cost_center_selector.val(cost_center_id);
                                                    var cost_center_level = cost_center_id == '' ? 'project' : 'task';
                                                    var table = container_box.find('.permanent_labour_budget_items');
                                                    cost_center_id = cost_center_id == '' ? table.attr('project_id') : cost_center_id;
                                                    var url = controller + cost_center_level + '/' + cost_center_id;
                                                    table.DataTable().ajax.url(url).load();
                                                    stop_spinner();
                                                });
                                            }
                                        });
                                        button.attr('initialized', 'true');
                                    }
                                });
                            });
                            modal.attr('initialized','true');
                        }
                    });
                }
            );
        };

        initialize_permanent_labour_tab();

        $('a[href="#permanent_labour_budgeting_tab"]').on('shown.bs.tab', function (e){
            initialize_permanent_labour_tab();
        });

        $('a[href="#casual_labour_budgeting_tab"]').on('shown.bs.tab', function (e){
            initialize_budget_datatable(
                'casual_labour',
                [
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": false}
                ],
                function () {
                    $('.casual_labour_budget_form').each(function () {
                        var modal = $(this);
                        if(modal.attr('initialized') != 'true') {
                            modal.on('show.bs.modal', function (e) {
                                var container_box = modal.closest('.box');
                                var cost_center_selector = container_box.find('select[name="cost_center_selector"]');
                                var form_cost_center_selector = modal.find('select[name="cost_center_id"]');
                                var rate_mode_selector = modal.find('select[name="rate_mode"]');
                                var labour_type_selector = modal.find('.budget_casual_labour_type_selector');

                                var load_labour_options = function () {
                                    form_cost_center_selector = modal.find('select[name="cost_center_id"]');
                                    var cost_center_id = form_cost_center_selector.val();
                                    var cost_center_level = cost_center_id.trim() != '' ? 'task' : 'project';
                                    cost_center_id = cost_center_id.trim() != '' ? cost_center_id : modal.find('input[name="project_id"]').val();

                                    if (labour_type_selector) {
                                        start_spinner();
                                        labour_type_selector.val('');
                                        $.post(
                                            base_url + "budgets/budget_casual_labour_type_options",
                                            {
                                                cost_center_level: cost_center_level,
                                                cost_center_id: cost_center_id,
                                                rate_mode: rate_mode_selector.val()
                                            }, function (data) {
                                                labour_type_selector.html(data);
                                            }
                                        ).complete(function () {
                                            stop_spinner();
                                        });
                                    }
                                };

                                load_labour_options();

                                if(form_cost_center_selector.attr('initialized') != 'true'){
                                    form_cost_center_selector.change(load_labour_options);
                                    form_cost_center_selector.attr('initialized','true');
                                }

                                if(rate_mode_selector.attr('initialized') != 'true'){
                                    rate_mode_selector.change(load_labour_options);
                                    rate_mode_selector.attr('initialized','true');
                                };

                                modal.on('change keyup',
                                    'input[name="rate"], input[name="duration"], input[name="no_of_workers"]',
                                    function(){
                                        var duration = parseFloat(modal.find('input[name="duration"]').val());
                                        var no_of_workers = parseFloat(modal.find('input[name="no_of_workers"]').val());
                                        var rate = modal.find('input[name="rate"]').unmask();
                                        modal.find('input[name="total_amount"]').val(duration*no_of_workers*rate).priceFormat();
                                    });


                                //Save casual labour budget item

                                modal.find('.save_casual_labour_budget_item').each(function(){
                                    var button = $(this);
                                    if(button.attr('initialized') != 'true') {
                                        button.click(function () {
                                            var modal = button.closest('.modal');
                                            var casual_labour_type_id = modal.find('select[name="casual_labour_type_id"]').val();
                                            var rate_mode = rate_mode_selector.val();
                                            var no_of_workers = parseFloat(modal.find('input[name="no_of_workers"]').val());
                                            var project_id = modal.find('input[name="project_id"]').val();
                                            var duration = parseFloat(modal.find('input[name="duration"]').val());
                                            if(duration > 0 && no_of_workers > 0 && casual_labour_type_id != '') {
                                                start_spinner();
                                                modal.modal('hide');
                                                var item_id = modal.find('input[name="item_id"]').val();
                                                var rate = modal.find('input[name="rate"]').unmask();
                                                var description = modal.find('textarea[name="description"]').val();
                                                var cost_center_id = form_cost_center_selector.val();
                                                $.post(
                                                    base_url + "budgets/save_casual_labour_budget_item/",
                                                    {
                                                        item_id: item_id,
                                                        project_id: project_id,
                                                        cost_center_id: form_cost_center_selector.val().trim(),
                                                        casual_labour_type_id: casual_labour_type_id,
                                                        duration: duration,
                                                        rate_mode: rate_mode,
                                                        rate: rate,
                                                        no_of_workers: no_of_workers,
                                                        description: description
                                                    }
                                                ).complete(function(){
                                                    //reset form
                                                    var form = button.closest('form');
                                                    form[0].reset();
                                                    if(item_id.trim() == '') {
                                                        labour_type_selector.select2('val', '');
                                                    }
                                                    container_box.find('select[name="cost_center_id"]').val(cost_center_id);
                                                    cost_center_selector.val(cost_center_id);
                                                    var cost_center_level = cost_center_id == '' ? 'project' : 'task';
                                                    var table = container_box.find('.casual_labour_budget_items');
                                                    cost_center_id = cost_center_id == '' ? table.attr('project_id') : cost_center_id;
                                                    var url = controller + cost_center_level + '/' + cost_center_id;
                                                    table.DataTable().ajax.url(url).load();
                                                    stop_spinner();
                                                });
                                            }
                                        });
                                        button.attr('initialized', 'true');
                                    }
                                });
                            });
                            modal.attr('initialized','true');
                        }
                    });
                }
            );
        });

    });

    $('a[href="#tools_budgeting_tab"]').on('shown.bs.tab', function (e){

        initialize_budget_datatable(
            'tools',
            [
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": false}
            ],
            function () {
                //Initialize form
                $('.tools_budget_form').each(function () {
                    var modal = $(this);
                    if(modal.attr('initialized') != 'true') {
                        modal.on('show.bs.modal', function (e) {
                            var container_box = modal.closest('.box');
                            var cost_center_selector = container_box.find('select[name="cost_center_selector"]');
                            var form_cost_center_selector = modal.find('select[name="cost_center_id"]');
                            var tool_type_selector = modal.find('.budget_tool_type_selector');

                            var load_tool_type_options = function () {
                                form_cost_center_selector = modal.find('select[name="cost_center_id"]');
                                var cost_center_id = form_cost_center_selector.val();
                                var cost_center_level = cost_center_id.trim() != '' ? 'task' : 'project';
                                cost_center_id = cost_center_id.trim() != '' ? cost_center_id : modal.find('input[name="project_id"]').val();

                                if (tool_type_selector) {
                                    start_spinner();
                                    tool_type_selector.val('');
                                    $.post(
                                        base_url + "budgets/budget_tool_type_options",
                                        {
                                            cost_center_level: cost_center_level,
                                            cost_center_id: cost_center_id
                                        }, function (data) {
                                            tool_type_selector.html(data);
                                        }
                                    ).complete(function () {
                                        stop_spinner();
                                    });
                                }
                            };

                            load_tool_type_options();

                            if(form_cost_center_selector.attr('initialized') != 'true'){
                                form_cost_center_selector.change(load_tool_type_options);
                                form_cost_center_selector.attr('initialized','true');
                            }

                            initialize_form_amount_calculator(modal.find('form'));

                            if(tool_type_selector.attr('initialized') != 'true') {
                                tool_type_selector.change(function () {
                                    load_material_unit($(this), 'form');
                                });
                                tool_type_selector.attr('initialized','true');
                            }

                            //Save material budget item

                            modal.find('.save_tools_budget_item').each(function(){
                                var button = $(this);
                                if(button.attr('initialized') != 'true') {
                                    button.click(function () {
                                        var modal = button.closest('.modal');
                                        var tool_type_id = modal.find('select[name="tool_type_id"]').val();
                                        var quantity = modal.find('input[name="quantity"]').val();
                                        var project_id = modal.find('input[name="project_id"]').val();
                                        if(quantity > 0 && tool_type_id != '') {
                                            start_spinner();
                                            modal.modal('hide');
                                            var item_id = modal.find('input[name="item_id"]').val();
                                            var rate = modal.find('input[name="rate"]').unmask();
                                            var description = modal.find('textarea[name="description"]').val();
                                            var cost_center_id = form_cost_center_selector.val().trim();
                                            $.post(
                                                base_url + "budgets/save_tools_budget_item/",
                                                {
                                                    item_id: item_id,
                                                    project_id: project_id,
                                                    cost_center_id: cost_center_id,
                                                    tool_type_id: tool_type_id,
                                                    rate: rate,
                                                    quantity: quantity,
                                                    description: description
                                                }
                                            ).complete(function(){
                                                //reset form
                                                var form = button.closest('form');
                                                form[0].reset();
                                                if(item_id.trim() == '') {
                                                    tool_type_selector.select2('val', '');
                                                }
                                                container_box.find('select[name="cost_center_id"]').val(cost_center_id);
                                                cost_center_selector.val(cost_center_id);
                                                var cost_center_level = cost_center_id == '' ? 'project' : 'task';
                                                var table = container_box.find('.tools_budget_items');
                                                cost_center_id = cost_center_id == '' ? table.attr('project_id') : cost_center_id;
                                                var url = controller + cost_center_level + '/' + cost_center_id;
                                                table.DataTable().ajax.url(url).load();
                                                stop_spinner();
                                            });
                                        }
                                    });
                                    button.attr('initialized', 'true');
                                }
                            });
                        });
                        modal.attr('initialized','true');
                    }
                });
            }
        );

    });

    $('a[href="#equipment_budgeting_tab"]').on('shown.bs.tab', function (e){
        initialize_budget_datatable(
            'equipment',
            [
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": false}
            ],function () {

                $('.save_equipment_budget_btn').each(function () {

                    var button = $(this);
                    if (button.attr('initialized') != 'true') {
                        button.click(function () {
                            //  alert('Hey am working');
                            var modal = button.closest('.modal');
                            var asset_group_id = modal.find("select[name='asset_group_id']").val();
                            var cost_center_id = modal.find("select[name='cost_center_id']").val();
                            var project_id = modal.find("input[name='project_id']").val();
                            var equipment_budget_id = modal.find("input[name='equipment_budget_id']").val();
                            var rate_mode = modal.find("select[name='rate_mode']").val();
                            var rate = modal.find("input[name='rate']").unmask();
                            var duration = modal.find("input[name='duration']").unmask();
                            var quantity = modal.find("input[name='quantity']").unmask();
                            var description = modal.find("textarea[name='description']").val();

                            if (asset_group_id != '') {
                                start_spinner();
                                modal.modal('hide');
                                $.post(
                                    base_url + "Budgets/save_equipment_budget_item",
                                    {
                                        equipment_budget_id: equipment_budget_id,
                                        asset_group_id: asset_group_id,
                                        cost_center_id: cost_center_id,
                                        project_id: project_id,
                                        rate_mode: rate_mode,
                                        rate: rate,
                                        duration: duration,
                                        quantity: quantity,
                                        description: description

                                    }, function () {
                                        stop_spinner();
                                        modal.find('form')[0].reset();
                                        $('.equipment_budget_items').DataTable().draw('page');
                                        toast('success', 'Equipment_budget Added successful ');
                                    }
                                );
                            } else {
                                toast('warning', 'Equipment_budget Name Must be filled ');
                            }
                        });
                        button.attr('initialized', 'true');
                    }
                });

                //DeleteEquipment
                $('.delete_equipment_budget').each(function () {
                    var button = $(this);
                    if (button.attr('active') != 'true') {
                        button.click(function () {
                            if (confirm('Are you sure?')) {
                                start_spinner();
                                $.post(
                                    base_url + "Budgets/delete_equipment_budget",
                                    {
                                        equipment_budget_id: button.attr('equipment_budget_id')
                                    }, function () {
                                        $('.equipment_budget_items').DataTable().draw('page');
                                    }
                                ).complete(function () {
                                    stop_spinner();
                                });
                            }
                        });
                        button.attr('active', 'true');
                    }
                });


                //equipment form amount calculator
                $('.equipment_budget_form').each(function () {

                    var modal = $(this);

                    modal.on('change keyup', 'input[name="rate"], input[name="duration"], input[name="quantity"]',
                        function () {
                            var duration = parseFloat(modal.find('input[name="duration"]').val());
                            var quantity = parseFloat(modal.find('input[name="quantity"]').val());
                            var rate = modal.find('input[name="rate"]').unmask();

                            modal.find('input[name="amount"]').val(duration * quantity * rate).priceFormat();
                        });


                });

                initialize_common_js();

                $(this).find('tr').each(function () {
                    $(this).find('td:last-child').attr('nowrap', 'nowrap');
                });

            }

        );
    });

    $('a[href="#sub_contract_budgeting_tab"]').on('shown.bs.tab', function (e){

        initialize_budget_datatable(
            'sub_contract',
            [
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": false}
            ],
            function () {
                $('.sub_contract_budget_form').each(function () {
                    var modal = $(this);
                    if(modal.attr('initialized') != 'true') {
                        modal.on('show.bs.modal', function (e) {

                            //Save sub_contract budget item
                            $('.save_sub_contract_budget').each(function(){
                                var button = $(this);
                                if(button.attr('initialized') != 'true') {

                                    //Initialize form essentials
                                    var modal = button.closest('.modal');
                                    var container_box = modal.closest('.box');
                                    var cost_center_selector = container_box.find('select[name="cost_center_selector"]');
                                    var form_cost_center_selector = modal.find('select[name="cost_center_id"]');

                                    button.click(function () {

                                        var modal = button.closest('.modal');
                                        var budget_item_id = modal.find('input[name="budget_item_id"]').val();
                                        var project_id = modal.find('input[name="project_id"]').val();
                                        var cost_center_id = modal.find('select[name="cost_center_id"]').val();
                                        var amount = modal.find('input[name="amount"]').unmask();
                                        var description = modal.find('textarea[name="description"]').val();
                                        if(amount != '' ) {
                                            start_spinner();
                                            modal.modal('hide');
                                            $.post(
                                                base_url + "budgets/save_sub_contract_budget/",
                                                {
                                                    budget_item_id: budget_item_id,
                                                    project_id: project_id,
                                                    cost_center_id : cost_center_id,
                                                    amount: amount,
                                                    description: description
                                                }
                                            ).complete(function(){
                                                toast('success','Sub_contract Budget has been successfully saved');
                                                modal.find('form')[0].reset();
                                                container_box.find('select[name="cost_center_id"]').val(cost_center_id);
                                                cost_center_selector.val(cost_center_id);
                                                var cost_center_level = cost_center_id == '' ? 'project' : 'task';
                                                var table = container_box.find('.sub_contract_budget_items');
                                                cost_center_id = cost_center_id == '' ? table.attr('project_id') : cost_center_id;
                                                var url = controller + cost_center_level + '/' + cost_center_id;
                                                table.DataTable().ajax.url(url).load();
                                                stop_spinner();
                                            });
                                        }
                                    });

                                    button.attr('initialized', 'true');
                                }
                            });
                        });
                        modal.attr('initialized','true');
                    }
                });
            }
        );
    });

});

$('a[href="#project_requisitions"]').on('shown.bs.tab', function (e){
    $('#project_requisitions').find('#project_requisitions_table').each(function(){
        var table = $(this);
        if($(this).attr('dataTable_initialized') != 'true') {
            var project_id = $(this).attr('project_id');
            $(this).DataTable({
                colReorder: true,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: base_url + "requisitions/index/"+project_id,
                    type: 'POST',
                    data: {
                        project_id : project_id
                    }
                },
                "columns": [
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": false}
                ],
                "language": {
                    "zeroRecords": "<div class='alert alert-info'>No matching requisitions found</div>",
                    "emptyTable": "<div class='alert alert-info'>No requisitions  found for this project</div>"
                },
                "drawCallback": function () {

                    $(this).find('tr').each(function () {
                        $(this).find('td:last-child').attr('nowrap', 'nowrap');
                    });

                    initialize_requisition_approval_forms(table);
                    initialize_requisition_buttons();
                    initialize_common_js();
                    initialize_requisition_and_order_form();
                }
            });
            $(this).attr('dataTable_initialized','true');
        } else {
            $(this).DataTable().draw('page');
        }
    });
});

$('a[href="#project_costs"]').on('shown.bs.tab', function (e){


    var cost_tab_pane = $($(this).attr('href')).find('#costs_summary');
    var summary_cost_center_selector = cost_tab_pane.find('select[name="cost_center_selector"]');
    var load_costs_summary = function () {
        var project_id = summary_cost_center_selector.next().val();
        var cost_center_id = summary_cost_center_selector.val();
        start_spinner();
        $.post(
            base_url + "costs/load_project_costs_summary",
            {
                project_id :  project_id,
                cost_center_id : cost_center_id
            }, function (data) {
                $('#costs_summary_table_container').html(data);
                stop_spinner();
            }
        );
    };

    load_costs_summary();

    summary_cost_center_selector.each(function () {
        $(this).off('change').on('change',function () {
            load_costs_summary();
        });
    });

    var controller = base_url+"costs/costs_items_list/";
    var initialize_cost_datatable = function (cost_type,table_columns,additional_call_back_function) {
        var table_class = $('.'+cost_type+'_costs_items');
        table_class.each(function () {
            if($(this).attr('initialized') != 'true') {
                var table = $(this);
                var container_box = table.closest('.box');
                var cost_center_selector = container_box.find('select[name="cost_center_selector"]');
                var cost_center_id = cost_center_selector.val().trim();
                var cost_center_level = cost_center_id == '' ? 'project' : 'task';
                cost_center_id = cost_center_id == '' ? $(this).attr('project_id') : cost_center_id;
                var url = controller + cost_center_level + '/' + cost_center_id;

                table.DataTable({
                    colReorder: true,
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        url: url,
                        type: 'POST',
                        data:{
                            'cost_type':cost_type
                        }
                    },
                    "columns": table_columns,
                    "language": {
                        "zeroRecords": "<div class='alert alert-info'>No matching costs items found</div>",
                        "emptyTable": "<div class='alert alert-info'>No costs items found</div>"
                    }, "drawCallback": function (settings) {

                        //Update budget total at the footer
                        table.find('#total_cost_amount_display').text(settings.json.cost_total).priceFormat();

                        //Update datatable
                        var update_datatable = function () {
                            cost_center_id = cost_center_selector.val().trim();
                            container_box.find('select[name="cost_center_selector"],select[name="cost_center_id"]').val(cost_center_id);
                            cost_center_level = cost_center_id == '' ? 'project' : 'task';
                            cost_center_id = cost_center_id == '' ? table.attr('project_id') : cost_center_id;
                            url = controller + cost_center_level + '/' + cost_center_id;
                            table.DataTable().ajax.url(url).load();
                        };


                        //initialize_cost_center_selectors
                        cost_center_selector.each(function () {
                            if($(this).attr('initialized') != 'true'){
                                container_box.find('select[name="cost_center_selector"]').change(update_datatable);
                                $(this).attr('initialized','true');
                                container_box.find('select[name="cost_center_id"]').change(function () {
                                    cost_center_selector.val($(this).val());
                                    $(this).closest('.modal').on('hidden.bs.modal', update_datatable);
                                });
                            }
                        });

                        //Initialize material budget form
                        additional_call_back_function();

                        //Delete material budget item delete
                        table.find('.cost_item_delete').each(function () {
                            var button = $(this);
                            if (button.attr('active') != 'true') {
                                button.click(function () {
                                    if (confirm('Are you sure?')) {
                                        start_spinner();
                                        $.post(
                                            base_url + "costs/cost_item_delete/",
                                            {
                                                cost_type:cost_type,
                                                item_id: button.attr('item_id')
                                            }
                                        ).complete(function () {
                                            stop_spinner();
                                            table.DataTable().draw('page');
                                        });
                                    }
                                    button.attr('active', true);
                                });
                            }
                        });

                        table.find('tr').each(function () {
                            $(this).find('td:last-child').attr('nowrap', 'nowrap');
                        });
                        initialize_common_js();

                    }
                });

                initialize_common_js();
                $(this).attr('initialized','true');
            } else {
                $(this).DataTable().draw('page');
            }
        });
    };

    $('a[href="#costs_summary"]').on('shown.bs.tab', function (e){
        load_costs_summary();
    });

    $('a[href="#material_costs"]').on('shown.bs.tab', function (e){
        initialize_cost_datatable('material',
            [
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": false}
            ],function () {
                $('.material_cost_form').each(function () {
                    var modal = $(this);
                    if(modal.attr('initialized') != 'true') {
                        modal.on('show.bs.modal', function (e) {
                            var container_box = modal.closest('.box');
                            var cost_center_selector = container_box.find('select[name="cost_center_selector"]');
                            var form_cost_center_selector = modal.find('select[name="cost_center_id"]');

                            modal.find('select[name="source_sub_location_id"]').change(function () {
                                load_sub_location_available_material_options($(this),'form');
                            });

                            var material_selector = $(this).find('.cost_material_selector');
                            if(material_selector.val() != ''){
                                validate_sub_store_material_quantity(material_selector,'form');
                            }

                            if(material_selector.attr('initialize_load_quantity_and_rate') != 'true') {
                                material_selector.change(function () {
                                    load_material_unit($(this), 'form');
                                    load_material_average_price($(this), 'form');
                                    validate_sub_store_material_quantity($(this), 'form');
                                });
                                material_selector.attr('initialize_load_quantity_and_rate','true');
                            }

                            initialize_form_amount_calculator($(this));

                            //Initialize material_save
                            $('.save_material_cost').each(function(){
                                var button = $(this);
                                if(button.attr('active') != 'true') {
                                    button.click(function () {
                                        var modal = button.closest('.modal');
                                        var project_id = modal.find('input[name="project_id"]').val();
                                        var item_id = modal.find('input[name="item_id"]').val();
                                        var source_sub_location_id = modal.find('select[name="source_sub_location_id"]').val();
                                        var material_id = modal.find('select[name="material_id"]').val();
                                        var description = modal.find('textarea[name="description"]').val();
                                        var quantity = modal.find('input[name="quantity"]').val();
                                        var rate = modal.find('input[name="rate"]').unmask();
                                        var date = modal.find('input[name="date"]').val();
                                        var cost_center_id = form_cost_center_selector.val();
                                        if(project_id != '' && quantity != '' && date != '') {
                                            modal.modal('hide');

                                            $.post(
                                                base_url + "costs/save_material_cost/",
                                                {
                                                    source_sub_location_id: source_sub_location_id,
                                                    material_id : material_id,
                                                    project_id: project_id,
                                                    cost_center_id: cost_center_id,
                                                    date: date,
                                                    description: description,
                                                    quantity: quantity,
                                                    rate: rate,
                                                    item_id: item_id
                                                }
                                            ).complete(function () {
                                                //reset form
                                                var form = button.closest('form');
                                                form[0].reset();
                                                if(item_id.trim() == '') {
                                                    material_selector.select2('val', '');
                                                }
                                                container_box.find('select[name="cost_center_id"]').val(cost_center_id);
                                                cost_center_selector.val(cost_center_id);
                                                var cost_center_level = cost_center_id == '' ? 'project' : 'task';
                                                var table = container_box.find('.material_costs_items');
                                                cost_center_id = cost_center_id == '' ? table.attr('project_id') : cost_center_id;
                                                var url = controller + cost_center_level + '/' + cost_center_id;
                                                table.DataTable().ajax.url(url).load();
                                                stop_spinner();
                                            });
                                        }
                                    });
                                    button.attr('active', 'true');
                                }
                            });

                            $(this).attr('initialized','true');
                        });
                        modal.attr('initialized','true');
                    }
                });
            })
    });

    $('a[href="#permanent_labour_costs"]').on('shown.bs.tab', function (e){
        initialize_cost_datatable('permanent_labour',
            [
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": false}
            ],function () {
                $('.permanent_labour_cost_form').each(function () {
                    var modal = $(this);
                    if (modal.attr('initialized') != 'true') {
                        var container_box = modal.closest('.box');
                        var cost_center_selector = container_box.find('select[name="cost_center_selector"]');
                        var form_cost_center_selector = modal.find('select[name="cost_center_id"]');
                        modal.on('show.bs.modal', function (e) {

                            var initialize_row_js = function (row) {
                                var member_selector = row.find('select[name="member_id"]');
                                var working_mode_selector = row.find('select[name="working_mode"]');
                                var duration_field = row.find('input[name="duration"]');
                                var rate_field = row.find('input[name="salary_rate"]');

                                var load_member_salary_rate = function () {
                                    var member_id = member_selector.val();
                                    if(member_id != '') {
                                        start_spinner();
                                        $.post(
                                            base_url + "human_resources/load_team_member_salary_rate",
                                            {
                                                member_id: member_id,
                                                working_mode: working_mode_selector.val()
                                            }, function (data) {
                                                rate_field.val(data);
                                            }
                                        ).complete(function () {
                                            stop_spinner();
                                        });
                                    } else {
                                        rate_field.val(0);
                                    }
                                };

                                row.find('select[name="member_id"]').each(function () {
                                    if($(this).attr('initialized') != 'true'){
                                        $(this).on('change',function(){
                                            load_member_salary_rate();
                                        }).select2()
                                        $(this).attr('initialized','true');
                                    }
                                });

                                var calculate_duration_field = function () {
                                    var working_mode = working_mode_selector.val();
                                    if(working_mode == 'date_range'){
                                        var from_date = row.find('input[name="start_date"]').val();
                                        var to_date = row.find('input[name="end_date"]').val();
                                        if(from_date.trim() != '' && to_date.trim() != '') {
                                            function parseDate(str) {
                                                var mdy = str.split('-');
                                                return new Date(mdy[0], mdy[1] - 1, mdy[2]);
                                            }
                                            function daydiff(first, second) {
                                                return Math.round((second - first) / (1000 * 60 * 60 * 24));
                                            }
                                            duration_field.val(daydiff(parseDate(from_date), parseDate(to_date)));
                                        } else {
                                            duration_field.val(0);
                                        }
                                        duration_field.attr('readonly','true');
                                    } else if(working_mode == 'hours') {
                                        duration_field.val('');
                                        duration_field.removeAttr('readonly');
                                    } else {
                                        duration_field.val(1);
                                        duration_field.attr('readonly','true');
                                    }
                                };

                                var setup_duration_fields = function () {
                                    var working_mode = working_mode_selector.val();
                                    var active_class = working_mode == 'date_range' ? '.date_range_input' : '.single_date_input';
                                    row.find(active_class).show().siblings().hide();
                                    calculate_duration_field();
                                    load_member_salary_rate();
                                };

                                row.find('input[name="start_date"], input[name="end_date"]').each(function () {
                                    if ($(this).attr('initialized') != 'true') {
                                        $(this).on('change', function () {
                                            calculate_duration_field();
                                        });
                                        $(this).attr('initialized','true');
                                    }
                                });

                                working_mode_selector.change(function(){
                                    setup_duration_fields();
                                });

                                row.find('.row_remover').click(function () {
                                    row.remove();
                                });
                            };

                            initialize_row_js(modal.find('tbody tr:first'));

                            modal.find('.row_adder').off('click').on('click',function () {
                                var tbody = $(this).closest('table').find('tbody');
                                var new_row = tbody.closest('table').find('.row_template').clone().removeAttr('style')
                                    .removeClass('row_template').addClass('artificial_row').appendTo(tbody);
                                initialize_row_js(new_row);
                                initialize_common_js();
                            });

                            modal.find('.save_permanent_labour_cost').off('click').on('click',function () {
                                start_spinner();
                                var button = $(this);
                                var member_ids = new Array(), working_modes = new Array(), start_dates = new Array(),
                                    end_dates = new Array(), allowances = new Array(), cost_dates = new Array(), durations = new Array(),
                                    salary_rates = new Array(), descriptions = new Array();

                                var cost_center_id = modal.find('select[name="cost_center_id"]').val();
                                var project_id = modal.find('input[name="project_id"]').val();
                                var i = 0;
                                modal.modal('hide');
                                modal.find('tbody tr').each(function () {
                                    var row = $(this);
                                    var member_id = row.find('select[name="member_id"]').val().trim();
                                    var working_mode = row.find('select[name="working_mode"]').val().trim();
                                    var start_date = row.find('input[name="start_date"]').val().trim();
                                    var end_date = row.find('input[name="end_date"]').val().trim();
                                    var cost_date = row.find('input[name="cost_date"]').val().trim();
                                    var duration = row.find('input[name="duration"]').val().trim();
                                    var salary_rate = row.find('input[name="salary_rate"]').val().trim();
                                    var allowance = row.find('input[name="allowance"]').unmask();
                                    if(member_id.trim() != '' && working_mode.trim() != '' && parseFloat(duration) > 0 && ((start_date != '' && end_date != '') || cost_date != '')) {
                                        member_ids[i] = member_id;
                                        working_modes[i] = working_mode;
                                        start_dates[i] = start_date;
                                        end_dates[i] = end_date;
                                        cost_dates[i] = cost_date;
                                        durations[i] = duration;
                                        salary_rates[i] = salary_rate;
                                        allowances[i] = allowance;
                                        descriptions[i] = row.find('textarea[name="description"]').val();
                                        i++;
                                    }
                                });

                                if(member_ids.length > 0) {
                                    $.post(
                                        base_url + "costs/save_permanent_labour_cost",
                                        {
                                            project_id: project_id,
                                            cost_center_id: cost_center_id,
                                            member_ids: member_ids,
                                            working_modes: working_modes,
                                            start_dates: start_dates,
                                            end_dates: end_dates,
                                            cost_dates: cost_dates,
                                            durations: durations,
                                            salary_rates: salary_rates,
                                            allowances: allowances,
                                            descriptions: descriptions
                                        }
                                    ).complete(function () {
                                        //reset form
                                        var form = button.closest('form');
                                        form[0].reset();
                                        modal.find('tbody select[name="member_id"]').select2('val', '');
                                        modal.find('tbody .artificial_row').remove();
                                        container_box.find('select[name="cost_center_id"]').val(cost_center_id);
                                        cost_center_selector.val(cost_center_id);
                                        var cost_center_level = cost_center_id == '' ? 'project' : 'task';
                                        var table = container_box.find('.permanent_labour_costs_items');
                                        cost_center_id = cost_center_id == '' ? table.attr('project_id') : cost_center_id;
                                        var url = controller + cost_center_level + '/' + cost_center_id;
                                        table.DataTable().ajax.url(url).load();
                                        stop_spinner();
                                    });
                                }


                            });
                        });
                        modal.attr('initialized', 'true');
                    }

                });
            });
    });

    $('.owned_equipment_cost_tab').each(function(){

        var table=$(this);
        var project_id=table.attr('project_id');
        if(table.attr('initialized') != 'true') {
            table.DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: base_url + "Costs/owned_equipment_cost_list/" + project_id,
                    type: 'POST'
                },
                "columns": [
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": false}
                ],
                "language": {
                    "zeroRecords": "<div class='alert alert-info'>No matching Equipment Cost found</div>",
                    "emptyTable": "<div class='alert alert-info'>No Equipment Cost found</div>"
                },

                "drawCallback": function (settings) {

                    table.find('#total_cost_amount_display').text(settings.json.cost_total).priceFormat();

                    //Save Equipment Cost

                    $('.save_owned_equipment_cost_btn').each(function () {

                        var button = $(this);
                        if (button.attr('initialized') != 'true') {
                            button.click(function () {
                                var modal = button.closest('.modal');
                                var project_id = modal.find("input[name='project_id']").val();
                                var owned_equipment_cost_id = modal.find("input[name='owned_equipment_cost_id']").val();
                                var cost_center_id = modal.find("select[name='cost_center_id']").val();
                                var start_date = modal.find("input[name='start_date']").val();
                                var end_date = modal.find("input[name='end_date']").val();
                                var asset_id = modal.find("select[name='asset_id']").val();
                                var rate_mode = modal.find("select[name='rate_mode']").val();
                                var rate = modal.find("input[name='rate']").unmask();
                                var description = modal.find("textarea[name='description']").val();

                                console.log(project_id, owned_equipment_cost_id, cost_center_id, start_date, end_date, asset_id,
                                    rate_mode, rate, description);
                                if (asset_id != '') {
                                    start_spinner();
                                    modal.modal('hide');
                                    $.post(
                                        base_url + "Costs/save_owned_equipment_cost",
                                        {
                                            project_id: project_id,
                                            owned_equipment_cost_id: owned_equipment_cost_id,
                                            task_id: cost_center_id,
                                            start_date: start_date,
                                            end_date: end_date,
                                            asset_id: asset_id,
                                            rate_mode: rate_mode,
                                            rate: rate,
                                            description: description

                                        }, function () {
                                            stop_spinner();
                                            modal.find('form')[0].reset();
                                            $('.owned_equipment_cost_tab').DataTable().draw('page');
                                            toast('success', 'Equipment Cost Added successful ');
                                        }
                                    );
                                } else {
                                    toast('warning', 'Equipment Name Must be filled ');
                                }
                            });
                            button.attr('initialized', 'true');
                        }
                    });


                    //DeleteEquipment cost
                    $('.delete_owned_equipment_cost').each(function () {
                        var button = $(this);
                        if (button.attr('active') != 'true') {
                            button.click(function () {
                                if (confirm('Are you sure?')) {
                                    start_spinner();
                                    $.post(
                                        base_url + "Costs/delete_owned_equipment_cost",
                                        {
                                            owned_equipment_cost_id: button.attr('owned_equipment_cost_id')
                                        }, function () {
                                            $('.owned_equipment_cost_tab').DataTable().draw('page');
                                        }
                                    ).complete(function () {
                                        stop_spinner();
                                    });
                                }
                            });
                            button.attr('active', 'true');
                        }
                    });

                    $('.owned_equipment_cost_form').each(function () {

                        var modal = $(this);

                        modal.on('change', '.asset_group_selector',
                            function () {
                                var asset_group_id = modal.find('select[name="asset_group_id"]').val();
                                modal.find('input[name="amount"]').val(asset_group_id);
                                var asset_option_selector = modal.find('select[name="asset_id"]');

                                start_spinner();
                                $.post(
                                    base_url + "Projects/load_assets_options",
                                    {
                                        asset_group_id: asset_group_id

                                    }, function (data) {

                                        asset_option_selector.html(data.asset_dropdown_options);

                                        stop_spinner();

                                        initialize_common_js();

                                    }, 'json'
                                ).complete();


                            });


                        modal.find('input[name="start_date"], input[name="end_date"], input[name="amount"]').each(function () {


                            if ($(this).attr('initialized') != 'true') {

                                modal.delegate(' input[name="start_date"], input[name="end_date"], input[name="rate"]', 'change keyup', function () {


                                    var from_date = modal.find('input[name="start_date"]').val();
                                    var to_date = modal.find('input[name="end_date"]').val();
                                    var rate = modal.find('input[name="rate"]').unmask();
                                    var amount_field = modal.find('input[name="amount"]');

                                    if (from_date.trim() != '' && to_date.trim() != '') {
                                        function parseDate(str) {
                                            var mdy = str.split('-');
                                            return new Date(mdy[0], mdy[1] - 1, mdy[2]);
                                        }

                                        function daydiff(first, second) {
                                            return Math.round((second - first) / (1000 * 60 * 60 * 24));
                                        }

                                        var duration = daydiff(parseDate(from_date), parseDate(to_date));


                                    } else {
                                        var duration = 0;
                                    }

                                    if (rate != '') {
                                        var amount = duration * rate;
                                    } else {
                                        amount = 0;
                                    }

                                    amount_field.val(amount).priceFormat();

                                });

                                $(this).attr('initialized', 'true');
                            }
                        });


                    });


                    $(this).find('tr').each(function () {
                        $(this).find('td:last-child').attr('nowrap', 'nowrap');
                    });

                    initialize_common_js();
                }

            });
            table.attr('initialized','true');

        } else {
            table.DataTable().draw('page');
        }

    });

    // Hired_equipment_cost_tab
    $('.hired_equipment_cost_tab').each(function(){

        var table=$(this);

        var project_id=table.attr('project_id');

        //var task_id=table.closest('.box').find("select[name='cost_center_selector']").val();

        if(table.attr('initialized') != 'true') {
            table.DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: base_url + "Costs/hired_equipment_cost_list/" + project_id,
                    type: 'POST',
                    'data': function (d) {
                        d.task_id = table.closest('.box').find("select[name='cost_center_selector']").val();
                    }
                },
                "columns": [
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": false}
                ],
                "language": {
                    "zeroRecords": "<div class='alert alert-info'>No matching Equipment Cost found</div>",
                    "emptyTable": "<div class='alert alert-info'>No Equipment Cost found</div>"
                },

                "drawCallback": function (settings) {

                    table.find('#total_cost_amount_display').text(settings.json.cost_total).priceFormat();

                    //Save Equipment Cost

                    $('.save_hired_equipment_cost_btn').each(function () {

                        var button = $(this);
                        if (button.attr('initialized') != 'true') {
                            button.click(function () {
                                var modal = button.closest('.modal');
                                var project_id = modal.find("input[name='project_id']").val();
                                var hired_equipment_cost_id = modal.find("input[name='hired_equipment_cost_id']").val();
                                var cost_center_id = modal.find("select[name='cost_center_id']").val();
                                var start_date = modal.find("input[name='start_date']").val();
                                var end_date = modal.find("input[name='end_date']").val();
                                var hired_equipment_id = modal.find("select[name='hired_equipment_id']").val();
                                var rate_mode = modal.find("select[name='rate_mode']").val();
                                var rate = modal.find("input[name='rate']").unmask();
                                var description = modal.find("textarea[name='description']").val();

                                console.log(project_id, hired_equipment_cost_id, cost_center_id, start_date, end_date, hired_equipment_id,
                                    rate_mode, rate, description);
                                if (hired_equipment_id != '') {
                                    start_spinner();
                                    modal.modal('hide');
                                    $.post(
                                        base_url + "Costs/save_hired_equipment_cost",
                                        {
                                            project_id: project_id,
                                            hired_equipment_cost_id: hired_equipment_cost_id,
                                            task_id: cost_center_id,
                                            start_date: start_date,
                                            end_date: end_date,
                                            hired_equipment_id: hired_equipment_id,
                                            rate_mode: rate_mode,
                                            rate: rate,
                                            description: description

                                        }, function () {
                                            stop_spinner();
                                            modal.find('form')[0].reset();
                                            $('.hired_equipment_cost_tab').DataTable().draw('page');
                                            toast('success', 'Equipment Cost Added successful ');
                                        }
                                    );
                                } else {
                                    toast('warning', 'Equipment Name Must be filled ');
                                }
                            });
                            button.attr('initialized', 'true');
                        }
                    });


                    //DeleteEquipment cost
                    $('.delete_hired_equipment_cost').each(function () {
                        var button = $(this);
                        if (button.attr('active') != 'true') {
                            button.click(function () {
                                if (confirm('Are you sure?')) {
                                    start_spinner();
                                    $.post(
                                        base_url + "Costs/delete_hired_equipment_cost",
                                        {
                                            hired_equipment_cost_id: button.attr('hired_equipment_cost_id')
                                        }, function () {
                                            $('.hired_equipment_cost_tab').DataTable().draw('page');
                                        }
                                    ).complete(function () {
                                        stop_spinner();
                                    });
                                }
                            });
                            button.attr('active', 'true');
                        }
                    });

                    $('.hired_equipment_cost_form').each(function () {

                        var modal = $(this);

                        modal.on('change', '.asset_group_selector',
                            function () {
                                var asset_group_id = modal.find('select[name="asset_group_id"]').val();
                                modal.find('input[name="amount"]').val(asset_group_id);
                                var equipment_option_selector = modal.find('select[name="hired_equipment_id"]');

                                start_spinner();
                                $.post(
                                    base_url + "Projects/load_equipments_options",
                                    {
                                        asset_group_id: asset_group_id

                                    }, function (data) {

                                        equipment_option_selector.html(data.asset_dropdown_options);

                                        stop_spinner();

                                        initialize_common_js();

                                    }, 'json'
                                ).complete();

                            });

                        modal.find('input[name="start_date"], input[name="end_date"], input[name="amount"]').each(function () {


                            if ($(this).attr('initialized') != 'true') {

                                modal.delegate(' input[name="start_date"], input[name="end_date"], input[name="rate"]', 'change keyup', function () {


                                    var from_date = modal.find('input[name="start_date"]').val();
                                    var to_date = modal.find('input[name="end_date"]').val();
                                    var rate = modal.find('input[name="rate"]').unmask();
                                    var amount_field = modal.find('input[name="amount"]');

                                    if (from_date.trim() != '' && to_date.trim() != '') {
                                        function parseDate(str) {
                                            var mdy = str.split('-');
                                            return new Date(mdy[0], mdy[1] - 1, mdy[2]);
                                        }

                                        function daydiff(first, second) {
                                            return Math.round((second - first) / (1000 * 60 * 60 * 24));
                                        }

                                        var duration = daydiff(parseDate(from_date), parseDate(to_date));


                                    } else {
                                        var duration = 0;
                                    }

                                    if (rate != '') {
                                        var amount = duration * rate;
                                    } else {
                                        amount = 0;
                                    }

                                    amount_field.val(amount).priceFormat();

                                });

                                $(this).attr('initialized', 'true');
                            }
                        });


                    });


                    $(this).find('tr').each(function () {
                        $(this).find('td:last-child').attr('nowrap', 'nowrap');
                    });

                    initialize_common_js();
                }

            });

            table.attr('initialized','true');
        } else {
            table.DataTable().draw('page');
        }

    });
});

$('a[href="#project_store"]').on('shown.bs.tab', function (e){
    start_spinner();
    var project_id = $(this).attr('project_id');
    $.post(
        base_url + "projects/project_store",
        {
            project_id : project_id
        }, function (data) {
            $('#project_store').html(data);
            initialize_location_material_stock();
            initialize_location_tools_and_equipment();
        }
    ).complete(function () {
        stop_spinner();
        initialize_common_js();
    });
});

$('a[href="#project_activities"], a[href="#task_wise_budgeting_tab"]').on('shown.bs.tab', function (e){
    load_project_activities();
});

$('a[href="#project_team"]').on('shown.bs.tab', function (e){
    $('#project_team').find('#project_team_members_table').each(function(){
        if($(this).attr('dataTable_initialized') != 'true') {
            var project_id = $(this).attr('project_id');
            $(this).DataTable({
                colReorder: true,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: base_url + "projects/project_team_members/",
                    type: 'POST',
                    data: {
                        project_id : project_id
                    }
                },
                "columns": [
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": false}
                ],
                "language": {
                    "zeroRecords": "<div class='alert alert-info'>No matching project team members found</div>",
                    "emptyTable": "<div class='alert alert-info'>No project team members found for this project</div>"
                },
                "drawCallback": function () {

                    //Initialize team member form
                    $('#new_project_team_member').on('show.bs.modal', function (e) {
                        if (e.namespace === 'bs.modal') {
                            var select_element = $(this).find('select[name="employee_id"]');
                            var project_id = $(this).find('input[name="project_id"]').val();
                            start_spinner();
                            $.post(
                                base_url + "projects/project_team_member_employees_options",
                                {
                                    project_id : project_id
                                }, function (data) {
                                    select_element.html(data);
                                }
                            ).complete(function () {
                                stop_spinner()
                            });
                        }
                    });

                    //Save Project Team Member

                    $('.save_project_team_member').off('click').on('click',function(){
                        var button = $(this);
                        var modal = button.closest('.modal');
                        var employee_id = modal.find('select[name="employee_id"]').val();
                        var job_position_id = modal.find('select[name="job_position_id"]').val();
                        if(employee_id != '' && job_position_id != '') {
                            modal.modal('hide');
                            start_spinner();
                            var member_id = modal.find('input[name="member_id"]').val();
                            var project_id = modal.find('input[name="project_id"]').val();
                            var date_assigned = modal.find('input[name="date_assigned"]').val();
                            var remarks = modal.find('textarea[name="remarks"]').val();
                            var manager_access = modal.find(' input[name="manager_access"]').is(":checked") ? 1 : 0;

                            $.post(
                                base_url + "projects/save_project_team_member/",
                                {
                                    member_id: member_id,
                                    project_id: project_id,
                                    employee_id: employee_id,
                                    manager_access: manager_access,
                                    date_assigned: date_assigned,
                                    job_position_id: job_position_id,
                                    remarks: remarks
                                }, function () {
                                    stop_spinner();
                                }
                            ).complete(function () {
                                modal.find('form')[0].reset();
                                $('#project_team_members_table').DataTable().draw('page');
                            });
                        } else {
                            display_form_fields_error();
                        }
                    });

                    //Delete Project Team Member

                    $('.delete_project_team_member').each(function () {
                        var button = $(this);
                        if(button.attr('initialized') != 'true'){
                            var member_id = button.attr('member_id');
                            button.click(function () {
                                if(confirm('Are you sure?')){
                                    $.post(
                                        base_url + "projects/delete_project_team_member",
                                        {
                                            member_id : member_id
                                        }, function () {
                                            $('#project_team_members_table').DataTable().draw('page');
                                        }
                                    );
                                }
                            });
                            button.attr('initialized','true');
                        }
                    });

                    $(this).find('tr').each(function () {
                        $(this).find('td:last-child').attr('nowrap', 'nowrap');
                    });
                    initialize_common_js();
                }
            });
            $(this).attr('dataTable_initialized','true');
        } else {
            $(this).DataTable().draw('page');
        }
    });
});

$('a[href="#project_reports"]').on('shown.bs.tab', function (e){
    $('#generate_project_report').off('click').on('click',function () {
        start_spinner();
        var form = $(this).closest('form');
        $.post(
            base_url + "projects/reports",
            {
                report_type : form.find('select[name="report_type"]').val(),
                project_id : form.find('input[name="project_id"]').val(),
                from:form.find('input[name="from"]').val(),
                to:form.find('input[name="to"]').val()
            }, function (data) {
                $('#project_report_container').html(data);
            }
        ).complete(function () {
            stop_spinner();
        });
    });
});

$('a[href="#project_cash_book"]').on('shown.bs.tab', function (e){
    var tab_container = $($(this).attr('href'));
    var petty_cash_account_id = $(this).attr('petty_cash_account_id');

    if(tab_container.html().trim() == '') {
        start_spinner();
        $.post(
            base_url + "finance/load_project_cashbook/",
            {
                petty_cash_account_id: petty_cash_account_id
            }, function (data) {
                tab_container.html(data);
                var tab_pane = tab_container.find('#account_statement');
                load_account_statement_transactions(tab_pane);
                tab_pane.find(' input[name="from"],  input[name="to"]').change(function() {
                    load_account_statement_transactions(tab_pane);
                });
                initialize_common_js();
            }
        ).complete(function () {
            stop_spinner()
        });
    }
});

$('#project_categories_list').DataTable({
    colReorder: true,
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "projects/project_categories_list/",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching categories found</div>",
        "emptyTable":     "<div class='alert alert-info'>No categories found</div>"
    },"drawCallback": function () {
        //Save Category
        $('.save_project_category_button').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                var modal = button.closest('.modal');
                var category_id = modal.find('input[name="category_id"]').val();

                button.click(function (){
                    var category_name = modal.find('input[name="category_name"]').val();

                    if(category_name != '') {
                        modal.modal('hide');
                        var description = modal.find('textarea[name="description"]').val();

                        $.post(
                            base_url + "projects/save_project_category",
                            {
                                category_id: category_id,
                                category_name: category_name,
                                description: description
                            }, function () {
                                modal.find('form')[0].reset();
                                $('#project_categories_list').DataTable().draw('page');
                            }
                        );
                    }
                });
                button.attr('active','true');
            }
        });

        //Delete Category
        $('.delete_project_category').each(function () {
            var button = $(this);
            var category_id = button.attr('category_id');
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    if(confirm('Are you sure?')) {
                        $.post(
                            base_url + "projects/delete_project_category/",
                            {
                                category_id: category_id
                            }
                        ).complete(function () {
                            $('#project_categories_list').DataTable().draw('page');
                        });
                    }
                });
            }
        });

        initialize_common_js();
    }
});

function upload_project_excel(button){
    var form = button.closest('form');
    var file_field = form.find('input[name="activities_excel"]');
    var excel_type = button.attr('excel_type');
    var captured = file_field[0];
    var project_id = file_field.attr('project_id');
    var file = captured.files[0], form_data = false;
    var path = base_url;
    if(excel_type == 'activities'){
        path += 'projects/upload_activities_excel/'
    } else {
        path += 'budgets/upload_material_budget_excel/'
    }

    if (window.FormData) {
        button.attr('disabled','disabled');
        start_spinner();
        form_data = new FormData();
        if (form_data) {
            form_data.append("file", file);
            form_data.append("project_id", project_id);

            $.ajax({
                url: path,
                type: "POST",
                timeout: 250000,
                cache: false,
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data){
                    if(excel_type == 'activities') {
                        if (parseFloat(data) > 0) {
                            form.hide();
                            form.closest('.box-tools').find('.search_container').show();
                        }
                    }
                },
                complete: function () {
                    form[0].reset();
                    button.removeAttr('disabled');
                    stop_spinner();
                    if(excel_type == 'activities') {
                        load_project_activities();
                    } else {
                        form[0].reset();
                        var table = $(button).closest('.box').find('.material_budget_items');
                        table.DataTable().draw('page');
                        stop_spinner();
                    }
                }
            });
        }
    }
}

function load_project_activities() {
    start_spinner();
    var activities_container = $('#activities_container');
    var project_id = activities_container.attr('project_id');
    var keyword = $('#activity_keyword').val();
    $.post(
        base_url + "projects/project_activities_list/",
        {
            keyword : keyword,
            project_id : project_id
        }, function (data) {
            activities_container.html(data);

            //Initialize internal tabs
            var initialize_activity_tab_load = function (){
                $('.activity_summary_activator').on('shown.bs.tab', function (e){
                    start_spinner();
                    var summary_container = $($(this).attr('href'));
                    var activity_id = summary_container.attr('activity_id');
                    var project_activity = $(this).attr('project_activity');

                    $.post(
                        base_url + "projects/activity_summary/",
                        {
                            project_activity : project_activity,
                            activity_id: activity_id
                        }, function (data) {
                            summary_container.html(data);
                            initialize_common_js();
                            stop_spinner();
                        }
                    );

                });

                $('.activity_tasks_activator').on('shown.bs.tab', function (e){
                    var project = $(this).attr('project') == 'true';
                    var tasks_table = $(this).closest('.nav-tabs-custom').find('.activity_tasks_table');
                    var activity_id = tasks_table.attr('activity_id');
                    if(tasks_table.attr('dataTable_initialized') != 'true') {
                        tasks_table.DataTable({
                            order: [[ 1, "asc" ]],
                            colReorder: true,
                            "processing": true,
                            "serverSide": true,
                            "ajax": {
                                url: base_url + "tasks/activity_tasks_list/" + activity_id+'/'+project,
                                type: 'POST'
                            },
                            "columns": [
                                {"orderable": true},
                                {"orderable": true},
                                {"orderable": true},
                                {"orderable": true},
                                {"orderable": false},
                                {"orderable": false},
                                {"orderable": true},
                                {"orderable": false}
                            ],
                            "language": {
                                "zeroRecords": "<div class='alert alert-info'>No matching tasks found</div>",
                                "emptyTable": "<div class='alert alert-info'>No tasks found</div>"
                            },"drawCallback": function () {

                                //Save Task
                                var save_task = function (button){
                                    var modal = button.closest('.modal');
                                    var task_name = modal.find('input[name="task_name"]').val();
                                    if(task_name != '') {
                                        modal.modal('hide');
                                        var task_id = modal.find('input[name="task_id"]').val();
                                        var activity_id = modal.find('input[name="activity_id"]').val();
                                        var weight_percentage = modal.find('input[name="weight_percentage"]').val();
                                        var start_date = modal.find('input[name="start_date"]').val();
                                        var end_date = modal.find('input[name="end_date"]').val();
                                        var contract_sum = modal.find('input[name="contract_sum"]').unmask();
                                        var description = modal.find('textarea[name="description"]').val();

                                        $.post(
                                            base_url + "tasks/save_task/",
                                            {
                                                task_id: task_id,
                                                activity_id: activity_id,
                                                task_name: task_name,
                                                start_date: start_date,
                                                end_date: end_date,
                                                contract_sum: contract_sum,
                                                weight_percentage: weight_percentage,
                                                description: description
                                            }, function () {
                                                modal.find('form')[0].reset();
                                                button.closest('.nav-tabs-custom').find('.activity_tasks_table').DataTable().draw('page');
                                            }
                                        );
                                    }
                                };

                                $('.save_task').each(function(){
                                    var button = $(this);
                                    if(button.attr('active') != 'true') {
                                        button.click(function(){
                                            save_task(button);
                                        });
                                        button.attr('active','true');
                                    }
                                });

                                //Task tab Actions
                                var initialize_tasks_tab_actions = function (){

                                    $('.task_summary_activator').each(function(){
                                        if($(this).attr('initialized') != 'true'){
                                            $(this).on('shown.bs.tab', function (e){
                                                start_spinner();
                                                var task_id = $(this).attr('task_id');
                                                var container = $($(this).attr('href'));
                                                $.post(
                                                    base_url + "tasks/load_task_summary",
                                                    {
                                                        task_id:task_id
                                                    }, function (data) {
                                                        container.html(data);
                                                    }
                                                ).complete(function(){
                                                    stop_spinner();
                                                });
                                            });
                                            $(this).attr('initialized','true');
                                        }
                                    });
                                    initialize_common_js();
                                };

                                //Delete Task
                                $('.delete_task').each(function () {
                                    var button = $(this);
                                    if (button.attr('initialized') != 'true') {
                                        button.click(function () {
                                            var task_id = button.attr('task_id');
                                            if (confirm('Are you sure?')) {
                                                $.post(
                                                    base_url + "tasks/delete_task",
                                                    {
                                                        task_id: task_id
                                                    }
                                                ).complete(function () {
                                                    button.closest('.activity_tasks_table').DataTable().draw('page');
                                                });
                                            }
                                        });
                                        button.attr('initialized', 'true');
                                    }
                                });

                                initialize_tasks_tab_actions();
                                tasks_table.find('tr').each(function(){
                                    $(this).find('td:last-child').attr('nowrap', 'nowrap');
                                    initialize_task_progress_loader();
                                });
                            }
                        });
                        tasks_table.attr('dataTable_initialized','true');
                    } else {
                        tasks_table.DataTable().draw('page');
                    }
                });

                initialize_common_js();
            };

            initialize_activity_tab_load();

            //Save Activity
            var save_activity = function (button){
                var modal = button.closest('.modal');
                var activity_name = modal.find('input[name="activity_name"]').val();
                if(activity_name != '') {
                    start_spinner();
                    modal.modal('hide');
                    var project_id = modal.find('input[name="project_id"]').val();
                    var activity_id = modal.find('input[name="activity_id"]').val();
                    var activity_name = modal.find('input[name="activity_name"]').val();
                    var weight_percentage = modal.find('input[name="weight_percentage"]').val();
                    var description = modal.find('textarea[name="description"]').val();

                    $.post(
                        base_url + "projects/save_activity/",
                        {
                            activity_id: activity_id,
                            project_id: project_id,
                            activity_name: activity_name,
                            weight_percentage: weight_percentage,
                            description: description
                        }, function () {
                            modal.find('form')[0].reset();
                            setTimeout(load_project_activities, 500)
                        }
                    ).complete(function(){
                        stop_spinner();
                    });
                }
            };

            $('.save_activity').each(function(){
                var button = $(this);
                if(button.attr('active') != 'true') {
                    button.click(function(){
                        save_activity(button);
                    });
                    button.attr('active','true');
                }
            });

            //Delete Activity
            $('.delete_activity').each(function () {
                var button = $(this);
                if(button.attr('initialized') != 'true'){
                    var activity_id = button.attr('activity_id');
                    button.click(function () {
                        if(confirm('Are you sure?')) {
                            $.post(
                                base_url + "projects/delete_activity",
                                {
                                    activity_id: activity_id
                                }
                            ).complete(function(){
                                load_project_activities();
                            });
                        }
                    });
                    button.attr('initialized','true');
                }
            });

            initialize_common_js();
            stop_spinner();
        }
    );
}

/***************************************************
 * TASKS
 ***************************************************/

function save_task_progress_update(button){
    var modal = button.closest('.modal');
    var task_id = modal.find('input[name="task_id"]').val();
    var update_id = modal.find('input[name="progress_update_id"]').val();
    var description = modal.find('textarea[name="description"]').val();
    var percentage = modal.find('input[name="percentage"]').val();
    var datetime = modal.find('input[name="datetime"]').val();
    if(task_id != '' && percentage != '' && datetime != '') {
        modal.modal('hide');

        $.post(
            base_url + "tasks/save_task_progress/",
            {
                update_id: update_id,
                task_id: task_id,
                datetime: datetime,
                description: description,
                percentage: percentage
            }, function (data) {
                modal.find('form')[0].reset();
                var graph_container = modal.closest('.box').find('.task_progress_graphical_container');
                var list_container = modal.closest('.box').find('.task_progress_list');
                load_task_progress_graph(graph_container);
                load_task_progress_list(list_container);
            }
        );
    }
}

function delete_task_progress_update(button){
    if(confirm('Are you sure?')){
        var update_id = button.attr('update_id');
        $.post(
            base_url + "tasks/delete_task_progress_update",
            {
                update_id:update_id
            }, function (data) {
                button.closest('table').DataTable().draw('page');
            }
        ).complete();
    }
}

function initialize_task_progress_loader(){

    $('.task_progress_activator').on('shown.bs.tab', function (e){

        var progress_tab = $($(this).attr('href'));

        if($(this).attr('initialized') != 'true'){
            progress_tab.find('.task_progress_graphical_container').each(function () {
                load_task_progress_graph($(this));
            });
            $(this).attr('initialized','true');
        }

        progress_tab.find('.task_progress_graphical_activator').each(function(){
            if($(this).attr('initialized') != 'true'){
                $(this).on('shown.bs.tab', function (e){
                    load_task_progress_graph($($(this).attr('href')));
                });
                $(this).attr('initialized','true');
            }
        });

        progress_tab.find('.task_progress_list_activator').each(function(){
            if($(this).attr('initialized') != 'true'){
                $(this).on('shown.bs.tab', function (e){
                    load_task_progress_list($($(this).attr('href')).find('.task_progress_list'));
                });
                $(this).attr('initialized','true');
            }
        });
    });
}

function load_task_progress_graph(graph_container){
    start_spinner();
    var task_id = graph_container.attr('task_id');
    $.post(
        base_url + "tasks/task_progress_graph_values",
        {
            task_id : task_id
        }, function (data) {
            stop_spinner();
            graph_container.highcharts({
                chart: {
                    type: 'spline'
                },
                title: {
                    text: 'Task Progress'
                },
                subtitle: {
                    text: ''
                },
                xAxis: {
                    type: 'datetime',
                    dateTimeLabelFormats: { // don't display the dummy year
                        month: '%e. %b',
                        year: '%b'
                    },
                    title: {
                        text: 'Date'
                    }
                },
                yAxis: {
                    title: {
                        text: 'Percentage(%)'
                    },
                    min: 0,
                    max: 100
                },
                tooltip: {
                    headerFormat: '<b>{series.name}</b><br>',
                    pointFormat: '{point.x:%e. %b}: {point.y:.1f} %'
                },

                plotOptions: {
                    spline: {
                        marker: {
                            enabled: true
                        }
                    }
                },

                series: [{
                    name: 'PROGRESS SPLINE',
                    // Define the data points. All series have a dummy year
                    // of 1970/71 in order to be compared on the same x axis. Note
                    // that in JavaScript, months start at 0 for January, 1 for February etc.
                    data: data.data
                }]
            });
        },
        'JSON'
    );
}

function load_task_progress_list(list_table){
    var task_id = list_table.attr('task_id');
    if(list_table.attr('initialized') != 'true') {
        list_table.DataTable({
            "order": [[0, "desc"]],
            colReorder: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: base_url + "tasks/task_progress_updates_list/"+task_id,
                type: 'POST'
            },
            "columns": [
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": false}
            ],
            "language": {
                "zeroRecords": "<div class='alert alert-info'>No matching progress updates found</div>",
                "emptyTable": "<div class='alert alert-info'>No progress updates found</div>"
            }, "drawCallback": function () {
                $('.delete_task_progress_update').click(function(){
                    delete_task_progress_update($(this));
                });
                initialize_common_js();
            }
        });
        list_table.attr('initialized','true');
    } else {
        list_table.DataTable().draw('page');
    }
}

/***************************************************
 * INVENTORY
 ***************************************************/

$('#locations_list').DataTable({
    colReorder: true,
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "inventory/locations/",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching locations found</div>",
        "emptyTable":     "<div class='alert alert-info'>No locations found</div>"
    },"drawCallback": function () {
        $('#locations_list .delete_location_button').click(function(){
            delete_location($(this));
        });
    }
});

function delete_location(button){
    if(confirm('Are you sure?')) {
        var location_id = button.attr('location_id');
        $.post(
            base_url + "inventory/delete_location",
            {
                location_id: location_id
            }, function () {
                $('#locations_list').DataTable().draw('page');
            }
        );
    }
}

function load_location_sub_locations(){
    start_spinner();
    var sub_locations_container = $('#sub_locations_container');
    var location_id = sub_locations_container.attr('location_id');
    var keyword = $('#sub_location_keyword').val();
    $.post(
        base_url + "inventory/sub_locations_list/",
        {
            keyword : keyword,
            location_id : location_id
        }, function (data) {
            sub_locations_container.html(data);
            initialize_opening_stock_form();
            initialize_common_js();
            stop_spinner();
        }
    );
}

function save_sub_location(button){
    var modal = button.closest('.modal');
    var sub_location_name = modal.find('input[name="sub_location_name"]').val();
    if(sub_location_name != '') {
        modal.modal('hide');
        var sub_location_id = modal.find('input[name="sub_location_id"]').val();
        var location_id = modal.find('input[name="location_id"]').val();
        var description = modal.find('textarea[name="description"]').val();

        $.post(
            base_url + "inventory/save_sub_location/",
            {
                sub_location_id: sub_location_id,
                location_id: location_id,
                sub_location_name: sub_location_name,
                description: description
            }, function () {
                modal.find('form')[0].reset();
                load_location_sub_locations();
            }
        );
    }
}

function delete_sub_location(sub_location_id){
    if(confirm('Are you sure?')){
        $.post(
            base_url + "inventory/delete_sub_location",
            {
                sub_location_id : sub_location_id
            }, function () {
                load_location_sub_locations();
            }
        );
    }
}

function load_material_unit(select_element, container_element){
    var material_id = select_element.val();
    var display_portion = select_element.closest(container_element).find('.unit_display');
    if(material_id != '') {
        start_spinner();
        $.post(
            base_url + "inventory/load_material_unit",
            {
                material_id: material_id
            }, function (data) {
                display_portion.html(data);
            }
        ).complete(stop_spinner());
    } else {
        display_portion.html('');
    }
}

$('#material_items_list').DataTable({
    "order": [[ 1, "asc" ]],
    colReorder: true,
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "inventory/material_items/",
        type: 'POST',
        'data' :function ( d ) {
            d.category_id = $('#filter_by_category').val();
        }
    },
    "columns": [
        {"orderable": false},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching items found</div>",
        "emptyTable":     "<div class='alert alert-info'>No items found</div>"
    },"drawCallback": function () {
        //Make the last column with button not to break
        var table = $(this);
        table.find('tr').each(function(){
            $(this).find('td:last-child').attr('nowrap', 'nowrap');
        });

        $('.upload_material_registration_excel').each(function () {
            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    var form = button.closest('form');
                    var file_field = form.find('input[name="material_registration_excel"]');
                    var captured = file_field[0];
                    var project_id = file_field.attr('project_id');
                    var file = captured.files[0], form_data = false;
                    var path = base_url + 'inventory/upload_material_registration_excel/';
                    if (window.FormData) {
                        button.attr('disabled','disabled');
                        start_spinner();
                        form_data = new FormData();
                        if (form_data) {
                            form_data.append("file", file);
                            form_data.append("project_id", project_id);

                            $.ajax({
                                url: path,
                                type: "POST",
                                timeout: 250000,
                                cache: false,
                                data: form_data,
                                processData: false,
                                contentType: false,
                                success: function(data){
                                    table.DataTable().draw('page');
                                },
                                complete: function () {
                                    button.removeAttr('disabled');
                                    form[0].reset();
                                    stop_spinner();
                                }
                            });
                        }
                    }
                });

                button.attr('initialized','true');
            }
        });

        //Activate nature filter
        $('#filter_by_nature').each(function () {
            var select_field = $(this);
            if(select_field.attr('initialized') != 'true'){
                select_field.change(function () {
                    start_spinner();
                    $.post(
                        base_url + "inventory/load_material_item_categories_options",
                        {
                            project_nature_id : select_field.val()
                        }, function (data) {
                            select_field.closest('form').find('#filter_by_category').html(data).trigger("change");
                            stop_spinner();
                        }
                    );
                });
                select_field.attr('initialized','true');
            }
        });

        //Activate category filter
        $('#filter_by_category').each(function () {
            var select_field = $(this);
            if(select_field.attr('initialized') != 'true'){
                select_field.change(function () {
                    $('#material_items_list').DataTable().draw();
                });
                select_field.attr('initialized','true');
            }
        });
        initialize_common_js();

        //Save Button
        var save_material_item = function (button){
            var modal = button.closest('.modal');
            var item_name = modal.find('input[name="item_name"]').val();
            var unit_id = modal.find('select[name="unit_id"]').val();
            if(item_name != '' && unit_id != '') {
                modal.modal('hide');
                start_spinner();
                var category_id = modal.find('select[name="category_id"]').val();
                var item_id = modal.find('input[name="item_id"]').val();
                var part_number = modal.find('input[name="part_number"]').val();
                var description = modal.find('textarea[name="description"]').val();
                var captured = modal.find('input[name="image"]')[0];
                var file = captured.files[0], form_data = false;

                if (window.FormData) {
                    form_data = new FormData();
                    if (form_data) {
                        form_data.append("file", file);
                        form_data.append("item_id", item_id);
                        form_data.append("item_name", item_name);
                        form_data.append("part_number", part_number);
                        form_data.append("unit_id", unit_id);
                        form_data.append("category_id", category_id);
                        form_data.append("description", description);

                        $.ajax({
                            url: base_url + 'inventory/save_material_item/',
                            type: "POST",
                            timeout: 250000,
                            cache: false,
                            data: form_data,
                            processData: false,
                            contentType: false,
                            success: function () {
                                $('#material_items_list').DataTable().draw('page');
                                modal.find('form')[0].reset();
                                stop_spinner();
                            }
                        });
                    }
                }
            }
        }

        $('.save_material_item_button').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function(){
                    save_material_item(button);
                });
                button.attr('active','true');
            }
        });

        //Delete
        $('.delete_material_item').each(function () {
            var button = $(this);
            var item_id = button.attr('item_id');
            if(button.attr('initialized') != 'true') {
                button.click(function () {
                    if (confirm('Are you sure?')) {
                        $.post(
                            base_url + "inventory/delete_material_item/",
                            {
                                item_id: item_id
                            }
                        ).complete(function () {
                            $('#material_items_list').DataTable().draw('page');
                        });
                    }
                });
                button.attr('initialized','true');
            }
        });

    }
});

$('#material_item_categories_list').DataTable({
    colReorder: true,
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "inventory/material_item_categories/",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": false},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching categories found</div>",
        "emptyTable":     "<div class='alert alert-info'>No categories found</div>"
    },"drawCallback": function () {
        //Save Category

        $('.save_material_item_category_button').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                var modal = button.closest('.modal');
                var category_id = modal.find('input[name="category_id"]').val();
                var parent_field = modal.find('select[name="parent_category_id"]');


                if(category_id.trim() != ''){
                    var get_accessible_parents = function () {
                        $.post(
                            base_url + "inventory/get_accessible_parent_categories_options",
                            {
                                category_id : category_id
                            }, function (data) {
                                parent_field.html(data);
                                var current_parent_category_id = modal.find('input[name="current_parent_category_id"]').val();
                                parent_field.val(current_parent_category_id).trigger("change");
                            }
                        );
                    }

                    modal.on('show.bs.modal', get_accessible_parents);
                }

                var save_material_item_category = function (){
                    var category_name = modal.find('input[name="category_name"]').val();
                    var parent_category_id = parent_field.val();

                    if(category_name != '' && ((parent_category_id != category_id && category_id != '') || category_id == '') && parent_category_id != '') {
                        modal.modal('hide');
                        var description = modal.find('textarea[name="description"]').val();

                        $.post(
                            base_url + "inventory/save_material_item_category/",
                            {
                                category_id: category_id,
                                parent_category_id: parent_category_id,
                                category_name: category_name,
                                description: description
                            }, function () {
                                modal.find('form')[0].reset();
                                $('#material_item_categories_list').DataTable().draw('page');
                            }
                        );
                    }
                };


                button.click(save_material_item_category);
                button.attr('active','true');
            }
        });

        //Delete Category
        $('.delete_material_item_category').each(function () {
            var button = $(this);
            var category_id = button.attr('category_id');
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    if(confirm('Are you sure?')) {
                        $.post(
                            base_url + "inventory/delete_material_item_category/",
                            {
                                category_id: category_id
                            }
                        ).complete(function () {
                            $('#material_item_categories_list').DataTable().draw('page');
                        });
                    }
                });
            }
        });


        initialize_common_js();
    }
});

$('#measurement_units_list').DataTable({
    colReorder: true,
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "inventory/measurement_units/",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching units found</div>",
        "emptyTable":     "<div class='alert alert-info'>No units found</div>"
    },"drawCallback": function () {
        //Save Measurement Unit

        var save_measurement_unit = function (button){
            var modal = button.closest('.modal');
            var name = modal.find('input[name="name"]').val();
            var symbol = modal.find('input[name="symbol"]').val();
            if(name != '' && symbol != '') {
                modal.modal('hide');
                var unit_id = modal.find('input[name="unit_id"]').val();
                var description = modal.find('textarea[name="description"]').val();

                $.post(
                    base_url + "inventory/save_measurement_unit/",
                    {
                        unit_id: unit_id,
                        name: name,
                        symbol: symbol,
                        description: description
                    }, function () {
                        modal.find('form')[0].reset();
                        $('#measurement_units_list').DataTable().draw('page');
                    }
                );
            }
        };


        $('.save_measurement_unit_button').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function(){
                    save_measurement_unit(button);
                });
                button.attr('active','true');
            }
        });

        $('.delete_measurement_unit').each(function () {
            var button = $(this);
            var unit_id = button.attr('unit_id');
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    if(confirm('Are you sure?')) {
                        $.post(
                            base_url + "inventory/delete_measurement_unit/",
                            {
                                unit_id: unit_id
                            }
                        ).complete(function () {
                            $('#measurement_units_list').DataTable().draw('page');
                        });
                    }
                });
                button.attr('initialized','true');
            }
        });

        initialize_common_js();
    }
});

/********************************************************
 * COSTS
 ********************************************************/

function load_material_average_price(selector,container,project_id){
    start_spinner();
    var material_id = selector.val();
    var form_container = selector.closest(container);
    var rate_field = form_container.find('input[name="rate"]');

    if(project_id == 'undefined' ) {
        project_id = container == 'form' ? form_container.find('input[name="project_id"]').val() : form_container.find('select[name="project_id"]').val();
    }

    if(material_id != '' && project_id != ''){
        var sub_location_id = form_container.find('select[name="source_sub_location_id"]').val();
        $.post(
            base_url + "inventory/load_material_average_price",
            {
                project_id : project_id,
                material_id : material_id,
                sub_location_id: sub_location_id
            }, function (data) {
                rate_field.val(parseFloat(data));
                if(container == 'form'){
                    rate_field.priceFormat();
                }
                stop_spinner();
            }
        ).complete();
    } else {
        rate_field.val('');
    }
}

function save_project_miscellaneous_cost(button){
    var modal = button.closest('.modal');
    var budget_id = modal.find('select[name="budget_id"]').val();
    var cost_id = modal.find('input[name="cost_id"]').val();
    var cost_item_name = modal.find('input[name="cost_item_name"]').val();
    var description = modal.find('textarea[name="description"]').val();
    var quantity = modal.find('input[name="quantity"]').val();
    var rate = modal.find('input[name="rate"]').unmask();
    var cost_date = modal.find('input[name="cost_date"]').val();
    if(budget_id != '' && quantity != '' && cost_date != '') {
        modal.modal('hide');

        $.post(
            base_url + "costs/save_project_miscellaneous_cost/",
            {
                cost_item_name : cost_item_name,
                budget_id: budget_id,
                cost_date: cost_date,
                description: description,
                quantity: quantity,
                rate: rate,
                cost_id: cost_id
            }, function (data) {
                modal.find('form')[0].reset();
                button.closest('.box').find('.general_miscellaneous_costs_list').DataTable().draw('page');
            }
        );
    }
}

function delete_material_cost(button){
    if(confirm('Are you sure?')){
        var cost_center = button.attr('cost_center');
        var cost_id = button.attr('cost_id');
        start_spinner();
        $.post(
            base_url + "costs/delete_material_cost",
            {
                cost_center: cost_center,
                cost_id: cost_id,
            }, function (data) {
                button.closest('table').DataTable().draw('page');
            }
        ).complete(function(){
            stop_spinner();
        });
    }
}

function delete_miscellaneous_cost(button){
    if(confirm('Are you sure?')){
        var cost_id = button.attr('cost_id');
        start_spinner();
        $.post(
            base_url + "costs/delete_miscellaneous_cost",
            {
                cost_id: cost_id
            }, function (data) {
                button.closest('table').DataTable().draw('page');
            }
        ).complete(function(){
            stop_spinner();
        });
    }
}

function save_bulk_material_cost(button){

    var modal = button.closest('.modal');
    var project_id = modal.find('input[name="project_id"]').val();
    var source_sub_location_id = modal.find('input[name="source_sub_location_id"]').val();
    var date = modal.find('input[name="date"]').val();
    var form_cost_center_selector = modal.find('select[name="cost_center_id"]');
    var cost_center_id = form_cost_center_selector.val();

    var i = 0;
    var material_ids = new Array(), item_ids = new Array(), quantities = new Array(), rates = new Array(),descriptions = new Array();
    var tbody = modal.find('tbody'), error = 0;

    tbody.find('input[name="material_id"]').each(function(){
        var material_id = $(this).val();
        var row = $(this).closest('tr');
        var rate = row.find('input[name="rate"]').unmask();
        var quantity = row.find('input[name="quantity"]').val();
        var description = row.find('textarea[name="description"]').val();
        var item_id = row.find('input[name="item_id"]').val();


        if(parseFloat(quantity) > 0 && parseFloat(rate) > 0 && material_id != '') {
            material_ids[i] = material_id;
            quantities[i] = quantity;
            descriptions[i] = description;
            rates[i] = rate;
            item_ids[i] = item_id;
            i++;
        } else {

            error++;
        }
    });


    if(quantities.length > 0 && project_id != '' && rates.length > 0 && date != '') {

        start_spinner();

        modal.modal('hide');

        $.post(
            base_url + "costs/save_bulk_material_cost/",
            {
                source_sub_location_id: source_sub_location_id,
                project_id: project_id,
                cost_center_id: cost_center_id,
                date: date,
                descriptions: descriptions,
                quantities: quantities,
                rates: rates,
                material_ids:material_ids,
                item_ids: item_ids
            }
        ).complete(function () {
            //reset form
            var form = button.closest('form');
            form[0].reset();
            toast('success','Successful');
            modal.closest('.box').find('.sub_location_material_stock').DataTable().draw('page');
            stop_spinner();
        });

        button.attr('active', 'true');
    }else{
        toast('error','An Error occured');
    }
}

/********************************************************
 * STOCKS
 ********************************************************/

function initialize_location_material_stock() {

    $('#location_material_stock_table').each(function () {
        var table = $(this);
        if(table.attr('dataTable_initialized') != 'true' ) {
            var location_id = table.attr('location_id');
            table.DataTable({
                "order": [[1, "asc"]],
                colReorder: true,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: base_url + "inventory/location_material_stock/" + location_id,
                    type: 'POST'
                },
                "columns": [
                    {"orderable": false},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": false}
                ],
                "language": {
                    "zeroRecords": "<div class='alert alert-info'>No matching items found</div>",
                    "emptyTable": "<div class='alert alert-info'>No items found</div>"
                }, "drawCallback": function () {
                    $(this).find('tr').each(function () {
                        $(this).find('td:last-child').attr('nowrap', 'nowrap');
                    });

                    $('a[href="#location_transfer_orders"]').on('shown.bs.tab', function (e) {
                        $('#location_transfer_orders_table').each(function () {
                            var table = $(this);
                            if(table.attr('datatable_initialized') != 'true') {
                                var location_id = table.attr('location_id');
                                table.DataTable({
                                    "order": [[0, "desc"]],
                                    colReorder: true,
                                    "processing": true,
                                    "serverSide": true,
                                    "ajax": {
                                        url: base_url + "inventory/location_transfer_orders/" + location_id,
                                        type: 'POST'
                                    },
                                    "columns": [
                                        {"orderable": true},
                                        {"orderable": true},
                                        {"orderable": true},
                                        {"orderable": true},
                                        {"orderable": true},
                                        {"orderable": false}
                                    ],
                                    "language": {
                                        "zeroRecords": "<div class='alert alert-info'>No matching transfer orders found</div>",
                                        "emptyTable": "<div class='alert alert-info'>No transfer orders found</div>"
                                    }, "drawCallback": function () {
                                        table.find('tr').each(function () {
                                            $(this).find('td:last-child').attr('nowrap', 'nowrap');
                                        });

                                        table.find('.transfer_order_transfer_form').each(function () {
                                            var transfer_form = $(this);
                                            var project_id = transfer_form.find('select[name="project_id"]').val();
                                            transfer_form.find('select[name="source_sub_location_id"]').each(function () {
                                                var source_selector = $(this);
                                                source_selector.change(function () {
                                                    var sub_location_id = $(this).val();
                                                    validate_sub_store_material_quantity(source_selector,'tr',project_id);
                                                });
                                            });
                                        });

                                        initialize_common_js();
                                    }
                                });
                                table.attr('datatable_initialized',true);
                            } else {
                                table.DataTable().draw('page');
                            }
                        });

                        $('.sub_location_material_stock_activator').on('shown.bs.tab', function (e) {
                            $($(this).attr('href')).find('.sub_location_material_stock').DataTable().draw('page');
                        });
                    });

                    $('a[href="#location_material_transfers"]').on('shown.bs.tab', function (e) {
                        var tab_pane = $($(this).attr('href'));
                        tab_pane.find('#location_material_transfers_table').each(function () {
                            var table = $(this);
                            if(table.attr('dataTable_initialized') != 'true' ) {
                                var location_id = table.attr('location_id');
                                table.DataTable({
                                    "order": [[1, "desc"]],
                                    colReorder: true,
                                    "processing": true,
                                    "serverSide": true,
                                    "ajax": {
                                        url: base_url + "inventory/location_material_transfers/" + location_id,
                                        type: 'POST'
                                    },
                                    "columns": [
                                        {"orderable": true},
                                        {"orderable": true},
                                        {"orderable": true},
                                        {"orderable": true},
                                        {"orderable": true},
                                        {"orderable": true},
                                        {"orderable": false}
                                    ],
                                    "language": {
                                        "zeroRecords": "<div class='alert alert-info'>No matching material transfers found</div>",
                                        "emptyTable": "<div class='alert alert-info'>No material transfers found</div>"
                                    }, "drawCallback": function () {
                                        table.find('tr').each(function () {
                                            $(this).find('td:last-child').attr('nowrap', 'nowrap');
                                        });
                                        table.find('.delete_external_material_transfer').each(function () {
                                            var button = $(this);
                                            if(button.attr('initialized') != 'true') {
                                                var transfer_id = button.attr('transfer_id');
                                                button.click(function () {
                                                    if(confirm('Are You Sure?')) {
                                                        start_spinner();
                                                        $.post(
                                                            base_url + "inventory/delete_transfer",
                                                            {
                                                                transfer_id: transfer_id
                                                            }, function (data) {
                                                                stop_spinner();
                                                                table.DataTable().draw('page');
                                                            }
                                                        ).complete();
                                                    }
                                                });
                                            }
                                        });
                                        initialize_material_movement_forms();
                                        initialize_common_js();
                                    }
                                });
                                table.attr('dataTable_initialized','true');
                            } else {
                                table.DataTable().draw('page');
                            }
                        });
                    });

                    $('a[href="#location_material_disposals"]').on('shown.bs.tab', function (e) {
                        $('#location_material_disposal').each(function () {
                            var table = $(this);
                            if(table.attr('datatable_initialized') != 'true') {
                                var location_id = table.attr('location_id');
                                table.DataTable({
                                    "order": [[0, "desc"]],
                                    colReorder: true,
                                    "processing": true,
                                    "serverSide": true,
                                    "ajax": {
                                        url: base_url + "inventory/location_material_disposals/" + location_id,
                                        type: 'POST'
                                    },
                                    "columns": [
                                        {"orderable": true},
                                        {"orderable": true},
                                        {"orderable": true},
                                        {"orderable": false}
                                    ],
                                    "language": {
                                        "zeroRecords": "<div class='alert alert-info'>No matching Disposals found</div>",
                                        "emptyTable": "<div class='alert alert-info'>No Disposals found</div>"
                                    },
                                    "drawCallback": function () {

                                        table.find('tr').each(function () {
                                            $(this).find('td:last-child').attr('nowrap', 'nowrap');
                                        });

                                        $('.save_material_disposal').each(function(){
                                            var button = $(this);
                                            if(button.attr('active') != 'true') {
                                                button.click(function () {
                                                    var modal = button.closest('.modal');
                                                    var disposal_id = modal.find('input[name="disposal_id"]').val();
                                                    var disposal_date = modal.find('input[name="disposal_date"]').val();
                                                    var location_id = modal.find('input[name="location_id"]').val();
                                                    var project_id = modal.find('select[name="project_id"]').val(),i = 0;

                                                    var source_sub_location_ids = new Array(),material_item_ids = new Array(),quantities = new Array(), remarks = new Array(),rates= new Array();
                                                    var tbody = modal.find('tbody');
                                                    var quantity, material_id, error = 0;
                                                    tbody.find('input[name="quantity"]').each(function(){
                                                        quantity = $(this).val();
                                                        material_id = tbody.find('select[name="material_id"]:eq(' + i + ')').val();

                                                        if(parseFloat(quantity) > 0 && location_id != '' ) {
                                                            quantities[i] = quantity;
                                                            source_sub_location_ids[i] = tbody.find('select[name="source_sub_location_id"]:eq(' + i + ')').val();
                                                            rates[i] = tbody.find('input[name="rate"]:eq(' + i + ')').val();
                                                            material_item_ids[i] = material_id;
                                                            remarks[i] = tbody.find('textarea[name="remarks"]:eq(' + i + ')').val();
                                                        } else {
                                                            error++;
                                                        }
                                                        i++;
                                                    });
                                                    console.log('disposal_id',disposal_date,location_id,project_id,source_sub_location_ids,material_item_ids,quantities,rates,remarks);

                                                    if(disposal_date != '' && location_id != ''  && source_sub_location_ids.length > 0 && error == 0) {
                                                        modal.modal('hide');
                                                        start_spinner();
                                                        $.post(
                                                            base_url + "inventory/save_material_disposal/",
                                                            {
                                                                disposal_id : disposal_id,
                                                                disposal_date : disposal_date,
                                                                rates : rates,
                                                                quantities : quantities,
                                                                location_id: location_id,
                                                                project_id : project_id,
                                                                material_item_ids : material_item_ids,
                                                                source_sub_location_ids : source_sub_location_ids,
                                                                remarks : remarks
                                                            }, function () {
                                                                $('#location_material_disposal').DataTable().draw('page');
                                                                modal.find('form')[0].reset();
                                                                tbody.find('.artificial_row').remove();
                                                                tbody.find('.unit_display').html('');
                                                                initialize_common_js();
                                                            }
                                                        ).complete(function(){
                                                            stop_spinner();
                                                        });
                                                    } else {
                                                        toast('warning','Check if all fields are filled correctly');
                                                    }
                                                });
                                                button.attr('active', 'true');
                                            }
                                        });

                                        initialize_material_movement_forms();
                                        initialize_common_js();
                                    }
                                });
                                table.attr('datatable_initialized',true);
                            } else {
                                table.DataTable().draw('page');
                            }
                        });


                        $('.sub_location_material_stock_activator').on('shown.bs.tab', function (e) {
                            $($(this).attr('href')).find('.sub_location_material_stock').DataTable().draw('page');
                        });

                    });

                    $('a[href="#material_cost_center_assignment"]').on('shown.bs.tab', function (e) { //ptm1  added '#material_cost_center_assignment'
                        $('#material_cost_center_assignment_tab').each(function () {
                            var table = $(this);
                            if(table.attr('datatable_initialized') != 'true') {
                                var location_id = table.attr('location_id');
                                table.DataTable({
                                    "order": [[0, "desc"]],
                                    colReorder: true,
                                    "processing": true,
                                    "serverSide": true,
                                    "ajax": {
                                        url: base_url + "inventory/material_cost_center_assignment/" + location_id,
                                        type: 'POST'
                                    },
                                    "columns": [
                                        {"orderable": true},
                                        {"orderable": true},
                                        {"orderable": true},
                                        {"orderable": true},
                                        {"orderable": true},
                                        {"orderable": false}
                                    ],
                                    "language": {
                                        "zeroRecords": "<div class='alert alert-info'>No matching Cost Assignment found</div>",
                                        "emptyTable": "<div class='alert alert-info'>No Cost Assignment found</div>"
                                    },
                                    "drawCallback": function () {

                                        //save assignment
                                        $('.save_material_cost_center_assignment').each(function(){
                                            var button = $(this);
                                            if(button.attr('active') != 'true') {
                                                button.click(function () {


                                                    var modal = button.closest('.modal');
                                                    var material_cost_center_assignment_id = modal.find('input[name="material_cost_center_assignment_id"]').val();
                                                    var assignment_date = modal.find('input[name="assignment_date"]').val();
                                                    var location_id = modal.find('input[name="location_id"]').val();
                                                    var source_project_id = modal.find('select[name="project_id"]').val();
                                                    var destination_project_id = modal.find('select[name="destination_project_id"]').val(),i = 0;

                                                    var sub_location_ids = new Array(),item_ids = new Array(),quantities = new Array(), descriptions = new Array(),prices= new Array();
                                                    var tbody = modal.find('tbody');
                                                    var quantity, material_id, error = 0;
                                                    tbody.find('input[name="quantity"]').each(function(){
                                                        quantity = $(this).val();
                                                        material_id = tbody.find('select[name="material_id"]:eq(' + i + ')').val();

                                                        if(parseFloat(quantity) > 0 && location_id != '' ) {
                                                            quantities[i] = quantity;
                                                            sub_location_ids[i] = tbody.find('select[name="source_sub_location_id"]:eq(' + i + ')').val();
                                                            prices[i] = tbody.find('input[name="rate"]:eq(' + i + ')').val();
                                                            item_ids[i] = material_id;
                                                            descriptions[i] = tbody.find('textarea[name="remarks"]:eq(' + i + ')').val();
                                                        } else {
                                                            error++;
                                                        }
                                                        i++;
                                                    });
                                                    // console.log('material_cost_center_assignment_id',assignment_date,location_id,source_project_id,destination_project_id,sub_location_ids,item_ids,quantities,prices,descriptions);

                                                    if(assignment_date != '' && location_id != ''  && sub_location_ids.length > 0 && error == 0) {
                                                        modal.modal('hide');
                                                        start_spinner();
                                                        $.post(
                                                            base_url + "inventory/save_material_cost_center_assignment/",
                                                            {
                                                                material_cost_center_assignment_id : material_cost_center_assignment_id,
                                                                assignment_date : assignment_date,
                                                                location_id: location_id,
                                                                destination_project_id : destination_project_id,
                                                                item_ids : item_ids,
                                                                sub_location_ids : sub_location_ids,
                                                                source_project_id : source_project_id,
                                                                quantities : quantities,
                                                                prices : prices,
                                                                descriptions : descriptions
                                                            }, function () {
                                                                $('#material_cost_center_assignment_tab').DataTable().draw('page');
                                                                modal.find('form')[0].reset();
                                                                tbody.find('.artificial_row').remove();
                                                                tbody.find('.unit_display').html('');
                                                                initialize_common_js();
                                                            }
                                                        ).complete(function(){
                                                            stop_spinner();
                                                        });
                                                    } else {
                                                        toast('warning','Check if all fields are filled correctly');
                                                    }
                                                });
                                                button.attr('active', 'true');
                                            }
                                        });

                                        table.find('tr').each(function () {
                                            $(this).find('td:last-child').attr('nowrap', 'nowrap');
                                        });
                                        initialize_material_movement_forms();
                                        initialize_common_js();
                                    }
                                });
                                table.attr('datatable_initialized',true);
                            } else {
                                table.DataTable().draw('page');
                            }
                        });


                        $('.sub_location_material_stock_activator').on('shown.bs.tab', function (e) {
                            $($(this).attr('href')).find('.sub_location_material_stock').DataTable().draw('page');
                        });

                    });

                    initialize_common_js();
                }
            });
            table.attr('dataTable_initialized','true');
        } else {
            table.DataTable().draw('page');
        }

        $('a[href="#location_material_stock"], a[href="#location_material"]').on('shown.bs.tab', function (e) {
            var tab_pane = $($(this).attr('href'));
            tab_pane.find('#location_material_stock_table').DataTable().draw('page');
        });
    });

    $('a[href="#location_requisitions"]').on('shown.bs.tab', function (e) {
        $('#location_requisitions_table').each(function () {
            var table = $(this);
            if(table.attr('datatable_initialized') != 'true') {
                var location_id = table.attr('location_id');
                table.DataTable({
                    "order": [[0, "desc"]],
                    colReorder: true,
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        url: base_url + "inventory/requisitions/",
                        type: 'POST',
                        data: {
                            location_id : location_id
                        }
                    },
                    "columns": [
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": false}
                    ],
                    "language": {
                        "zeroRecords": "<div class='alert alert-info'>No matching requisitions found</div>",
                        "emptyTable": "<div class='alert alert-info'>No requisitions found</div>"
                    }, "drawCallback": function () {
                        table.find('tbody tr').each(function () {
                            $(this).find('td:last-child').attr('nowrap', 'nowrap');
                        });

                        initialize_requisition_approval_forms(table);

                        initialize_requisition_buttons();
                        initialize_requisition_and_order_form();
                    }
                });
                table.attr('datatable_initialized',true);
            } else {
                table.DataTable().draw('page');
            }
        });
    });

    $('a[href="#location_grns"]').on('shown.bs.tab', function (e) {
        $('#location_grns_table').each(function () {
            var table = $(this);
            if(table.attr('datatable_initialized') != 'true') {
                var location_id = table.attr('location_id');
                table.DataTable({
                    "order": [[0, "desc"]],
                    colReorder: true,
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        url: base_url + "inventory/location_grns/" + location_id,
                        type: 'POST'
                    },
                    "columns": [
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": false},
                        {"orderable": false},
                        {"orderable": true},
                        {"orderable": false}
                    ],
                    "language": {
                        "zeroRecords": "<div class='alert alert-info'>No matching GRNs found</div>",
                        "emptyTable": "<div class='alert alert-info'>No GRNs found</div>"
                    }
                });
                table.attr('datatable_initialized',true);
            } else {
                table.DataTable().draw('page');
            }
        });

        $('.sub_location_material_stock_activator').on('shown.bs.tab', function (e) {
            $($(this).attr('href')).find('.sub_location_material_stock').DataTable().draw('page');
        });
    });

    $('a[href="#location_purchase_orders"]').on('shown.bs.tab', function (e) {
        $('#location_purchase_orders_table').each(function () {
            var table = $(this);
            if(table.attr('datatable_initialized') != 'true') {
                var location_id = table.attr('location_id');
                table.DataTable({
                    "order": [[0, "desc"]],
                    colReorder: true,
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        url: base_url + "procurements/location_purchase_orders/" + location_id,
                        type: 'POST'
                    },
                    "columns": [
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": false}
                    ],
                    "language": {
                        "zeroRecords": "<div class='alert alert-info'>No matching purchase orders found</div>",
                        "emptyTable": "<div class='alert alert-info'>No purchase orders found</div>"
                    }, "drawCallback": function () {
                        table.find('tr').each(function () {
                            $(this).find('td:last-child').attr('nowrap', 'nowrap');
                        });
                        initialize_common_js();
                        initialize_requisition_and_order_form();
                    }
                });
                table.attr('datatable_initialized',true);
            } else {
                table.DataTable().draw('page');
            }
        });

        $('.sub_location_material_stock_activator').on('shown.bs.tab', function (e) {
            $($(this).attr('href')).find('.sub_location_material_stock').DataTable().draw('page');
        });
    });

    $('a[href="#location_sub_locations"]').on('shown.bs.tab', function (e) {
        $('.sub_location_material_stock').each(function () {
            var table = $(this);
            if(table.attr('datatable_initialized') != 'true') {
                var sub_location_id = table.attr('sub_location_id');
                table.DataTable({
                    "order": [[1, "asc"]],
                    colReorder: true,
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        url: base_url + "inventory/sub_location_material_stock/" + sub_location_id,
                        type: 'POST'
                    },
                    "columns": [
                        {"orderable": false},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": false}
                    ],
                    "language": {
                        "zeroRecords": "<div class='alert alert-info'>No matching items found</div>",
                        "emptyTable": "<div class='alert alert-info'>No items found</div>"
                    }, "drawCallback": function () {
                        $(this).find('tr').each(function () {
                            $(this).find('td:last-child').attr('nowrap', 'nowrap');
                        });
                        initialize_common_js();
                    }
                });
                table.attr('datatable_initialized',true);
            } else {
                table.DataTable().draw('page');
            }
        });

        $('.sub_location_material_stock_activator').on('shown.bs.tab', function (e) {
            $($(this).attr('href')).find('.sub_location_material_stock').DataTable().draw('page');
        });

        initialize_opening_stock_form();
    });

    $('a[href="#location_reports"]').on('shown.bs.tab', function (e){
        var tab = $($(this).attr('href'));
        var form = tab.find('form');
        form.find('select[name="report_type"]').change(function () {
            var report_type = $(this).val();
            var material_selector = form.find('select[name="material_id"]');
            var material_selector_form_group = material_selector.closest('.form-group');
            if(report_type == 'location_item_movement' || report_type == 'location_item_availability'){
                if(material_selector.html().trim() == '') {
                    start_spinner();
                    $.post(
                        base_url + "inventory/load_item_movement_material_options",
                        {
                            location_id: form.find('input[name="location_id"]').val()
                        }, function (data) {
                            material_selector.html(data);
                            stop_spinner();
                        }
                    );
                }
                material_selector_form_group.show();
            } else {
                material_selector_form_group.hide();
            }
        });

        $('#generate_location_report').off('click').on('click',function () {
            var report_type = form.find('select[name="report_type"]').val();
            var material_id = form.find('select[name="material_id"]').val();
            var project_id = form.find('select[name="project_id"]').val();
            if((report_type != 'location_item_movement'  && report_type != 'location_item_availability') || material_id.trim() != '') {
                start_spinner();
                $.post(
                    base_url + "inventory/location_reports",
                    {
                        report_type: report_type,
                        location_id: form.find('input[name="location_id"]').val(),
                        sub_location_id: form.find('select[name="sub_location_id"]').val(),
                        project_id: project_id,
                        material_id: material_id,
                        from: form.find('input[name="from"]').val(),
                        to: form.find('input[name="to"]').val()
                    }, function (data) {
                        $('#location_report_container').html(data);
                    }
                ).complete(function () {
                    stop_spinner();
                });
            }
        });
    });
}

initialize_location_material_stock();

function check_material_opening_stock_selected_material(select_element){
    select_element.change(function(){
        select_element.closest('tbody').find('select[name="item_id"]').each(function(){
            $(this).attr('active','false');
            select_element.attr('active','true');
            if($(this).attr('active') != 'true' && $(this).val() == select_element.val() && $(this).val() != null){
                select_element.select2('val',null);
            }
        });
    });
}

function initialize_opening_stock_form(){

    $('.opening_stock_form').on('show.bs.modal', function (e) {
        //var modal = $(this);

        load_sub_location_opening_stock_material_options($(this).find('select[name="project_id"]'));

        if($(this).attr('loaded') != 'true') {

            $(this).find('select[name="project_id"]').change(function () {
                var select_element = $(this);
                load_sub_location_opening_stock_material_options(select_element);
                initialize_common_js();
            });

            $(this).find('select[name="item_id"]').change(function () {
                var select_element = $(this);
                check_material_opening_stock_selected_material(select_element);
            });

            $(this).find('table').each(function(){

                var table = $(this);

                var row_template = table.find('.row_template');
                if(row_template.attr('initialized') != 'true') {
                    var tbody = table.find('tbody');

                    table.find('.row_adder').click(function () {
                        var new_row = row_template.clone().removeAttr('style')
                            .removeClass('row_template').addClass('artificial_row').appendTo(tbody);

                        var material_selector = new_row.find('select[name="item_id"]');
                        material_selector.addClass('searchable');
                        check_material_opening_stock_selected_material(material_selector);
                        new_row.find('.row_remover').click(function () {
                            $(this).closest('tr').remove();
                        });
                        initialize_common_js();
                        row_template.attr('initialized', 'true');
                    });
                }

            });

            $('.material_selector').change(function () {
                load_material_unit($(this),'form');
            });

            $(this).attr('loaded','true');
        }
    });
}

function load_sub_location_opening_stock_material_options(select_element){
    start_spinner();
    var project_id = select_element.val();
    var form_container = select_element.closest('form');

    var sub_location_id = select_element.attr('sub_location_id');

    start_spinner();
    $.post(
        base_url + "inventory/load_sub_location_opening_stock_material_options",
        {
            project_id : project_id,
            sub_location_id : sub_location_id
        }, function (data) {
            var table = form_container.find('table');

            table.find('select[name="item_id"]').each(function () {
                $(this).html(data).trigger('change');
            });
        }
    ).complete(function(){
        stop_spinner();
    });

}

function save_material_opening_stock(button){
    var modal = button.closest('.modal');
    var project_id = modal.find('select[name="project_id"]').val();
    var date = modal.find('input[name="date"]').val();

    var quantities = new Array(), item_ids = new Array(), prices = new Array(), remarks = new Array(), i = 0;
    modal.find('tbody').find('input[name="quantity"]').each(function(){
        var quantity = parseFloat($(this).val());
        var row = $(this).closest('tr');
        var item_id = row.find('select[name="item_id"]').val();
        if(quantity > 0 && item_id != null){
            quantities[i] = quantity;
            item_ids[i] = item_id;
            prices[i] = row.find('input[name="rate"]').unmask();
            remarks[i] = row.find('textarea[name="remarks"]').val();
            i++;
        }
    });

    if(date != '' && quantities.length > 0) {
        modal.modal('hide');
        var sub_location_id = modal.find('select[name="project_id"]').attr('sub_location_id');

        $.post(
            base_url + "inventory/save_material_opening_stock/",
            {
                project_id: project_id,
                quantities: quantities,
                date: date,
                sub_location_id: sub_location_id,
                item_ids : item_ids,
                prices: prices,
                remarks: remarks
            }, function () {
                modal.find('form')[0].reset();
                modal.find('.artificial_row').remove();
                modal.closest('.box').find('.sub_location_material_stock').DataTable().draw('page');
            }
        );
    }
}

function validate_sub_store_material_quantity(select_element,container,project_id){
    var form_container = select_element.closest(container);
    var material_selector = form_container.find('select[name="material_id"]');
    var material_id = material_selector.val();
    var sub_location_id = form_container.find('select[name="source_sub_location_id"]').val();
    var project_id_field = container == 'tr' ? 'select[name="project_id"]' : 'input[name="project_id"]';
    project_id = typeof project_id !== 'undefined' ? project_id : form_container.find(project_id_field).val();
    var input_element = form_container.find('input[name="quantity"]');
    var form = form_container.closest('form');
    var available_quantity_field = form_container.find('input[name="available_quantity"]');
    if(material_id != '' && sub_location_id != '') {
        project_id = project_id != '' ? project_id : null;
        start_spinner();
        $.post(
            base_url + "inventory/validate_sub_store_material_quantity",
            {
                project_id: project_id,
                material_id: material_id,
                sub_location_id: sub_location_id
            }, function (data) {
                data = parseFloat(data);
                input_element.attr('available_quantity',data);
                var currenct_quantity = input_element.val();
                currenct_quantity = currenct_quantity.trim() != '' ? currenct_quantity : 0;
                available_quantity_field.val((parseFloat(data)));
                calculate_allowed_material_quantity(input_element,false,container);
                load_material_average_price(material_selector,container,project_id);
                input_element.keyup(function(){
                    calculate_allowed_material_quantity(input_element,true,container);
                });
            }
        ).complete(function () {
            stop_spinner();
        });
    } else {
        input_element.val('');
        available_quantity_field.val('')
    }
}

function calculate_allowed_material_quantity(input_element,typed,container){

    var allowed_amount = parseFloat(input_element.attr('available_quantity'))+parseFloat(input_element.attr('previous_quantity'));

    var form_container = input_element.closest(container);

    var material_id = form_container.find('select[name="material_id"]').val();
    var sub_location_id = form_container.find('select[name="source_sub_location_id"]').val();

    var current_quantity = input_element.val();
    current_quantity = current_quantity != '' ? parseFloat(current_quantity) : 0;
    if(container == 'tr') {

        var project_id = form_container.find('select[name="project_id"]').val();

        input_element.closest('form').find('select[name="material_id"]').each(function () {
            var row = $(this).closest('tr');
            var row_sub_location_id = row.find('select[name="source_sub_location_id"]').val();
            var row_project_id = row.find('select[name="project_id"]').val();
            var row_quantity = row.find('input[name="quantity"]').val();
            row_quantity = row_quantity != '' ? parseFloat(row_quantity) : 0;
            if (material_id != '' && material_id == $(this).val() && row_sub_location_id == sub_location_id && row_project_id == project_id) {
                allowed_amount -= row_quantity;
            }
        });

        allowed_amount += parseFloat(current_quantity);
    } else {
        allowed_amount += parseFloat(input_element.attr('previous_quantity'));
    }

    if(current_quantity > allowed_amount){
        toast('error','The quantity '+current_quantity+' you entered exceeds the available quantity of '+allowed_amount);
        input_element.val(allowed_amount);
    }

    if(!typed && container == 'tr'){
        input_element.val(current_quantity);
    }

}

function load_sub_location_available_material_options(select_element,container){
    var source_sub_location_id = select_element.val();
    var material_selector = select_element.closest(container).find('select[name="material_id"]');


    if(source_sub_location_id != '') {
        start_spinner();
        $.post(
            base_url + "inventory/load_sub_location_available_material_options",
            {
                source_sub_location_id: source_sub_location_id
            }, function (data) {
                material_selector.html(data).select2("val", "");
            }
        ).complete(function(){
            stop_spinner();
        });
    } else {
        material_selector.html('').select2("val", "");
    }
}

function load_sub_location_material_transfer_project_options(material_selector){
    start_spinner();
    var row = material_selector.closest('tr');
    var material_id = material_selector.val();
    var sub_location_id = row.find('select[name="source_sub_location_id"]').val();
    $.post(
        base_url + "inventory/load_sub_location_material_transfer_project_options",
        {
            material_item_id : material_id,
            sub_location_id : sub_location_id
        }, function (data) {
            var project_selector = row.find('select[name="project_id"]');
            project_selector.html(data);
            project_selector.select2('val','');

            if(project_selector.attr('initialized') != 'true'){
                project_selector.change(function(){
                    validate_sub_store_material_quantity(project_selector,'tr');
                    load_material_average_price(project_selector.closest('tr').find('select[name="material_id"]'),'tr');
                });
                project_selector.attr('initialized','true');
            }
        }
    ).complete(function(){
        stop_spinner();
    });
}

function initialize_material_movement_forms() {
    $('.internal_material_transfer_form, .external_material_transfer_form, .material_disposal_form, .cost_center_assignment_form').each(function () {
        var modal = $(this);

        if(modal.attr('initialized') != 'true') {
            modal.on('show.bs.modal', function (e) {

                var project_id = modal.find('select[name="project_id"]').val();

                if ($(this).hasClass('external_material_transfer_form') && $(this).attr('loaded') != 'true') {
                    var source_id = $(this).attr('location_id');
                    var destination_id = $(this).attr('destination_id');

                    $.post(
                        base_url + "inventory/load_external_material_form_requirements/",
                        {
                            source_id: source_id,
                            destination_id: destination_id
                        }, function (data) {
                            modal.find('select[name="destination_location_id"]').html(data.destination_options).addClass('searchable').select2();
                        },
                        'JSON'
                    );

                    modal.find('.edit_row select[name="material_id"]').each(function () {
                        validate_sub_store_material_quantity($(this), 'tr',project_id);
                    });
                }

                modal.find('select[name="source_sub_location_id"]').each(function () {
                    var select_element = $(this);
                    if (select_element.attr('initialized') != 'true') {
                        select_element.change(function () {
                            load_sub_location_available_material_options(select_element, 'tr');
                        });
                        select_element.attr('initialized', 'true');
                    }
                });

                modal.find('select[name="project_id"]').change(function () {
                    modal.find('tbody select[name="source_sub_location_id"]').each(function () {
                        var select_element = $(this);
                        load_sub_location_available_material_options(select_element, 'tr');
                    });
                });

                modal.find('select[name="material_id"]').change(function () {
                    if ($(this).val() != '') {
                        load_material_unit($(this), ' tr ');
                        validate_sub_store_material_quantity($(this),'tr',modal.find('select[name="project_id"]').val());
                    } else {
                        $(this).closest('tr').find('.unit_display').html('');
                        $(this).closest('tr').find('input[name="available_quantity"],input[name="quantity"]').val('');
                    }
                });

                modal.find('.row_adder').each(function () {
                    if ($(this).attr('initialized') != 'true') {
                        $(this).click(function () {
                            var tbody = $(this).closest('table').find('tbody');
                            var new_row = tbody.closest('table').find('.row_template').clone().removeAttr('style')
                                .removeClass('row_template').addClass('artificial_row').appendTo(tbody);

                            new_row.find('select[name="source_sub_location_id"]').change(function () {
                                var select_element = $(this);
                                load_sub_location_available_material_options(select_element, 'tr');
                            });
                            new_row.find('select').addClass('searchable');
                            new_row.find('select[name="material_id"]').change(function () {
                                if ($(this).val() != '') {
                                    load_material_unit($(this), ' tr ');
                                    validate_sub_store_material_quantity($(this),'tr',modal.find('select[name="project_id"]').val());
                                } else {
                                    $(this).closest('tr').find('.unit_display').html('');
                                    $(this).closest('tr').find('input[name="available_quantity"],input[name="quantity"]').val('');
                                }
                            });
                            new_row.find('.row_remover').click(function () {
                                $(this).closest('tr').remove();
                            });
                            initialize_common_js();
                        });
                        $(this).attr('initialized', 'true');
                    }
                });

                modal.find('.row_remover').click(function () {
                    $(this).closest('tr').remove();
                });
                initialize_common_js();
            });

            modal.attr('initialized','true');
        }
    });
}

function save_external_material_transfer(button){
    var modal = button.closest('.modal');
    var transfer_id = modal.find('input[name="transfer_id"]').val();
    var destination_location_id = modal.find('select[name="destination_location_id"]').val();
    var source_location_id = modal.find('input[name="source_location_id"]').val();
    var project_id = modal.find('select[name="project_id"]').val();
    var is_transfer_order = modal.hasClass('transfer_order_transfer_form');
    var requisition_approval_id = is_transfer_order ? modal.find('input[name="requisition_approval_id"]').val() : null;
    var transfer_date = modal.find('input[name="transfer_date"]').val(), i = 0;
    var source_sub_location_ids = new Array(),material_item_ids = new Array(), project_ids = new Array(), quantities = new Array(), average_prices = new Array(), remarks = new Array();
    var tbody = modal.find('tbody');
    var quantity, material_id, available_quantity, error = 0;
    tbody.find('input[name="quantity"]').each(function(){
        quantity = parseFloat($(this).val());
        material_id = tbody.find('select[name="material_id"]:eq(' + i + ')').val();
        available_quantity = parseFloat(tbody.find('input[name="quantity"]:eq(' + i + ')').attr('available_quantity'));


        if(quantity > 0 && material_id != '' && available_quantity >= quantity) {
            quantities[i] = quantity;
            source_sub_location_ids[i] = tbody.find('select[name="source_sub_location_id"]:eq(' + i + ')').val();
            average_prices[i] = tbody.find('input[name="rate"]:eq(' + i + ')').val();
            material_item_ids[i] = material_id;
            remarks[i] = tbody.find('textarea[name="remarks"]:eq(' + i + ')').val();
        } else {
            error++;
        }
        i++;
    });

    if(source_location_id != '' && transfer_date != '' && destination_location_id != '' && quantities.length > 0 && error == 0) {
        modal.modal('hide');
        var comments = modal.find('textarea[name="comments"]').val();
        start_spinner();
        $.post(
            base_url + "inventory/save_external_material_transfer/",
            {
                transfer_id : transfer_id,
                destination_location_id : destination_location_id,
                source_location_id : source_location_id,
                average_prices : average_prices,
                quantities : quantities,
                requisition_approval_id: requisition_approval_id,
                is_transfer_order : is_transfer_order,
                transfer_date: transfer_date,
                material_item_ids : material_item_ids,
                project_id : project_id,
                source_sub_location_ids : source_sub_location_ids,
                remarks : remarks,
                comments : comments
            }, function () {
                $('#location_material_transfers_table,#location_transfer_orders_table').DataTable().draw('page');
                modal.find('form')[0].reset();
                if(transfer_id == '') {
                    tbody.find('select[name="material_id"]').select2("val", "");
                }
                tbody.find('.artificial_row').remove();
                tbody.find('.unit_display').html('');
                initialize_common_js();
            }
        ).complete(function(){
            stop_spinner();
        });
    } else {
        toast('warning','Check if all fields are filled correctly');
    }
}

function save_internal_material_transfer(button){
    var modal = button.closest('.modal');
    var transfer_id = modal.find('input[name="transfer_id"]').val();
    var location_id = modal.find('input[name="location_id"]').val();
    var receiver = modal.find('input[name="receiver"]').val();
    var project_id = modal.find('select[name="project_id"]').val();
    var transfer_date = modal.find('input[name="transfer_date"]').val(), i = 0;
    var source_sub_location_ids = new Array(),material_item_ids = new Array(), project_ids = new Array(), quantities = new Array(), average_prices = new Array(), remarks = new Array();
    var tbody = modal.find('tbody'), destination_sub_location_ids = new Array();

    var error = 0;
    tbody.find('input[name="quantity"]').each(function(){
        var quantity = $(this).val();
        var material_id = tbody.find('select[name="material_id"]:eq(' + i + ')').val();
        var average_price = tbody.find('input[name="rate"]:eq(' + i + ')').val();
        var source_sub_location_id = tbody.find('select[name="source_sub_location_id"]:eq(' + i + ')').val();
        var destination_sub_location_id = tbody.find('select[name="destination_sub_location_id"]:eq(' + i + ')').val();
        if(parseFloat(quantity) > 0 && material_id != '' && project_id != '' && source_sub_location_id != ''&& destination_sub_location_id != '' && source_sub_location_id != destination_sub_location_id) {
            quantities[i] = quantity;
            source_sub_location_ids[i] = source_sub_location_id;
            destination_sub_location_ids[i] = destination_sub_location_id;
            material_item_ids[i] = material_id;
            project_ids[i] = project_id;
            average_prices[i] = average_price;
            remarks[i] = tbody.find('textarea[name="remarks"]:eq(' + i + ')').val();
        } else {
            error++;
        }
        i++;
    });

    if(location_id != '' && transfer_date != '' && quantities.length > 0 && error == 0) {
        modal.modal('hide');
        var comments = modal.find('textarea[name="comments"]').val();
        start_spinner();
        $.post(
            base_url + "inventory/save_internal_material_transfer/",
            {
                transfer_id : transfer_id,
                location_id : location_id,
                quantities: quantities,
                average_prices: average_prices,
                receiver: receiver,
                transfer_date: transfer_date,
                material_item_ids : material_item_ids,
                project_ids : project_ids,
                project_id:project_id,
                source_sub_location_ids: source_sub_location_ids,
                destination_sub_location_ids: destination_sub_location_ids,
                remarks: remarks,
                comments: comments
            }, function (data) {
                toast('success','');
                $('#location_material_transfers_table').DataTable().draw('page');
                modal.find('form')[0].reset();
                if(transfer_id == '') {
                    tbody.find('select[name="material_id"]').select2("val", "");
                }
                tbody.find('.artificial_row').remove();
                tbody.find('.unit_display').html('');
                initialize_common_js();
            }
        ).complete(function(){
            stop_spinner();
        });
    } else {
        toast('error','Please fill all fields correctly');
    }
}

function receive_external_material_transfer(button){
    var modal = button.closest('.modal');
    var location_id = modal.find('input[name="location_id"]').val();
    var source_location_id = modal.find('input[name="source_location_id"]').val();
    var receive_date = modal.find('input[name="receive_date"]').val();
    var receiving_sub_location_id = modal.find('select[name="receiving_sub_location_id"]').val();
    if(receiving_sub_location_id != '' && receive_date != '') {
        start_spinner();
        modal.modal('hide');
        var transfer_id = modal.find('input[name="transfer_id"]').val();
        var comments = modal.find('textarea[name="comments"]').val();
        var material_ids = new Array(),project_ids = new Array(), quantities = new Array(),prices = new Array(), remarks = new Array(), i = 0;
        modal.find('input[name="material_id"]').each(function(){
            var row = $(this).closest('tr');
            var quantity = row.find('input[name="quantity"]').val();
            if(parseFloat(quantity) > 0) {
                material_ids[i] = $(this).val();
                project_ids[i] = row.find('input[name="project_id"]').val();
                quantities[i] = row.find('input[name="quantity"]').val();
                prices[i] = row.find('input[name="price"]').val();
                remarks[i] = row.find('textarea[name="remarks"]').val();
                i++;
            }
        });

        $.post(
            base_url + "inventory/receive_external_material_transfer",
            {
                transfer_id : transfer_id,
                location_id : location_id,
                source_location_id : source_location_id,
                receiving_sub_location_id : receiving_sub_location_id,
                receive_date: receive_date,
                project_ids: project_ids,
                material_ids: material_ids,
                quantities: quantities,
                prices: prices,
                remarks: remarks,
                comments: comments
            }
        ).complete(function(){
            stop_spinner();
            toast('success','Transaction Submitted Successfully');
            $('#location_material_transfers_table').DataTable().draw('page');
        });
    }
}

/***************************************************
 * REQUISITIONS
 ***************************************************/

function save_requisition(button){
    var modal = button.closest('.modal');
    var requisition_id = modal.find('input[name="requisition_id"]').val();
    var approval_module_id = modal.find('select[name="approval_module_id"]').val();
    var requisition_cost_center_field = modal.find('select[name="requisition_cost_center_id"]');
    var requisition_cost_center_id = requisition_cost_center_field.val();
    var request_date = modal.find('input[name="request_date"]').val();
    var action_level = modal.find('input[name="action_level"]').val();
    var currency_id = modal.find('select[name="currency_id"]').val();
    var required_date = modal.find('input[name="required_date"]').val(), i = 0;
    var cost_center_ids = new Array(),expense_account_ids = new Array(),item_types = new Array(), source_types = Array(),
        source_or_unit_ids = new Array(), item_ids = new Array(), quantities = new Array(), rates = new Array();
    var tbody = modal.find('tbody'), error = 0;

    tbody.find('input[name="quantity"]').each(function(){
        var item_id, source_or_unit_id;
        var quantity = $(this).val();
        var row = $(this).closest('tr');
        var rate = row.find('input[name="rate"]').unmask();
        var item_type = row.find('input[name="item_type"]').val();
        var source_type;
        if(item_type == 'material'){
            item_id = row.find('select[name="material_id"]').val();
            source_or_unit_id = row.find('select[name="source_id"]').val();
            source_type = row.find('select[name="source_type"]').val();
        } else {
            item_id = row.find('input[name="description"]').val();
            source_or_unit_id = row.find('select[name="uom_id"]').val();
            source_type = '';
        }

        if(parseFloat(quantity) > 0 && parseFloat(rate) > 0 && item_id != '' && ((item_type == 'material' && source_type == 'cash') || source_or_unit_id.trim() != '')) {
            quantities[i] = quantity;
            item_types[i] = item_type;
            cost_center_ids[i] = '';
            expense_account_ids[i] = '';
            source_or_unit_ids[i] = source_or_unit_id;
            source_types[i] = source_type;
            rates[i] = rate;
            item_ids[i] = item_id;
            i++;
        } else {
            error++;
        }
    });

    if(error == 0 && request_date != '' && quantities.length > 0 && approval_module_id.trim() != '' && approval_module_id != '' && requisition_cost_center_id.trim() != '') {
        modal.modal('hide');
        var freight = parseFloat(modal.find('input[name="freight"]').unmask());
        var inspection_and_other_charges = parseFloat(modal.find('input[name="inspection_and_other_charges"]').unmask());
        var vat_inclusive = modal.find('  input[name="vat_inclusive"]').is(":checked") ? 1 : 0;
        var vat_percentage = modal.find('input[name="vat_percentage"]').val();
        var comments = modal.find('textarea[name="comments"]').val();
        var status = button.hasClass('suspend_requisition') ? 'INCOMPLETE' : 'PENDING';

        start_spinner();
        $.post(
            base_url + "requisitions/save_requisition/",
            {
                requisition_id : requisition_id,
                approval_module_id : approval_module_id,
                requisition_cost_center_id : requisition_cost_center_id,
                quantities: quantities,
                rates: rates,
                currency_id: currency_id,
                request_date: request_date,
                required_date: required_date,
                item_types : item_types,
                item_ids : item_ids,
                source_or_unit_ids: source_or_unit_ids,
                cost_center_ids: cost_center_ids,
                expense_account_ids: expense_account_ids,
                source_types : source_types,
                freight: freight,
                inspection_and_other_charges: inspection_and_other_charges,
                vat_inclusive: vat_inclusive,
                vat_percentage: vat_percentage,
                status:status,
                comments: comments
            }, function (data) {
                modal.find('form')[0].reset();
                tbody.find('.artificial_row').remove();
                requisition_cost_center_field.closest('form-group').hide();
                modal.find('.unit_display, .total_amount_display').html('');
                modal.closest('.box').find('.requisitions_table').DataTable().draw('page');
                initialize_common_js();
            }
        ).complete(function(){
            stop_spinner();
        });
    } else {
        toast('error','Please make sure all fields are correctly filled');
    }
}

function initialize_requisition_approval_forms(table) {
    table.find('.requisition_approval_form').each(function () {
        var modal = $(this);
        $(this).on('show.bs.modal', function (e){
            var material_sources_row_template = modal.find('.material_source_row_template');
            var cash_sources_row_template = modal.find('.cash_source_row_template');

            var calculate_requisition_approval_total = function () {
                var total_amount = 0;
                modal.find(' .major_table_tbody input[name="rate"]').each(function () {
                    var row = $(this).closest('tr');
                    var rate = parseFloat($(this).unmask());
                    var quantity = parseFloat(row.find(' input[name="quantity"]').val());
                    total_amount += quantity*rate;
                });

                total_amount += parseFloat(modal.find('input[name="freight"]').unmask())+parseFloat(modal.find('input[name="inspection_and_other_charges"]').unmask());
                modal.find('.total_amount_display').html(Math.round(total_amount * 100) / 100).priceFormat();
            };

            calculate_requisition_approval_total();

            modal.find('.sources_table').each(function () {
                var sources_table = $(this);

                sources_table.find('tr').each(function () {
                    initialize_form_amount_calculator($(this),3);
                });

                var initialize_source_change = function () {
                    var cashbook_options = modal.find('.sources_options_templates select[name="cashbook_options"]').html();
                    var vendor_options = modal.closest('table').find('.sources_options_templates select[name="vendor_options"]').html();
                    var main_location_options = modal.closest('table').find('.sources_options_templates select[name="main_location_options"]').html();
                    sources_table.find('select[name="source_type"]').each(function () {
                        if($(this).attr('initialized') != 'true') {
                            $(this).change(function () {
                                var source_selector = $(this).closest('tr').find('select[name="source"]');
                                if ($(this).val() == 'cash') {
                                    source_selector.html(cashbook_options).select2('val','');
                                    source_selector.attr('disabled','disabled');
                                } else if($(this).val() == 'store') {
                                    source_selector.html(main_location_options).select2('val','');
                                    source_selector.removeAttr('disabled');
                                } else {
                                    source_selector.html(vendor_options).select2('val','');
                                    source_selector.removeAttr('disabled');
                                }
                            });
                            $(this).attr('initialized','true');
                        }
                    });
                };

                initialize_source_change();


                sources_table.find(' .material_source_adder').each(function () {
                    var button = $(this);
                    if(button.attr('initialized') != 'true') {
                        var tbody = button.closest('table').find('tbody');
                        button.click(function () {
                            var new_row = material_sources_row_template.clone().removeAttr('style').removeClass('material_source_row_template').addClass('artificial_row').prependTo(tbody);
                            new_row.find('select[name="source"]').select2();
                            new_row.find('.number_format').priceFormat();
                            initialize_form_amount_calculator(new_row,3);
                            new_row.find('.row_remover').click(function () {
                                $(this).closest('tr').remove();
                                calculate_requisition_approval_total();
                            });
                            initialize_source_change();
                        });
                        button.attr('initialized','true');
                    }
                });

                sources_table.find(' .cash_source_adder').each(function () {
                    var button = $(this);
                    if(button.attr('initialized') != 'true') {
                        var tbody = button.closest('table').find('tbody');
                        button.click(function () {
                            var new_row = cash_sources_row_template.clone().removeAttr('style').removeClass('cash_source_row_template').addClass('artificial_row').prependTo(tbody);
                            new_row.find('select[name="source"]').select2();
                            new_row.find('.number_format').priceFormat();
                            initialize_form_amount_calculator(new_row,3);
                            new_row.find('.row_remover').click(function () {
                                $(this).closest('tr').remove();
                                calculate_requisition_approval_total();
                            });
                            initialize_source_change();
                        });
                        button.attr('initialized','true');
                    }
                });
            });

            modal.delegate('  .major_table_tbody input[name="rate"], .major_table_tbody input[name="quantity"], input[name="freight"], input[name="inspection_and_other_charges"]  ','change keyup',function() {
                calculate_requisition_approval_total();
            });

        });
    });
}

function approve_requisition(button){
    var modal = button.closest('.modal');
    var requisition_id = modal.find('input[name="requisition_id"]').val();
    var has_sources = modal.find('input[name="has_sources"]').val();
    var approval_chain_level_id = modal.find('input[name="approval_chain_level_id"]').val();
    var returned_chain_level_id = modal.find('select[name="returned_chain_level_id"]').val();
    var currency_id = modal.find('select[name="currency_id"]').val();
    var approve_date = modal.find('input[name="approve_date"]').val(), i = 0;
    var item_ids = new Array(), expense_account_ids = [], item_types = Array(), quantities = new Array(), source_types = new Array(), sources = new Array(),
        rates = new Array(), remarks = new Array();
    var tbody = modal.find(' .major_table_tbody');

    var error = 0;
    tbody.find('input[name="item_id"]').each(function(){
        var row = $(this).closest('tr');
        var row_index = 0;
        item_ids[i] = $(this).val();
        item_types[i] = row.find('input[name="item_type"]').val();
        expense_account_ids[i] = row.find('select[name="expense_account_id"]').val();
        if(has_sources == 'true') {
            var row_quantities = new Array(), row_rates = new Array(), row_source_types = new Array(), row_currencies = new Array(), row_sources = new Array();
            row.find('.source_approved_quantity').each(function () {
                var source_row = $(this).closest('tr');
                row_quantities[row_index] = $(this).val();
                row_rates[row_index] = source_row.find('input[name="rate"]').unmask();
                row_currencies[row_index] = source_row.find('select[name="currency_id"]').val();
                row_sources[row_index] = source_row.find('select[name="source"]').val();
                row_source_types[row_index] = source_row.find('select[name="source_type"]').val();
                if ((row_source_types[row_index] != 'cash' && item_types[i] != 'cash') && (row_sources[row_index] == '' || row_source_types[row_index] == '')) {
                    error++;
                }
                row_index++;
            });

            quantities[i] = row_quantities;
            rates[i] = row_rates;
            sources[i] = row_sources;
            source_types[i] = row_source_types;
        } else {
            item_types[i] = row.find('input[name="item_type"]').val();
            quantities[i] = row.find('input[name="quantity"]').val();
            rates[i] = row.find('input[name="rate"]').unmask();
        }
        i++;
    });


    if(approve_date != '' && error == 0) {


        modal.modal('hide');
        var freight = modal.find('input[name="freight"]').unmask();
        var inspection_and_other_charges = modal.find('input[name="inspection_and_other_charges"]').unmask();
        var vat_inclusive = modal.find('  input[name="vat_inclusive"]').is(":checked") ? 1 : 0;
        var vat_percentage = modal.find('input[name="vat_percentage"]').val();
        var freight_charges=parseFloat(freight);
        var inspection_charges=parseFloat(inspection_and_other_charges);
        var comments = modal.find('textarea[name="comments"]').val();
        start_spinner();
        $.post(
            base_url + "requisitions/approve_requisition/",
            {
                requisition_id : requisition_id,
                has_sources: has_sources,
                approval_chain_level_id: approval_chain_level_id,
                returned_chain_level_id:returned_chain_level_id,
                quantities: quantities,
                rates: rates,
                currency_id : currency_id,
                expense_account_ids : expense_account_ids,
                approve_date: approve_date,
                item_ids : item_ids,
                sources : sources,
                source_types : source_types,
                item_types : item_types,
                remarks: remarks,
                freight: freight_charges,
                inspection_and_other_charges: inspection_charges,
                vat_inclusive: vat_inclusive,
                vat_percentage: vat_percentage,
                comments: comments
            }, function (data) {
                modal.closest('.box').find('.requisitions_table').DataTable().draw('page');
                initialize_common_js();
            }
        ).complete(function(){
            stop_spinner();
        });
    } else {
        toast('error','Please make sure all fields are filled correctly');
    }
}

function load_project_dropdown_options(select_field) {
    start_spinner();
    $.post(
        base_url + "projects/load_project_dropdown_options",
        {

        }, function (data) {
            var modal = select_field.closest('.modal');
            var material_selector = modal.find('select[name="material_id"]');
            material_selector.html('');
            select_field.html(data).trigger('change');

            if(select_field.attr('project_initialized') != 'true'){
                select_field.change(function () {
                    var module_id = modal.find('select[name="approval_module_id"]').val();
                    if(module_id == '2') {
                        $.post(
                            base_url + "projects/load_project_material_options",
                            {
                                project_id: $(this).val()
                            }, function (data) {
                                material_selector.each(function () {
                                    $(this).html(data);
                                });
                            }
                        );
                    }
                });

                select_field.attr('project_initialized','true');
            }
            stop_spinner();
        }
    );
}

function load_general_cost_centers_options(select_field) {

    start_spinner();
    $.post(
        base_url + "finance/load_cost_centers_dropdown_options",
        {

        }, function (data) {
            select_field.html(data).trigger('change');
            stop_spinner();
        }
    );
}

function load_material_last_approved_price(material_selector){
    if(material_selector.attr('load_price_initialized') != 'true'){

        material_selector.change(function () {
            start_spinner();
            var material_item_id = $(this).val();
            $.post(
                base_url + "inventory/load_material_last_approved_price",
                {
                    currency_id: material_selector.closest('.modal').find('select[name="currency_id"]').val(),
                    material_item_id : material_item_id
                }, function (data) {
                    material_selector.closest('tr').find('input[name="rate"]').val(data).priceFormat();
                    stop_spinner();
                }
            );
        });
        material_selector.attr('load_price_initialized','true');
    }
}

function initialize_requisition_and_order_form(){

    $('.requisition_form, .purchase_order_form, .purchase_order_receive_form').on('show.bs.modal', function (e) {
        var modal = $(this);

        if(modal.hasClass('requisition_form')){

            var check_store_available_stock = function(source_id_selector){
                var row = source_id_selector.closest('tr');
                var modal = row.closest('.modal');
                var approval_module_id = modal.find('select[name="approval_module_id"]').val();
                if(row.attr('initialized') != 'true') {
                    row.delegate(' select[name="source_id"], select[name="material_id"],  input[name="quantity"] ', 'change keyup', function () {
                        var source_type = row.find('select[name="source_type"]').val().trim();
                        var source_id = row.find('select[name="source_id"]').val().trim();
                        var material_id = row.find('select[name="material_id"]').val();
                        var quantity_field = row.find('input[name="quantity"]');
                        var project_id = modal.find('select[name="requisition_cost_center_id"]').val();

                        var validate_typed_quantity = function () {
                            var available_quantity = parseFloat(quantity_field.attr('available_quantity'));
                            var typed_quantity = parseFloat(quantity_field.val());
                            if(available_quantity < typed_quantity){
                                toast('error','Only '+available_quantity+' is available at the selected store');
                                quantity_field.val(available_quantity);
                            }
                        };


                        if (source_type == 'store' && source_id != '' && material_id.trim() != '') {
                            var send_request = $(this).is('select');
                            if(send_request){
                                start_spinner();
                                $.post(
                                    base_url + "inventory/check_store_available_material_quantity",
                                    {
                                        material_id: material_id,
                                        location_id: source_id,
                                        approval_module_id:approval_module_id,
                                        project_id: project_id
                                    }, function (data) {
                                        toast('info',data+' of them available at the selected store','Info:');
                                        quantity_field.attr('available_quantity',data);
                                        validate_typed_quantity();
                                        stop_spinner();
                                    })
                            } else {
                                validate_typed_quantity();
                            }
                        }
                    });
                    row.attr('initialized','true');
                }
            };

            modal.find('tbody select[name="material_id"]').each(function(){
                var select_element = $(this);

                load_material_last_approved_price(select_element);
                select_element.select2({width: '300px'});
            });

            modal.find('select[name="approval_module_id"]').each(function () {
                var requisition_type_field = $(this);
                if(requisition_type_field.attr('initialized') != 'true'){
                    var requisition_cost_center_field = requisition_type_field.closest('.modal-body').find('select[name="requisition_cost_center_id"]');
                    var general_options = modal.find('select[name="material_id"]:first ').html();

                    requisition_type_field.change(function () {
                        var module_id = requisition_type_field.val();

                        if(module_id == '2'){
                            load_project_dropdown_options(requisition_cost_center_field);
                            modal.find('.cost_center_form_group').show();
                        }  else if(module_id == '1'){
                            load_general_cost_centers_options(requisition_cost_center_field);
                            modal.find('.cost_center_form_group').hide();
                            modal.find('select[name="material_id"]').html(general_options).trigger('change');
                        } else {
                            requisition_cost_center_field.html('');
                        }
                    });
                    requisition_type_field.attr('initialized','true');
                }
            });

            function initialize_source_type (container) {
                container.find('select[name="source_type"]').each(function () {
                    var type_selector = $(this);
                    if(type_selector.attr('initialized') != 'true') {
                        type_selector.change(function () {

                                var source_id_selector = type_selector.closest('tr').find('select[name="source_id"]');
                                var vendor_options = modal.find('select[name="vendor_selector_template"]').html();
                                var main_location_options = modal.find('select[name="main_location_selector_template"]').html();
                                var account_options = modal.find('select[name="account_selector_template"]').html();
                                var selected_source = type_selector.val();

                                if (selected_source == 'vendor') {
                                    source_id_selector.html(vendor_options).val('');
                                } else if (selected_source == 'store') {
                                    source_id_selector.html(main_location_options).val('');
                                    source_id_selector.change(function () {
                                        if(source_id_selector.val().trim() != ''){
                                            check_store_available_stock(source_id_selector);
                                        }
                                    });
                                } else if (selected_source == 'cash') {
                                    source_id_selector.html(account_options).val('');
                                } else {
                                    source_id_selector.html('<option value="">&nbsp;</option>').val('');
                                }

                                source_id_selector.select2('val','');
                                type_selector.attr('initialized','true');
                        });
                    }
                });
            };

            initialize_source_type(modal);
        }

        if(modal.hasClass('purchase_order_form')){
            modal.find('select[name="order_type"]').each(function () {
                var type_selector = $(this);
                if(type_selector.attr('initialized') != 'true'){
                    type_selector.change(function () {
                        if(type_selector.val() == 'project_purchase_order'){
                                modal.find('.project_options_form_group').show();
                                modal.find('.cost_center_options_form_group').hide();
                        } else {
                            modal.find('.project_options_form_group').hide();
                            modal.find('.cost_center_options_form_group').show();
                        }
                        modal.find('select[name="cost_center_id"],select[name="project_id"]').select2('val','');
                    });
                    type_selector.attr('initialized','true');
                }
            });
        }

        modal.find('tbody select[name="material_id"]').each(function(){
            var select_element = $(this);
            select_element.select2({width:'300px'});
            select_element.change(function(){
                load_material_unit(select_element,'tr');
            });
        });

        modal.find('input, select, textarea').each(function () {
            $(this).attr('style','min-width :100px !important');
        });

        modal.find('tbody select[name="source_id"]').each(function () {
            $(this).select2({width: '200px'});
        });

        modal.find('tbody tr').each(function(){
            initialize_form_amount_calculator($(this));
            $(this).find('.row_remover').click(function(){
                var table = $(this).closest('table');
                $(this).closest('tr').remove();
                calculate_table_total_amount(table);
            });
        });


        modal.find('.material_row_adder').each(function(){
            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function(){
                    var tbody = $(this).closest('.row').find('tbody');
                    var new_row = tbody.closest('table').find('.material_row_template').clone().removeAttr('style').removeClass('material_row_template').addClass('artificial_row').appendTo(tbody);


                    tbody.find('select[name="material_id"]').each(function(){
                        var select_element = $(this);

                        load_material_last_approved_price(select_element);

                    });

                    new_row.find('select[name="material_id"]').each(function(){
                        var select_element = $(this);

                        select_element.select2({width: '300px'});

                        select_element.change(function(){
                            load_material_unit(select_element,'tr');
                        });
                    });


                    if(modal.hasClass('requisition_form')){
                        new_row.find('select[name="source_id"]').each(function(){
                            var select_element = $(this);

                            select_element.select2({width: '200px'});

                        });
                        initialize_source_type(new_row);
                    }

                    new_row.find('.row_remover').click(function(){
                        var table = $(this).closest('table');
                        $(this).closest('tr').remove();
                        calculate_table_total_amount(table);
                    });
                    initialize_form_amount_calculator(new_row);
                    initialize_common_js();
                    new_row.find('.number_format').priceFormat();

                });
                $(this).attr('initialized','true');
            }
        });


        modal.find('.cash_row_adder').each(function(){
            if($(this).attr('initialized') != 'true'){
                $(this).click(function(){
                    var tbody = $(this).closest('.row').find('tbody');
                    var new_row = tbody.closest('table').find('.cash_row_template').clone().removeAttr('style')
                        .removeClass('cash_row_template').addClass('artificial_row').appendTo(tbody);

                    new_row.find('select').addClass('searchable');
                    new_row.find('.row_remover').click(function(){
                        var table = $(this).closest('table');
                        $(this).closest('tr').remove();
                        calculate_table_total_amount(table);
                    });
                    initialize_form_amount_calculator(new_row);
                    initialize_common_js();
                    new_row.find('.number_format').priceFormat();

                });
                $(this).attr('initialized','true');
            }
        });

        modal.find('.number_format').priceFormat();

        var items_table = modal.find('table');
        if(modal.hasClass('purchase_order_receive_form')) {

            var factor_finder = function () {
                var form = modal.find('form');
                var exchange_rate = form.find('input[name="exchange_rate"]').unmask();
                var clearance_charges = form.find('input[name="clearance_charges"]').unmask();
                var total_amount = parseFloat(items_table.find('.total_amount_display').unmask());
                var additional_costs = parseFloat(form.find('input[name="freight"]').unmask()) + parseFloat(form.find('input[name="insurance"]').unmask()) + parseFloat(form.find('input[name="other_charges"]').unmask());
                var total_cif = total_amount+additional_costs;
                additional_costs += total_cif*0.01*parseFloat(form.find('input[name="import_duty"]').val());
                additional_costs += total_cif*0.01*parseFloat(form.find('input[name="cpf"]').val());
                additional_costs += total_cif*0.01*parseFloat(form.find('input[name="rdl"]').val());
                var factor = (total_amount+additional_costs+(clearance_charges/exchange_rate))/total_amount;
                form.find('input[name="factor"]').val(factor);
            };

            calculate_table_total_amount(items_table);
            factor_finder();

            modal.find('input[name="exchange_rate"]').priceFormat();

            modal.delegate(' input[name="rate"],  input[name="quantity"], input[name="import_duty"],' +
                'input[name="freight"],  input[name="insurance"] ,  input[name="other_charges"],' +
                'input[name="vat"],  input[name="cpf"],  input[name="rdl"],  input[name="clearance_charges"] ',
                'change keyup',function() {
                calculate_table_total_amount(items_table);
                factor_finder();
            });

            modal.find(' .next_to_duties').click(function () {
                modal.find(' .import_duties').tab("show");
            });

            modal.find(' .previous_to_item_details').click(function () {
                modal.find(' .item_details').tab("show");
                $(this).hide();
            });

            modal.find('.item_details').on('shown.bs.tab', function (e) {
                modal.find(' .previous_to_item_details, .receive_purchase_order').hide();
                modal.find('.next_to_duties').show();
            });

            modal.find('.import_duties').on('shown.bs.tab', function (e) {
                modal.find(' .previous_to_item_details, .receive_purchase_order').show();
                modal.find('.next_to_duties').hide();
            });
        } else {
            modal.delegate(' input[name="rate"],  input[name="quantity"], input[name="freight"], input[name="inspection_and_other_charges"] ','change keyup',function() {
                calculate_table_total_amount(items_table);
                var total_amount = parseFloat(items_table.find('.total_amount_display').unmask());
                var freight = parseFloat(modal.find('input[name="freight"]').unmask());
                var other_charges = parseFloat(modal.find('input[name="inspection_and_other_charges"]').unmask());
                freight = !isNaN(freight) ? freight : 0;
                other_charges = !isNaN(other_charges) ? other_charges : 0;
                modal.find('.grand_total_display').html(total_amount+freight+other_charges).priceFormat();
            });
        }


    });

    $('.delete_purchase_order').each(function () {
        var button = $(this);
        if(button.attr('initialized') != 'true'){
            button.click(function () {
                if(confirm('Are you sure?')) {
                    start_spinner();
                    $.post(
                        base_url + "procurements/delete_purchase_order",
                        {
                            order_id: button.attr('order_id')
                        }, function (data) {
                            stop_spinner();
                            button.closest('table').DataTable().draw('page');
                        }
                    );
                }
            });
            button.attr('initialized','true');
        }
    });
}

function update_requisition_attachments(form_container){
    var requisition_attachments_container = form_container.parent().find(' .requisition_attachments_container');
    var requisition_id = requisition_attachments_container.attr('requisition_id');
    $.post(
        base_url + "requisitions/requisition_attachments",
        {
            requisition_id: requisition_id
        }, function (data) {
            form_container.parent().parent().find(' form').get(0).reset();
            form_container.find('button').removeAttr('disabled');
            requisition_attachments_container.html(data);
        }
    ).complete();
}

function delete_requisition_attachment(attachment_id){
    if(confirm('Are you sure?')) {
        $.post(
            base_url + "inventory/delete_attachment",
            {
                'attachment_id': attachment_id
            }, function () {
                $('.requisition_attachment_form').each(function () {
                    update_requisition_attachments($(this));
                });
            }
        ).complete();
    }
}

function initialize_requisition_buttons(){
    $('.delete_requisition').click(function () {
        if(confirm('Are you sure?')) {
            var requisition_id = $(this).attr('requisition_id');
            var table = $(this).closest('table');
            $.post(
                base_url + "requisitions/delete_requisition",
                {
                    requisition_id: requisition_id
                }
            ).complete(function () {
                table.DataTable().draw('page');
            });
        }
    });

    $('.decline_requisition').click(function () {
        if(confirm('Are you sure?')) {
            var requisition_id = $(this).attr('requisition_id');
            var table = $(this).closest('table');
            $.post(
                base_url + "inventory/decline_requisition",
                {
                    requisition_id: requisition_id
                }
            ).complete(function () {
                table.DataTable().draw('page');
            });
        }
    });

    $('.requisition_attach').each(function () {
        var button = $(this);
        if (button.attr('active') != 'true') {
            button.click(function () {
                var form_container = $(this).parent();
                var captured = form_container.find(' input[name="file"] ')[0];
                var caption = form_container.find(' input[name="caption"] ').val();
                var requisition_id = form_container.find(' input[name="requisition_id"] ').val();
                var file = captured.files[0], formdata = false;
                form_container.find('button').attr('disabled','disabled');
                if (window.FormData) {
                    formdata = new FormData();

                    if (formdata) {
                        formdata.append("file", file);
                        formdata.append("caption", caption);
                        formdata.append("requisition_id", requisition_id);

                        $.ajax({
                            url: base_url + 'requisitions/save_requisition_attachment/',
                            type: "POST",
                            timeout: 250000,
                            cache: false,
                            data: formdata,
                            processData: false,
                            contentType: false,
                            success: function (data) {
                                update_requisition_attachments(form_container);
                            },
                            complete: function () {
                            }
                        });

                    }
                    button.attr('active', 'true');
                }
            });
        }
    });

}

function draw_requisition_table(table) {
    if(table.attr('initialized') != 'true'){
        table.DataTable({
            "order": [[4, "asc"]],
            colReorder: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: base_url + "requisitions",
                type: 'POST',
                data: {
                    'job_position_id' : table.attr('job_position_id')
                }
            },
            "columns": [
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": false}
            ],
            "language": {
                "zeroRecords": "<div class='alert alert-info'>No matching requisitions found</div>",
                "emptyTable": "<div class='alert alert-info'>No requisitions found</div>"
            }, "drawCallback": function () {
                $(this).find('tr').each(function () {
                    $(this).find('td:last-child').attr('nowrap', 'nowrap');
                });

                initialize_requisition_approval_forms($(this));
                initialize_requisition_buttons();
                initialize_common_js();
                initialize_requisition_and_order_form();
            }
        });
    } else {
        table.dataTable().draw('page');
    }
}

draw_requisition_table($('#requisitions_table, #my_desk_requisitions_table'));


/****************************************************
 * PURCHASE ORDERS
 ****************************************************/

$('#pre_orders_table').DataTable({
    "order": [[0, "desc"]],
    colReorder: true,
    "processing": true,
    "serverSide": true,
    "ajax": {
        url: base_url + "procurements/pre_orders/",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords": "<div class='alert alert-info'>No matching pending pre-orders found</div>",
        "emptyTable": "<div class='alert alert-info'>No pending pre-orders found</div>"
    }, "drawCallback": function () {

        $(this).find('tr').each(function () {
            $(this).find('td:last-child').attr('nowrap', 'nowrap');
        });
        initialize_requisition_and_order_form();
        initialize_common_js();
    }
});

function save_purchase_order(button){
    var modal = button.closest('.modal');
    var order_id = modal.find('input[name="order_id"]').val();
    var handler_id = modal.find('select[name="handler_id"]').val();
    var vendor_id = modal.find('select[name="vendor_id"]').val();
    var currency_id = modal.find('select[name="currency_id"]').val();
    var location_id = modal.find('select[name="location_id"]').val();
    var project_id = modal.find('select[name="project_id"]').val();
    var cost_center_id = modal.find('select[name="cost_center_id"]').val();
    var issue_date = modal.find('input[name="issue_date"]').val();
    var delivery_date = modal.find('input[name="delivery_date"]').val();
    var reference = modal.find('input[name="reference"]').val();
    var item_ids = new Array(), item_types = new Array(), quantities = new Array(), prices = new Array(), remarks = new Array(), i = 0;
    var tbody = modal.find('tbody');

    tbody.find('input[name="quantity"]').each(function(){
        var quantity = $(this).val();
        var row = $(this).closest('tr');
        var item_type = row.find('input[name="item_type"]').val();
        if(item_type == 'material'){
            var item_id = row.find('select[name="material_id"]').val();
        } else {
            var item_id = row.find('select[name="tool_type_id"]').val();
        }
        if(parseFloat(quantity) > 0 && item_id != '') {
            quantities[i] = quantity;
            item_types[i] = item_type;
            prices[i] = row.find('input[name="rate"]').unmask();
            item_ids[i] = item_id;
            remarks[i] = row.find('textarea[name="remarks"]').val();
            i++;
        }
    });

    if(location_id != '' && vendor_id != '' && issue_date != '' && quantities.length > 0 && handler_id != '') {
        modal.modal('hide');
        var freight = modal.find('input[name="freight"]').unmask();
        var inspection_and_other_charges = modal.find('input[name="inspection_and_other_charges"]').unmask();
        var vat_inclusive = modal.find('  input[name="vat_inclusive"]').is(":checked") ? 1 : 0;
        var vat_percentage = modal.find('input[name="vat_percentage"]').val();
        var freight_charges=parseFloat(freight);
        var inspection_charges=parseFloat(inspection_and_other_charges);
        var comments = modal.find('textarea[name="comments"]').val();
        start_spinner();
        $.post(
            base_url + "procurements/save_purchase_order/",
            {
                order_id : order_id,
                location_id : location_id,
                project_id : project_id,
                cost_center_id : cost_center_id,
                currency_id : currency_id,
                handler_id : handler_id,
                reference : reference,
                delivery_date : delivery_date,
                vendor_id : vendor_id,
                item_types: item_types,
                item_ids: item_ids,
                quantities: quantities,
                prices: prices,
                issue_date: issue_date,
                remarks: remarks,
                freight: freight_charges,
                inspection_and_other_charges: inspection_charges,
                vat_inclusive: vat_inclusive,
                vat_percentage: vat_percentage,
                comments: comments
            }, function () {
                modal.find('form')[0].reset();
                tbody.find('.artificial_row').remove();
                modal.find('.unit_display, .total_amount_display').html('');
                modal.closest('.box').find('#purchase_orders_table').DataTable().draw('page');
                initialize_common_js();
                initialize_requisition_and_order_form();
            }
        ).complete(function(){
            stop_spinner();
        });
    }
}

function save_pre_ordered_purchase_order(button){
    var modal = button.closest('.modal');
    var requisition_id = modal.find('input[name="requisition_id"]').val();
    var vendor_id = modal.find('select[name="vendor_id"]').val();
    var handler_id = modal.find('select[name="handler_id"]').val();
    var location_id = modal.find('select[name="location_id"]').val();
    var project_id = modal.find('input[name="project_id"]').val();
    var cost_center_id = modal.find('input[name="cost_center_id"]').val();
    var issue_date = modal.find('input[name="issue_date"]').val();
    var delivery_date = modal.find('input[name="delivery_date"]').val();
    var reference = modal.find('input[name="reference"]').val();
    var currency_id = modal.find('select[name="currency_id"]').val();
    var item_ids = new Array(), item_types = new Array(), quantities = new Array(), prices = new Array(), i = 0;
    var tbody = modal.find('tbody');
    tbody.find('input[name="quantity"]').each(function(){
        var quantity = $(this).val();
        var row = $(this).closest('tr');
        var item_type = row.find('input[name="item_type"]').val();
        var item_id = row.find('input[name="item_id"]').val();
        if(parseFloat(quantity) > 0 && item_id != '') {
            quantities[i] = quantity;
            item_types[i] = item_type;
            prices[i] = row.find('input[name="rate"]').unmask();
            item_ids[i] = item_id;
            i++;
        }
    });

    if(location_id != '' && vendor_id != '' && issue_date != '' && quantities.length > 0) {
        modal.modal('hide');
        var freight = modal.find('input[name="freight"]').unmask();
        var inspection_and_other_charges = modal.find('input[name="inspection_and_other_charges"]').unmask();
        var vat_inclusive = modal.find('  input[name="vat_inclusive"]').is(":checked") ? 1 : 0;
        var vat_percentage = modal.find('input[name="vat_percentage"]').val();
        var freight_charges=parseFloat(freight);
        var inspection_charges=parseFloat(inspection_and_other_charges);
        var comments = modal.find('textarea[name="comments"]').val();
        start_spinner();
        $.post(
            base_url + "procurements/save_purchase_order/",
            {
                requisition_id : requisition_id,
                location_id : location_id,
                project_id : project_id,
                cost_center_id : cost_center_id,
                handler_id : handler_id,
                reference : reference,
                delivery_date : delivery_date,
                currency_id : currency_id,
                vendor_id : vendor_id,
                item_types: item_types,
                item_ids: item_ids,
                quantities: quantities,
                prices: prices,
                issue_date: issue_date,
                freight: freight_charges,
                inspection_and_other_charges: inspection_charges,
                vat_inclusive: vat_inclusive,
                vat_percentage: vat_percentage,
                comments: comments
            }, function (data) {
                toast('success',data);
                modal.closest('#pre_orders_table').DataTable().draw('page');
                initialize_requisition_and_order_form();
                initialize_common_js();
            }
        ).complete(function(){
            stop_spinner();
        });
    }
}

function receive_purchase_order(button){
    var modal = button.closest('.modal');
    var order_id = modal.find('input[name="order_id"]').val();
    var receiving_sub_location_id = modal.find('select[name="receiving_sub_location_id"]').val();
    var exchange_rate = modal.find('input[name="exchange_rate"]').unmask();
    var factor = modal.find('input[name="factor"]').val();
    var freight = modal.find('input[name="freight"]').unmask();
    var insurance = modal.find('input[name="insurance"]').unmask();
    var other_charges = modal.find('input[name="other_charges"]').unmask();
    var clearance_charges = modal.find('input[name="clearance_charges"]').unmask();
    var clearance_vat = modal.find('input[name="clearance_vat"]').unmask();
    var import_duty = modal.find('input[name="import_duty"]').val();
    var vat = modal.find('input[name="vat"]').val();
    var cpf = modal.find('input[name="cpf"]').val();
    var rdl = modal.find('input[name="rdl"]').val();
    var receive_date = modal.find('input[name="receive_date"]').val();
    var item_types = new Array(), item_ids = new Array(), quantities = new Array(), rejected_quantities = new Array(), prices = new Array(), remarks = new Array(), i = 0;
    var tbody = modal.find('tbody');

    tbody.find('input[name="quantity"]').each(function(){
        var quantity = $(this).val();
        var row = $(this).closest('tr');
        var item_id = row.find('input[name="item_id"]').val();
        if(parseFloat(quantity) > 0 && item_id != '') {
            quantities[i] = quantity;
            rejected_quantities[i] = row.find('input[name="rejected_quantity"]').val();
            item_types[i] = row.find('input[name="item_type"]').val();
            prices[i] = parseFloat(row.find('input[name="rate"]').unmask())*parseFloat(factor);
            item_ids[i] = item_id;
            remarks[i] = row.find('textarea[name="remarks"]').val();
            i++;
        }
    });

    if(receive_date != '' && receiving_sub_location_id != '' && quantities.length > 0) {
        modal.modal('hide');
        var comments = modal.find('textarea[name="comments"]').val();
        start_spinner();
        $.post(
            base_url + "procurements/receive_purchase_order/",
            {
                order_id : order_id,
                exchange_rate : exchange_rate,
                freight : freight,
                insurance : insurance,
                clearance_charges : clearance_charges,
                clearance_vat : clearance_vat,
                other_charges : other_charges,
                import_duty : import_duty,
                vat : vat,
                cpf : cpf,
                rdl : rdl,
                factor : factor,
                rejected_quantities: rejected_quantities,
                item_types: item_types,
                item_ids: item_ids,
                receiving_sub_location_id: receiving_sub_location_id,
                quantities: quantities,
                prices: prices,
                receive_date: receive_date,
                remarks: remarks,
                comments: comments
            }, function (data) {
                toast('success','Transaction Submitted Successfuly');
                modal.find('form')[0].reset();
                modal.find('.unit_display, .total_amount_display').html('');
                modal.closest('.box').find('#purchase_orders_table,#location_purchase_orders_table').DataTable().draw('page');
                initialize_requisition_and_order_form();
                initialize_common_js();
            }
        ).complete(function(){
            stop_spinner();
        });
    }
}

function initialize_purchase_order_cancellation_form() {
    $('.cancel_purchase_order').each(function () {
        var submit_button = $(this);
        if(submit_button.attr('initialized') != 'true'){

            submit_button.click(function () {
                var order_id = submit_button.attr('order_id');
                $.confirm({
                    title: 'Cancel Purchase Order No.'+order_id,
                    content: 'This action is irreversible! Are you sure?',
                    buttons: {
                        confirm: {
                            text: 'Confirm Cancel',
                            btnClass: 'btn btn-danger',
                            action: function(){
                                start_spinner();
                                var form = submit_button.closest('form');
                                var cancellation_date = form.find('input[name="cancellation_date"]').val().trim();
                                var reason = form.find('textarea[name="reason"]').val().trim();
                                if(cancellation_date != '' && reason != '') {
                                    $.post(
                                        base_url + "procurements/cancel_purchase_order",
                                        {
                                            order_id: order_id,
                                            reason: reason,
                                            cancellation_date: cancellation_date
                                        }, function (data) {
                                            toast('success', data);
                                            submit_button.closest('.modal').modal('hide');
                                            submit_button.closest('table').DataTable().draw('page');
                                            stop_spinner();
                                        }
                                    );
                                }
                            }
                        },
                        cancel: {
                            text: "Don't Cancel",
                            btnClass: 'btn btn-default'
                        }
                    }
                });
            });

            submit_button.attr('initialized','true');
        }
    });
}

$('#purchase_orders_table').DataTable({
    "order": [[0, "desc"]],
    colReorder: true,
    "processing": true,
    "serverSide": true,
    "ajax": {
        url: base_url + "procurements/purchase_orders/",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false},
        {"orderable": false},
        {"orderable": false},
        {"orderable": false},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords": "<div class='alert alert-info'>No matching purchase orders found</div>",
        "emptyTable": "<div class='alert alert-info'>No purchase orders found</div>"
    }, "drawCallback": function () {
        $(this).find('tr').each(function () {
            $(this).find('td:last-child').attr('nowrap', 'nowrap');
        });
        initialize_requisition_and_order_form();
        initialize_purchase_order_cancellation_form();
        initialize_common_js();
    }
});

$('#purchase_order_grns_table').DataTable({
    "order": [[0, "desc"]],
    colReorder: true,
    "processing": true,
    "serverSide": true,
    "ajax": {
        url: base_url + "procurements/purchase_orders_grns/",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords": "<div class='alert alert-info'>No matching GRNs found</div>",
        "emptyTable": "<div class='alert alert-info'>No GRNs found</div>"
    }
});

$('#vendor_purchase_orders').each(function () {
    var table = $(this);
    if(table.attr('datatable_initialized') != 'true') {
        var vendor_id = table.attr('vendor_id');
        table.DataTable({
            "order": [[0, "desc"]],
            colReorder: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: base_url + "procurements/vendor_purchase_orders/" + vendor_id,
                type: 'POST'
            },
            "columns": [
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": false}
            ],
            "language": {
                "zeroRecords": "<div class='alert alert-info'>No matching purchase orders found</div>",
                "emptyTable": "<div class='alert alert-info'>No purchase orders found</div>"
            }, "drawCallback": function () {
                table.find('tr').each(function () {
                    $(this).find('td:last-child').attr('nowrap', 'nowrap');
                });
                initialize_common_js();
                initialize_requisition_and_order_form();
            }
        });
        table.attr('datatable_initialized',true);
    } else {
        table.DataTable().draw('page');
    }
});

/***************************************************
 * TOOLS AND EQUIPMENT
 ***************************************************/

function save_tools_and_equipment_type(button){
    var modal = button.closest('.modal');
    modal.modal('hide');
    var type_id = modal.find('input[name="type_id"]').val();
    var form_type = modal.find('input[name="form_type"]').val();
    var name = modal.find('input[name="name"]').val();
    var depreciation_rate = modal.find('input[name="depreciation_rate"]').val();
    var description = modal.find('textarea[name="description"]').val();

    $.post(
        base_url + "tools_and_equipment/save_tools_and_equipment_type/",
        {
            type_id: type_id,
            form_type: form_type,
            name: name,
            depreciation_rate: depreciation_rate,
            description: description
        }, function () {
            modal.find('form')[0].reset();
            var table_id = form_type == 'Tool' ? '#tool_types_list' : '#equipment_types_list';
            $(table_id).DataTable().draw('page');
        }
    );
}

$('#tool_types_list, #equipment_types_list').each(function(){
    var id = $(this).attr('id');
    var form_type = id == 'tool_types_list' ? 'Tool' : 'Equipment';

    $(this).DataTable({
        colReorder: true,
        "processing": true,
        "serverSide": true,
        "ajax" : {
            url: base_url + "tools_and_equipment/tools_and_equipment_types_list/"+form_type,
            type: 'POST'
        },
        "columns": [
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": false},
            {"orderable": false}
        ],
        "language": {
            "zeroRecords":     "<div class='alert alert-info'>No matching "+form_type+" types found</div>",
            "emptyTable":     "<div class='alert alert-info'>No  "+form_type+" types found</div>"
        },"drawCallback": function () {
            $(this).find('tr').each(function(){
                $(this).find('td:last-child').attr('nowrap', 'nowrap');
            });
            initialize_common_js();
        }
    });
});

function delete_tool_and_equipment_type(form_type,type_id){
    if(confirm('Are you sure?')){
        $.post(
            base_url + "tools_and_equipment/delete_tool_and_equipment_type",
            {
                type_id: type_id,
                form_type: form_type
            }
        ).complete(function(){
            var table_id = form_type == 'Tool' ? '#tool_types_list' : '#equipment_types_list';
            $(table_id).DataTable().draw('page');
        });
    }
}

$('#equipment_list').DataTable({
    "order": [[ 4, "asc" ]],
    colReorder: true,
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "tools_and_equipment/equipment/",
        type: 'POST'
    },
    "columns": [
        {"orderable": false},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching equipment found</div>",
        "emptyTable":     "<div class='alert alert-info'>No equipment found</div>"
    },"drawCallback": function () {

        $('.save_equipment_button').each(function(){
            var button = $(this);
            if(button.attr('initialized') != 'true') {
                button.click(function () {
                    var modal = button.closest('.modal');
                    var make = modal.find('input[name="make"]').val();
                    var equipment_id = modal.find('input[name="equipment_id"]').val();
                    var sub_location_id = modal.find('select[name="sub_location_id"]').val();
                    var type_id = modal.find('select[name="type_id"]').val();
                    var ownership = modal.find('select[name="ownership"]').val();
                    var identification_number = modal.find('input[name="identification_number"]').val();
                    if(make != '' && type_id != '' && ownership != '' && identification_number != ''  && sub_location_id != '' ) {
                        modal.modal('hide');
                        start_spinner();

                        var model = modal.find('input[name="model"]').val();
                        var value = modal.find('input[name="value"]').unmask();
                        var vendor_id = modal.find('select[name="vendor_id"]').val();
                        var description = modal.find('textarea[name="description"]').val();

                        var captured = modal.find('input[name="thumbnail"]')[0];
                        var file = captured.files[0], form_data = false;

                        if (window.FormData) {
                            form_data = new FormData();
                            if (form_data) {
                                form_data.append("file", file);
                                form_data.append("make", make);
                                form_data.append("type_id", type_id);
                                form_data.append("ownership", ownership);
                                form_data.append("identification_number", identification_number);
                                form_data.append("model", model);
                                form_data.append("value", value);
                                form_data.append("vendor_id", vendor_id);
                                form_data.append("description", description);
                                form_data.append("sub_location_id", sub_location_id);

                                $.ajax({
                                    url: base_url + "tools_and_equipment/save_equipment/" + equipment_id,
                                    type: "POST",
                                    timeout: 250000,
                                    cache: false,
                                    data: form_data,
                                    processData: false,
                                    contentType: false,
                                    complete: function () {
                                        modal.find('form')[0].reset();
                                        stop_spinner();
                                        $('#equipment_list').DataTable().draw('page');
                                    }
                                });
                            }
                        }
                    }
                });
                button.attr('initialized', 'true');
            }
        });

        $('.delete_equipment').each(function () {
            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    var equipment_id = button.attr('equipment_id');
                    if(confirm('Are you sure?')){
                        $.post(
                            base_url + "tools_and_equipment/delete_equipment",
                            {
                                equipment_id : equipment_id
                            }
                        ).complete(function(){
                            $('#equipment_list').DataTable().draw('page');
                        });
                    }
                });
                button.attr('initialized','initialized');
            }
        });

        $(this).find('tr').each(function(){
            $(this).find('td:last-child').attr('nowrap', 'nowrap');
        });
        initialize_common_js();
    }
});

$('#tools_list').DataTable({
    "order": [[ 4, "asc" ]],
    colReorder: true,
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "tools_and_equipment/tools/",
        type: 'POST'
    },
    "columns": [
        {"orderable": false},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching tools found</div>",
        "emptyTable":     "<div class='alert alert-info'>No tools found</div>"
    },"drawCallback": function () {

        $('.save_tool_button').each(function(){
            var button = $(this);
            if(button.attr('initialized') != 'true') {
                button.click(function () {
                    var modal = button.closest('.modal');
                    var make = modal.find('input[name="make"]').val();
                    var type_id = modal.find('select[name="type_id"]').val();
                    var identification_number = modal.find('input[name="identification_number"]').val();
                    var tool_id = modal.find('input[name="tool_id"]').val();
                    var sub_location_id = modal.find('select[name="sub_location_id"]').val();
                    if(make != '' && type_id != '' && identification_number != '' && sub_location_id) {
                        modal.modal('hide');
                        start_spinner();
                        var model = modal.find('input[name="model"]').val();
                        var part_number = modal.find('input[name="part_number"]').val();
                        var value = modal.find('input[name="value"]').unmask();
                        var description = modal.find('textarea[name="description"]').val();

                        var captured = modal.find('input[name="thumbnail"]')[0];
                        var file = captured.files[0], form_data = false;

                        if (window.FormData) {
                            form_data = new FormData();
                            if (form_data) {
                                form_data.append("file", file);
                                form_data.append("make", make);
                                form_data.append("type_id", type_id);
                                form_data.append("identification_number", identification_number);
                                form_data.append("part_number", part_number);
                                form_data.append("model", model);
                                form_data.append("value", value);
                                form_data.append("description", description);
                                form_data.append("sub_location_id", sub_location_id);

                                $.ajax({
                                    url: base_url + "tools_and_equipment/save_tool/" + tool_id,
                                    type: "POST",
                                    timeout: 250000,
                                    cache: false,
                                    data: form_data,
                                    processData: false,
                                    contentType: false,
                                    complete: function () {
                                        modal.find('form')[0].reset();
                                        stop_spinner();
                                        $('#tools_list').DataTable().draw('page');
                                    }
                                });
                            }
                        }
                    }
                });
                button.attr('initialized', 'true');
            }
        });

        $(this).find('tr').each(function(){
            $(this).find('td:last-child').attr('nowrap', 'nowrap');
        });
        initialize_common_js();
    }
});

function delete_tool(tool_id){
    if(confirm('Are you sure?')){
        $.post(
            base_url + "tools_and_equipment/delete_tool/"+tool_id,
            {
                tool_id : tool_id
            }
        ).complete(function(){
            $('#tools_list').DataTable().draw('page');
        });
    }
}

function initialize_location_tools_and_equipment(){
    $('a[href="#location_tools"]').on('shown.bs.tab', function (e) {
        var tab_pane = $($(this).attr('href'));
        tab_pane.find('.location_tools_stock').each(function(){
            var table = $(this);
            if(table.attr('dataTable_initialized') != 'true' ) {
                var location_id = table.attr('location_id');
                table.DataTable({
                    "order": [[ 1, "asc" ]],
                    colReorder: true,
                    "processing": true,
                    "serverSide": true,
                    "ajax" : {
                        url: base_url + "tools_and_equipment/location_tools_stock/"+location_id,
                        type: 'POST'
                    },
                    "columns": [
                        {"orderable": false},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": false}
                    ],
                    "language": {
                        "zeroRecords":     "<div class='alert alert-info'>No matching tools found</div>",
                        "emptyTable":     "<div class='alert alert-info'>No tools found</div>"
                    },"drawCallback": function () {
                        $(this).find('tr').each(function(){
                            $(this).find('td:last-child').attr('nowrap', 'nowrap');
                        });
                        initialize_common_js();
                    }
                });
                table.attr('dataTable_initialized','true');
            } else {
                table.DataTable().draw('page');
            }
        });
    });

    $('.sub_location_tools_stock_activator').on('shown.bs.tab', function (e) {
        var tab_pane = $($(this).attr('href'));
        tab_pane.find('.sub_location_tools_stock').each(function(){
            var table = $(this);
            if(table.attr('dataTable_initialized') != 'true' ) {
                var sub_location_id = table.attr('sub_location_id');
                table.DataTable({
                    "order": [[ 1, "asc" ]],
                    colReorder: true,
                    "processing": true,
                    "serverSide": true,
                    "ajax" : {
                        url: base_url + "tools_and_equipment/sub_location_tools_stock/"+sub_location_id,
                        type: 'POST'
                    },
                    "columns": [
                        {"orderable": false},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": false}
                    ],
                    "language": {
                        "zeroRecords":     "<div class='alert alert-info'>No matching tools found</div>",
                        "emptyTable":     "<div class='alert alert-info'>No tools found</div>"
                    },"drawCallback": function () {
                        $(this).find('tr').each(function(){
                            $(this).find('td:last-child').attr('nowrap', 'nowrap');
                        });
                        initialize_common_js();
                    }
                });
                table.attr('dataTable_initialized','true');
            } else {
                table.DataTable().draw('page');
            }
        });
    });

    $('.sub_location_equipment_stock_activator').on('shown.bs.tab', function (e) {
        var tab_pane = $($(this).attr('href'));
        tab_pane.find('.sub_location_equipment_stock').each(function(){
            var table = $(this);
            if(table.attr('dataTable_initialized') != 'true' ) {
                var sub_location_id = table.attr('sub_location_id');
                table.DataTable({
                    "order": [[ 1, "asc" ]],
                    colReorder: true,
                    "processing": true,
                    "serverSide": true,
                    "ajax" : {
                        url: base_url + "tools_and_equipment/sub_location_equipment_stock/"+sub_location_id,
                        type: 'POST'
                    },
                    "columns": [
                        {"orderable": false},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": false}
                    ],
                    "language": {
                        "zeroRecords":     "<div class='alert alert-info'>No matching equipment found</div>",
                        "emptyTable":     "<div class='alert alert-info'>No equipment found</div>"
                    },"drawCallback": function () {
                        $(this).find('tr').each(function(){
                            $(this).find('td:last-child').attr('nowrap', 'nowrap');
                        });
                        initialize_common_js();
                    }
                });
                table.attr('dataTable_initialized','true');
            } else {
                table.DataTable().draw('page');
            }
        });
    });

    $('a[href="#location_equipment"]').on('shown.bs.tab', function (e) {
        var tab_pane = $($(this).attr('href'));
        tab_pane.find('.location_equipment_stock').each(function(){
            var table = $(this);
            if(table.attr('dataTable_initialized') != 'true' ) {
                var location_id = table.attr('location_id');
                table.DataTable({
                    "order": [[ 1, "asc" ]],
                    colReorder: true,
                    "processing": true,
                    "serverSide": true,
                    "ajax" : {
                        url: base_url + "tools_and_equipment/sub_location_equipment_stock/"+location_id,
                        type: 'POST'
                    },
                    "columns": [
                        {"orderable": false},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": false}
                    ],
                    "language": {
                        "zeroRecords":     "<div class='alert alert-info'>No matching tools found</div>",
                        "emptyTable":     "<div class='alert alert-info'>No tools found</div>"
                    },"drawCallback": function () {
                        $(this).find('tr').each(function(){
                            $(this).find('td:last-child').attr('nowrap', 'nowrap');
                        });
                        initialize_common_js();
                    }
                });
                table.attr('dataTable_initialized','true');
            } else {
                table.DataTable().draw('page');
            }
        });
    });
}

initialize_location_tools_and_equipment();

/***************************************************
 * PROCUREMENTS
 ***************************************************/

$('#vendors_list').DataTable({
    colReorder: true,
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "procurements/vendors/",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching vendors found</div>",
        "emptyTable":     "<div class='alert alert-info'>No vendors found</div>"
    }
});

/*************************************************************************************
 * FINANCE
 *************************************************************************************/

$('#accounts_list').DataTable({
    colReorder: true,
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "finance/accounts_list/",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": false},
        {"orderable": true},
        {"orderable": false},
        {"orderable": false},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching accounts found</div>",
        "emptyTable":     "<div class='alert alert-info'>No accounts found</div>"
    },"drawCallback": function () {

        //Save Account

        $('.save_account').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function () {
                    var modal = button.closest('.modal');
                    var account_id = modal.find('input[name="account_id"]').val();
                    var account_name = modal.find('input[name="account_name"]').val();
                    var account_group_id = modal.find('select[name="account_group_id"]').val();
                    if(account_id == '') {
                        var opening_balance = modal.find('input[name="opening_balance"]').unmask();
                    }
                    var currency_id = modal.find('select[name="currency_id"]').val();
                    var description = modal.find('textarea[name="description"]').val();
                    if(account_name != '' && account_group_id != '') {
                        modal.modal('hide');

                        $.post(
                            base_url + "finance/save_account/",
                            {
                                account_id: account_id,
                                account_name : account_name,
                                account_group_id: account_group_id,
                                currency_id: currency_id,
                                opening_balance: opening_balance,
                                description: description
                            }, function (data) {
                                modal.find('form')[0].reset();
                                $('#accounts_list').DataTable().draw('page');
                            }
                        );
                    }

                });
                button.attr('active', 'true');
            }
        });


        //Delete Account
        $('.delete_account').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function () {
                    var account_id = $(this).attr('account_id');
                    if(confirm('Are you sure?')){
                        start_spinner();
                        $.post(
                            base_url + "finance/delete_account",
                            {
                                account_id: account_id
                            }, function (data) {
                                $('#accounts_list').DataTable().draw('page');
                            }
                        ).complete(function(){
                            stop_spinner();
                        });
                    }
                });
                button.attr('active', 'true');
            }
        });


        $(this).find('tr').each(function () {
            $(this).find('td:last-child').attr('nowrap', 'nowrap');
        });

        initialize_common_js();
    }
});

function load_account_statement_transactions(tab_pane) {
    var container = $('#account_statement_table_container');
    start_spinner();
    var from = tab_pane.find('input[name="from"]').val();
    var to = tab_pane.find('input[name="to"]').val();
    var account_id = tab_pane.find('input[name="account_id"]').val();

    $.post(
        base_url + "finance/account_statement",
        {
            account_id: account_id,
            from : from,
            to : to
        }, function (data) {
            container.html(data);

            container.find('tr').each(function () {
                $(this).find('td:last-child').attr('nowrap', 'nowrap');
            });

            $('a[href="#contras"]').on('shown.bs.tab', function (e){
                var tab_container = $($(this).attr('href'));
                tab_container.find('#account_contras_list').each(function () {
                    var table = $(this);
                    if(table.attr('initialized') != 'true') {
                        var account_id = table.attr('account_id');

                        table.DataTable({
                            "order": [[0, "desc"]],
                            colReorder: true,
                            "processing": true,
                            "serverSide": true,
                            "ajax" : {
                                url: base_url + "finance/account_contras_list/" + account_id,
                                type: 'POST'
                            },
                            "columns": [
                                {"orderable": true},
                                {"orderable": true},
                                {"orderable": true},
                                {"orderable": true},
                                {"orderable": false},
                                {"orderable": true},
                                {"orderable": true},
                                {"orderable": true},
                                {"orderable": false}
                            ],"drawCallback": function () {
                                $(this).find('tr').each(function () {
                                    $(this).find('td:last-child').attr('nowrap', 'nowrap');
                                });

                                $('.row_adder').each(function () {
                                    var button = $(this);
                                    if(button.attr('initialized') != 'true'){
                                        button.click(function () {
                                            var table = button.closest('table');
                                            var tbody = table.find('tbody');
                                            var row_template = table.find('.row_template');
                                            var new_row = row_template.clone().removeAttr('style').removeClass('row_template').addClass('artificial_row').appendTo(tbody);
                                            new_row.find('.row_remover').click(function () {
                                                $(this).closest('tr').remove();
                                            });
                                            new_row.find('.number_format').priceFormat();
                                            new_row.find('select').select2();
                                        });
                                        button.attr('initialized','true');
                                    }
                                });

                                $('.row_remover').click(function () {
                                    $(this).closest('tr').remove();
                                });

                                $('.save_contra').each(function(){
                                    var button = $(this);
                                    if(button.attr('active') != 'true') {
                                        button.click(function(){
                                            var modal = button.closest('.modal');
                                            var contra_date = modal.find('input[name="contra_date"]').val();
                                            var debit_accounts_ids = new Array(), amounts = new Array(), descriptions = new Array(), i = 0;
                                            var tbody = modal.find('tbody');
                                            tbody.find('select[name="debit_account_id"]').each(function(){
                                                var debit_account_id = parseFloat($(this).val());
                                                var row = $(this).closest('tr');
                                                var amount = row.find('input[name="amount"]').unmask();
                                                if(debit_account_id != '' && parseFloat(amount) > 0){
                                                    debit_accounts_ids[i] = debit_account_id;
                                                    amounts[i] = amount;
                                                    descriptions[i] = row.find('textarea[name="description"]').val();
                                                    i++;
                                                }
                                            });

                                            if(contra_date != '' && debit_accounts_ids.length > 0) {
                                                modal.modal('hide');
                                                var credit_account_id = modal.find('input[name="credit_account_id"]').val();
                                                var contra_id = modal.find('input[name="contra_id"]').val();
                                                var reference = modal.find('input[name="reference"]').val();
                                                var remarks = modal.find('textarea[name="remarks"]').val();

                                                $.post(
                                                    base_url + "finance/save_contra/",
                                                    {
                                                        contra_date: contra_date,
                                                        reference: reference,
                                                        contra_id: contra_id,
                                                        credit_account_id: credit_account_id,
                                                        debit_accounts_ids : debit_accounts_ids,
                                                        amounts : amounts,
                                                        descriptions : descriptions,
                                                        remarks: remarks
                                                    }, function (data) {
                                                        modal.find('form')[0].reset();
                                                        tbody.find('.artificial_row').remove();
                                                        if(contra_id == '' ) {
                                                            tbody.find('.searchable').select2('val', '');
                                                        }
                                                        button.closest('.box').find('#account_contras_list').DataTable().draw('page');
                                                        stop_spinner();
                                                    }
                                                );
                                            }
                                        });
                                        button.attr('active','true');
                                    }
                                });

                                $('.delete_contra').each(function(){
                                    var button = $(this);
                                    if(button.attr('active') != 'true') {
                                        button.click(function () {
                                            var contra_id = $(this).attr('contra_id');
                                            if(confirm('Are you sure?')){
                                                start_spinner();
                                                $.post(
                                                    base_url + "finance/delete_contra",
                                                    {
                                                        contra_id: contra_id
                                                    }, function (data) {
                                                        $('#account_contras_list').DataTable().draw('page');
                                                    }
                                                ).complete(function(){
                                                    stop_spinner();
                                                });
                                            }
                                        });
                                        button.attr('active', 'true');
                                    }
                                });

                                initialize_common_js();
                            },"language": {
                                "zeroRecords":     "<div class='alert alert-info'>No matching contras found</div>",
                                "emptyTable":     "<div class='alert alert-info'>No contras found</div>"
                            }
                        });

                        table.attr('initialized','true');
                    } else {
                        table.DataTable().draw('page');
                    }

                });
            });

            $('a[href="#payment_vouchers"]').on('shown.bs.tab', function (e){
                var tab_container = $($(this).attr('href'));
                tab_container.find('#account_payment_vouchers_list').each(function () {
                    var table = $(this);
                    if(table.attr('initialized') != 'true') {
                        var account_id = table.attr('account_id');

                        table.DataTable({
                            "order": [[0, "desc"]],
                            colReorder: true,
                            "processing": true,
                            "serverSide": true,
                            "ajax" : {
                                url: base_url + "finance/account_payment_vouchers_list/" + account_id,
                                type: 'POST'
                            },
                            "columns": [
                                {"orderable": true},
                                {"orderable": true},
                                {"orderable": true},
                                {"orderable": true},
                                {"orderable": false},
                                {"orderable": true},
                                {"orderable": true},
                                {"orderable": true},
                                {"orderable": false}
                            ],"drawCallback": function () {

                                table.find('tr').each(function () {
                                    $(this).find('td:last-child').attr('nowrap', 'nowrap');
                                });

                                $('.row_adder').each(function () {
                                    var button = $(this);
                                    if(button.attr('initialized') != 'true'){
                                        button.click(function () {
                                            var table = button.closest('table');
                                            var tbody = table.find('tbody');
                                            var row_template = table.find('.row_template');
                                            var new_row = row_template.clone().removeAttr('style').removeClass('row_template').addClass('artificial_row').appendTo(tbody);
                                            new_row.find('.row_remover').click(function () {
                                                $(this).closest('tr').remove();
                                            });
                                            new_row.find('.number_format').priceFormat();
                                            new_row.find('select').select2();
                                        });
                                        button.attr('initialized','true');
                                    }
                                });

                                $('.row_remover').click(function () {
                                    $(this).closest('tr').remove();
                                });

                                $('.save_expense_payment_voucher').each(function(){
                                    save_expense_payment_voucher($(this));
                                });

                                $('.save_vendor_payment_voucher').each(function(){
                                    var button = $(this);
                                    if(button.attr('active') != 'true') {
                                        button.click(function(){
                                            var modal = button.closest('.modal');
                                            var payment_date = modal.find('input[name="payment_date"]').val();
                                            var payee = modal.find('input[name="payee"]').val();
                                            var amount = modal.find('input[name="amount"]').unmask();
                                            var debit_account_id = modal.find('select[name="debit_account_id"]').val();

                                            if(payment_date.trim() != '' && debit_account_id != '' && parseFloat(amount) > 0) {
                                                modal.modal('hide');
                                                var credit_account_id = modal.find('input[name="credit_account_id"]').val();
                                                var payment_voucher_id = modal.find('input[name="payment_voucher_id"]').val();
                                                var reference = modal.find('input[name="reference"]').val();
                                                var remarks = modal.find('textarea[name="remarks"]').val();

                                                $.post(
                                                    base_url + "finance/save_vendor_payment_voucher/",
                                                    {
                                                        payment_date: payment_date,
                                                        payee : payee,
                                                        reference: reference,
                                                        payment_voucher_id: payment_voucher_id,
                                                        credit_account_id: credit_account_id,
                                                        debit_account_id : debit_account_id,
                                                        amount : amount,
                                                        remarks: remarks
                                                    }, function (data) {
                                                        modal.find('form')[0].reset();
                                                        button.closest('.box').find('#account_payment_vouchers_list').DataTable().draw('page');
                                                        stop_spinner();
                                                    }
                                                );
                                            }
                                        });
                                        button.attr('active','true');
                                    }
                                });

                                $('.delete_payment_voucher').each(function(){
                                    var button = $(this);
                                    if(button.attr('active') != 'true') {
                                        button.click(function () {
                                            var payment_voucher_id = $(this).attr('payment_voucher_id');
                                            if(confirm('Are you sure?')){
                                                start_spinner();
                                                $.post(
                                                    base_url + "finance/delete_payment_voucher",
                                                    {
                                                        payment_voucher_id: payment_voucher_id
                                                    }, function (data) {
                                                        $('#account_payment_vouchers_list').DataTable().draw('page');
                                                    }
                                                ).complete(function(){
                                                    stop_spinner();
                                                });
                                            }
                                        });
                                        button.attr('active', 'true');
                                    }
                                });

                                initialize_common_js();
                            },"language": {
                                "zeroRecords":     "<div class='alert alert-info'>No matching payment vouchers found</div>",
                                "emptyTable":     "<div class='alert alert-info'>No payment vouchers found</div>"
                            }
                        });

                        table.attr('initialized','true');
                    } else {
                        table.DataTable().draw('page');
                    }

                });
            });

            $('a[href="#approved_cash"]').on('shown.bs.tab', function (e){

                var tab_container = $($(this).attr('href'));
                draw_approved_cash_datatable(tab_container.find('.approved_cash_requisitions_table'));

            });

            stop_spinner();
        }
    );
}

function draw_approved_cash_datatable(table) {
    if(table.attr('initialized') != 'true') {
        var account_id = table.attr('account_id');

        table.DataTable({
            "order": [[0, "desc"]],
            colReorder: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: base_url + "finance/approved_cash_requisitions_list/" + account_id,
                type: 'POST'
            },
            "columns": [
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": false},
                {"orderable": true},
                {"orderable": false}
            ], "drawCallback": function () {

                $('.payment_voucher_form, .imprest_voucher_form').each(function(){

                    var modal=$(this);

                    var items_table = modal.find('table');

                    items_table.delegate(' input[name="rate"],  input[name="quantity"]','change keyup',function() {

                        var item=$(this);

                        var tr=item.closest('tr');

                        var rate = tr.find(' input[name="rate"]').unmask();
                        var quantity = tr.find(' input[name="quantity"]').val();
                        var amount = parseFloat(rate) * parseFloat(quantity);
                        tr.find(' input[name="amount"]').val(amount).priceFormat();

                        var total_amount = 0;
                        items_table.find('tbody input[name="amount"]').each(function(){
                            $(this).priceFormat();
                            var amount = $(this).val();
                            amount = amount != '' ? parseFloat($(this).unmask()) : 0;
                            total_amount += amount;
                        });

                        items_table.find('.total_amount_display').html(total_amount).priceFormat();

                    });

                    calculate_table_total_amount(items_table);

                    modal.on('change', '.location_selector',function () {
                        var location_id = modal.find('select[name="location_id"]').val();
                        var sub_location_selector = modal.find('.sub_location_id');
                        start_spinner();
                        $.post(
                            base_url + "Finance/load_sub_location_options",
                            {
                                location_id: location_id

                            }, function (data) {

                                sub_location_selector.html(data.sub_location_options);

                                stop_spinner();

                                initialize_common_js();

                            }, 'json'
                        ).complete();

                    });

                    //save payment voucher

                    modal.find('.save_approved_cash_payment_voucher').each(function(){

                        var button=$(this);

                        if(button.attr('active') != 'true') {
                            button.click(function(){
                                var payment_date = modal.find('input[name="payment_date"]').val();
                                var payee = modal.find('input[name="payee"]').val();
                                var exchange_rate = modal.find('input[name="exchange_rate"]').val();
                                var credit_account_id = modal.find('select[name="credit_account_id"]').val();
                                var debit_accounts_ids = new Array(), rates = new Array(), amounts = new Array(), descriptions= new Array(), quantities = new Array(), item_ids = new Array(), item_types= new Array(), i = 0;
                                var tbody = modal.find('tbody');
                                tbody.find('select[name="debit_account_id"]').each(function(){

                                    var debit_account_id = $(this).val();
                                    var row = $(this).closest('tr');
                                    var amount = row.find('input[name="amount"]').unmask();
                                    var description = row.find('input[name="description"]').val();
                                    var rate = row.find('input[name="rate"]').unmask();
                                    var quantity = row.find('input[name="quantity"]').val();
                                    var item_id = row.find('input[name="item_id"]').val();
                                    var item_type = row.find('input[name="item_type"]').val();
                                    if(debit_account_id.trim() !== '' && parseFloat(amount) > 0){
                                        debit_accounts_ids[i] = debit_account_id;
                                        amounts[i] = amount;
                                        descriptions[i]=description;
                                        rates[i] = rate;
                                        quantities[i] = quantity;
                                        item_ids[i] = item_id;
                                        item_types[i] = item_type;
                                        i++;
                                    }
                                });

                                if(credit_account_id.trim() != '' && payment_date.trim() != '' && debit_accounts_ids.length > 0 && payee.trim() != '') {
                                    modal.modal('hide');
                                    var requisition_approval_id = modal.find('input[name="requisition_approval_id"]').val();
                                    var payment_voucher_id = modal.find('input[name="payment_voucher_id"]').val();
                                    var reference = modal.find('input[name="reference"]').val();
                                    var remarks = modal.find('textarea[name="remarks"]').val();

                                    $.post(
                                        base_url + "finance/save_approved_cash_payment_voucher/",
                                        {
                                            payment_date: payment_date,
                                            payee : payee,
                                            exchange_rate : exchange_rate,
                                            reference: reference,
                                            payment_voucher_id: payment_voucher_id,
                                            requisition_approval_id: requisition_approval_id,
                                            credit_account_id: credit_account_id,
                                            quantities : quantities,
                                            debit_accounts_ids:debit_accounts_ids,
                                            rates : rates,
                                            amounts : amounts,
                                            descriptions:descriptions,
                                            item_ids :item_ids,
                                            item_types:item_types,
                                            remarks: remarks
                                        }, function (data) {
                                            modal.find('form')[0].reset();
                                            toast('success','Payment Voucher has been Created');
                                            tbody.find('.artificial_row').remove();
                                            if(payment_voucher_id == '' ) {
                                                tbody.find('.searchable').select2('val', '');
                                            }

                                            table.DataTable().draw('page');
                                            stop_spinner();
                                        }
                                    );
                                } else {
                                    display_form_fields_error();
                                }
                            });
                            button.attr('active','true');
                        }

                    });

                    //save impress voucher

                    modal.find('.save_imprest_voucher').each(function () {
                        var button = $(this);
                        if(button.attr('initilized') != 'true'){
                            button.click(function () {
                                var payment_voucher_id = modal.find('input[name="payment_voucher_id"]').val();
                                var issue_date = modal.find('input[name="issue_date"]').val();
                                var location_id = modal.find('select[name="location_id"]').val();
                                var sub_location_id = modal.find('select[name="sub_location_id"]').val();
                                var remarks = modal.find('textarea[name="remarks"]').val();
                                var item_types = Array(), descriptions = Array(), item_ids = Array(), quantities = Array(),rates = Array(), i = 0, material_items_counter = 0;

                                modal.find('input[name="item_type"]').each(function () {
                                    var row = $(this).closest('tr');
                                    item_types[i] = $(this).val();
                                    if(item_types[i] == 'material'){
                                        material_items_counter++;
                                    }
                                    descriptions[i] = row.find('input[name="description"]').val();
                                    item_ids[i] = row.find('input[name="item_id"]').val();
                                    quantities[i] = row.find('input[name="quantity"]').val();
                                    rates[i] = row.find('input[name="rate"]').unmask();

                                    i++;
                                });

                                if(issue_date.trim() != '' && (material_items_counter == 0 || (sub_location_id != '' && sub_location_id != null))){
                                    start_spinner();
                                    modal.modal('hide');
                                    $.post(
                                        base_url + "finance/save_imprest_voucher",
                                        {
                                            issue_date: issue_date,
                                            payment_voucher_id:payment_voucher_id,
                                            rates:rates,
                                            location_id : location_id,
                                            sub_location_id : sub_location_id,
                                            quantities:quantities,
                                            descriptions:descriptions,
                                            item_types:item_types,
                                            material_items_counter: material_items_counter,
                                            item_ids:item_ids,
                                            remarks:remarks
                                        }, function (data) {
                                            toast('success','Imprest Submitted Successfully');
                                            table.DataTable().draw('page');
                                            stop_spinner();
                                        }
                                    );
                                } else {
                                    display_form_fields_error();
                                }

                            });
                            button.attr('initilized','true');
                        }
                    });

                });


                //Save Imprest Voucher
                /*$('.save_imprest_voucher').each(function(){

                    var button=$(this);

                    if(button.attr('active') != 'true') {
                        button.click(function(){
                            var modal = button.closest('.modal');
                            var issue_date = modal.find('input[name="issue_date"]').val();
                            var sub_location_id = modal.find('select[name="sub_location_id"]').val();
                            var rates = new Array(), amounts = new Array(), descriptions= new Array(), quantities = new Array(), item_ids = new Array(), item_types= new Array(), i = 0;
                            var tbody = modal.find('tbody');
                            var material_items_count = 0;
                            tbody.find('input[name="item_id"]').each(function(){

                                var item_id = $(this).val();

                                var row = $(this).closest('tr');
                                var amount = row.find('input[name="amount"]').unmask();
                                var description = row.find('input[name="description"]').val();
                                var rate = row.find('input[name="rate"]').unmask();
                                var quantity = row.find('input[name="quantity"]').val();
                                var item_type = row.find('input[name="item_type"]').val();
                                if(item_type == 'material'){
                                    material_items_count++;
                                }
                                if(item_id != '' && parseFloat(amount) > 0){
                                    amounts[i] = amount;
                                    descriptions[i]=description;
                                    rates[i] = rate;
                                    quantities[i] = quantity;
                                    item_ids[i] = item_id;
                                    item_types[i] = item_type;
                                    i++;
                                }
                            });



                            if(issue_date.trim() != '' && quantities.length > 0 && (material_items_count == 0 || sub_location_id != '')) {
                                modal.modal('hide');
                                var payment_voucher_id = modal.find('input[name="payment_voucher_id"]').val();
                                var imprest_id = modal.find('input[name="imprest_id"]').val();
                                var remarks = modal.find('textarea[name="remarks"]').val();

                                $.post(
                                    base_url + "finance/save_imprest_voucher/",
                                    {
                                        issue_date: issue_date,
                                        imprest_id: imprest_id,
                                        payment_voucher_id: payment_voucher_id,
                                        sub_location_id: sub_location_id,
                                        quantities : quantities,
                                        rates : rates,
                                        amounts : amounts,
                                        descriptions:descriptions,
                                        item_ids :item_ids,
                                        item_types:item_types,
                                        remarks: remarks
                                    }, function (data) {
                                        toast('success',data);
                                        modal.find('form')[0].reset();
                                        toast('success','Imprest Voucher has been Created');
                                        tbody.find('.artificial_row').remove();

                                        button.closest('.box').find('#account_payment_vouchers_list, #account_cash_requisitions_list').DataTable().draw('page');
                                        stop_spinner();
                                    }
                                );
                            } else {
                                display_form_fields_error();
                            }
                        });
                        button.attr('active','true');
                    }

                });*/


                initialize_common_js();

            }, "language": {
                "zeroRecords": "<div class='alert alert-info'>No matching approved cash requisitions found</div>",
                "emptyTable": "<div class='alert alert-info'>No approved cash requisitions found</div>"
            }
        });

        table.attr('initialized','true');
    } else {
        table.DataTable().draw('page');
    }
}

draw_approved_cash_datatable($('.approved_cash_requisitions_table'));

function save_expense_payment_voucher(button) {
    if(button.attr('active') != 'true') {
        button.click(function(){
            var modal = button.closest('.modal');
            var payment_date = modal.find('input[name="payment_date"]').val();
            var payee = modal.find('input[name="payee"]').val();
            var payment_voucher_type = modal.find('input[name="payment_voucher_type"]').val();
            var debit_accounts_ids = new Array(), cost_center_types = new Array(), cost_center_ids = new Array(), amounts = new Array(), descriptions = new Array(), i = 0;
            var tbody = modal.find('tbody');
            tbody.find('select[name="debit_account_id"]').each(function(){
                var debit_account_id = $(this).val();
                var row = $(this).closest('tr');
                var amount = row.find('input[name="amount"]').unmask();
                if(debit_account_id != '' && parseFloat(amount) > 0){
                    debit_accounts_ids[i] = debit_account_id;
                    amounts[i] = amount;
                    cost_center_types[i] = row.find('select[name="cost_center_type"]').val();
                    cost_center_ids[i] = row.find('select[name="cost_center_id"]').val();
                    descriptions[i] = row.find('textarea[name="description"]').val();
                    i++;
                }
            });

            if(payment_date.trim() != '' && debit_accounts_ids.length > 0 && payee.trim() != '') {
                modal.modal('hide');
                var cash_requisition_id = modal.find('input[name="cash_requisition_id"]').val();
                var credit_account_id = modal.find('input[name="credit_account_id"]').val();
                var payment_voucher_id = modal.find('input[name="payment_voucher_id"]').val();
                var reference = modal.find('input[name="reference"]').val();
                var remarks = modal.find('textarea[name="remarks"]').val();

                $.post(
                    base_url + "finance/save_expense_payment_voucher/",
                    {
                        payment_date: payment_date,
                        payee : payee,
                        payment_voucher_type : payment_voucher_type,
                        reference: reference,
                        payment_voucher_id: payment_voucher_id,
                        cash_requisition_id: cash_requisition_id,
                        credit_account_id: credit_account_id,
                        debit_accounts_ids : debit_accounts_ids,
                        cost_center_types : cost_center_types,
                        cost_center_ids : cost_center_ids,
                        amounts : amounts,
                        descriptions : descriptions,
                        remarks: remarks
                    }, function () {
                        modal.find('form')[0].reset();
                        toast('success','Payment Voucher has been saved');
                        tbody.find('.artificial_row').remove();
                        if(payment_voucher_id == '' ) {
                            tbody.find('.searchable').select2('val', '');
                        }
                        button.closest('.box').find('#account_payment_vouchers_list, #account_cash_requisitions_list').DataTable().draw('page');
                        stop_spinner();
                    }
                );
            } else {
                display_form_fields_error();
            }
        });
        button.attr('active','true');
    }
}

$('#account_statement_table_container').each(function () {
    var container = $(this);
    if(container.attr('initialized') != 'true'){
        var tab_pane = $('#account_statement');

        load_account_statement_transactions(tab_pane);


        tab_pane.find(' input[name="from"],  input[name="to"]').change(function() {
            load_account_statement_transactions(tab_pane);
        });

        container.attr('initialized','true');
    }
});

$('#account_groups_list').DataTable({
    colReorder: true,
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "finance/account_groups_list/",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": false},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching account groups found</div>",
        "emptyTable":     "<div class='alert alert-info'>No account groups found</div>"
    },"drawCallback": function () {

        //Save Account Group

        $('.save_account_group').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function () {
                    var modal = button.closest('.modal');
                    var account_group_id = modal.find('input[name="account_group_id"]').val();
                    var account_group_name = modal.find('input[name="account_group_name"]').val();
                    var parent_group_id = modal.find('select[name="parent_group_id"]').val();
                    var description = modal.find('textarea[name="description"]').val();
                    if(account_group_name != '' && parent_group_id != '') {
                        modal.modal('hide');

                        $.post(
                            base_url + "finance/save_account_group/",
                            {
                                account_group_id: account_group_id,
                                account_group_name : account_group_name,
                                parent_group_id: parent_group_id,
                                description: description
                            }, function () {
                                modal.find('form')[0].reset();
                                $('#account_groups_list').DataTable().draw('page');
                            }
                        );
                    }
                });
                button.attr('active', 'true');
            }
        });


        //Delete Account Group
        $('.delete_account_group').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function () {
                    var account_id = $(this).attr('account_id');
                    if(confirm('Are you sure?')){
                        start_spinner();
                        $.post(
                            base_url + "finance/delete_account_group",
                            {
                                account_group_id: account_group_id
                            }, function (data) {
                                $('#account_groups_list').DataTable().draw('page');
                            }
                        ).complete(function(){
                            stop_spinner();
                        });
                    }
                });
                button.attr('active', 'true');
            }
        });


        $(this).find('tr').each(function () {
            $(this).find('td:last-child').attr('nowrap', 'nowrap');
        });
        initialize_common_js();
    }
});

$('#currencies_table').DataTable({
    colReorder: true,
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "finance/currencies_list/",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": false},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching currencies found</div>",
        "emptyTable":     "<div class='alert alert-info'>No currencies found</div>"
    },"drawCallback": function () {
        $('.save_currency').each(function () {
            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    var form_container = button.closest('form');
                    var currency_name = form_container.find('  input[name="currency_name"]').val();
                    var symbol = form_container.find('  input[name="symbol"]').val();
                    var rate_to_native = form_container.find('  input[name="rate_to_native"]').unmask();
                    var currency_id = form_container.find('  input[name="currency_id"]').val();

                    if(currency_name.trim() != '' && symbol.trim() != '' && (currency_id != '' || rate_to_native > 0 )) {
                        var modal = form_container.closest('.modal');
                        modal.modal('hide');
                        start_spinner();
                        $.post(
                            base_url + "finance/save_currency/",
                            {
                                currency_name: currency_name,
                                symbol: symbol,
                                rate_to_native: rate_to_native,
                                currency_id: currency_id
                            }, function () {
                                stop_spinner();
                                modal.find('form')[0].reset();
                                iziToast.success({
                                    title: '',
                                    message: 'Currency was Successfully saved!',
                                });
                                $('#currencies_table').DataTable().draw('page');
                            }
                        );
                    } else {
                        display_form_fields_error();
                    }
                });
                button.attr('initialized','true');
            }
        });

        $('.delete_currency').each(function () {
            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    if(confirm('Are You Sure?')){
                        $.post(
                            base_url + "finance/delete_currency",
                            {
                                currency_id: button.attr('currency_id')
                            }, function () {
                                toast('success','Currency was successfully deleted');
                                $('#currencies_table').DataTable().draw('page');
                            }
                        );
                    }
                });
                button.attr('initialized','true')
            }
        });

        $('#save_exchange_rates').each(function () {
            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    var modal = button.closest('.modal');
                    var date = modal.find('input[name="date"]').val();
                    var currency_ids = Array(), exchange_rates = Array();
                    var i = 0;
                    modal.find(' input[name="currency_id"]').each(function () {
                        var exchange_rate = $(this).closest('tr').find('input[name="exchange_rate"]').unmask();
                        if (parseFloat(exchange_rate) > 0) {
                            currency_ids[i] = $(this).val();
                            exchange_rates[i] = exchange_rate;
                        }
                        i++;
                    });

                    if(currency_ids.length > 0){
                        start_spinner();
                        modal.modal('hide');
                        $.post(
                            base_url + "finance/update_exchange_rates",
                            {
                                date: date,
                                currency_ids: currency_ids,
                                exchange_rates: exchange_rates
                            }, function () {
                                stop_spinner();
                                toast('success','Exchange rates has been updated');
                                $('#currencies_table').DataTable().draw('page');
                            }
                        );
                    }

                });
                button.attr('initialized','true');
            }
        });

        initialize_common_js();
        $('.exchange_rate').priceFormat();
    }
});

/**************************************************************
 * APPROVAL SETTINGS
 */

$('.chain_levels_table').each(function () {
    var approval_module_id = $(this).attr('approval_module_id');
    var table = $(this);
    var panel_body = table.closest('.panel-body');
    var after_level_field = panel_body.find('select[name="after_level"]');
    var load_table_content = function () {
        start_spinner();
        $.post(
            base_url + "administrative_actions/load_approval_chain_levels",
            {
                approval_module_id: approval_module_id
            }, function (data) {
                table.html(data.table);
                after_level_field.html(data.chain_levels_options);
                after_level_field.trigger('change');
                panel_body.find('.save_approval_chain').each(function () {
                    var button = $(this);
                    if(button.attr('initialized') != 'true'){
                        button.click(function () {

                            var  panel_body=button.closest('.next_form');
                            var approval_module_id = panel_body.find("input[name='approval_module_id']").val();
                            var label = panel_body.find("select[name='approval_label']").val();
                            var job_position_id=panel_body.find("select[name='job_position_id']").val();
                            var after_level=panel_body.find("select[name='after_level']").val();
                            var change_source=panel_body.find("select[name='change_source']").val();


                            if(job_position_id != '' && label !=''){
                                start_spinner();
                                $.post(
                                    base_url + "Administrative_actions/save_approval_settings",
                                    {
                                        approval_module_id:approval_module_id,
                                        label:label,
                                        after_level:after_level,
                                        job_position_id:job_position_id,
                                        change_source:change_source,

                                    },function (data) {
                                        load_table_content();
                                        stop_spinner();
                                        initialize_common_js();

                                    }
                                );
                            } else {

                                toast('warning','Job Position and Label Must be filled ');
                            }
                        });
                        button.attr('initialized','true');
                    }
                });

                panel_body.find('.delete_chain_level').each(function(){
                    var button = $(this);
                    if(button.attr('initialized') != 'true') {
                        button.click(function () {
                            if(confirm('Are you sure?')){
                                start_spinner();
                                $.post(
                                    base_url + "Administrative_actions/delete_chain_level",
                                    {
                                        approval_chain_level_id: button.attr('approval_chain_level_id')

                                    }, function () {
                                        load_table_content();
                                    }
                                ).complete(function(){
                                    stop_spinner();
                                    initialize_common_js();
                                });
                            }
                        });
                        button.attr('initialized', 'true');
                    }
                });

                //disable_chain_level
                panel_body.find('.disable_chain_level').each(function(){
                    var button = $(this);
                    if(button.attr('active') != 'true') {
                        button.click(function () {
                            if(confirm('Are you sure?')){
                                start_spinner();
                                $.post(
                                    base_url + "Administrative_actions/disable_chain_level",
                                    {
                                        approval_chain_level_id: button.attr('approval_chain_level_id')

                                    }, function (data) {
                                        load_table_content();
                                    }
                                ).complete(function(){
                                    stop_spinner();
                                });
                            }
                        });
                        button.attr('active', 'true');
                    }
                });

                //enable_chain_level
                panel_body.find('.enable_chain_level').each(function(){
                    var button = $(this);
                    if(button.attr('active') != 'true') {
                        button.click(function () {
                            if(confirm('Are you sure?')){
                                start_spinner();
                                $.post(
                                    base_url + "Administrative_actions/enable_chain_level",
                                    {
                                        approval_chain_level_id: button.attr('approval_chain_level_id')

                                    }, function (data) {
                                        load_table_content();
                                    }
                                ).complete(function(){
                                    stop_spinner();
                                    initialize_common_js();
                                });
                            }
                        });
                        button.attr('active', 'true');
                    }
                });
                stop_spinner();
            },
            'json'
        ).complete();
    };

    load_table_content();
});

$('#cost_centers_list').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "Finance/cost_centers_list",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching Cost Centers found</div>",
        "emptyTable":     "<div class='alert alert-info'>No Cost Centers found</div>"
    },"drawCallback": function () {

        $(this).find('tr').each(function () {
            $(this).find('td:last-child').attr('nowrap', 'nowrap');
        });

        $('.save_cost_center').each(function () {
            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    var modal = button.closest('.modal');
                    var cost_center_id = modal.find("input[name='cost_center_id']").val();
                    var cost_center_name = modal.find("input[name='cost_center_name']").val();
                    var description = modal.find("textarea[name='description']").val();

                    if(cost_center_name.trim() != ''){
                        start_spinner();
                        modal.modal('hide');
                        $.post(
                            base_url + "Finance/save_cost_center",
                            {
                                cost_center_name:cost_center_name,
                                cost_center_id: cost_center_id,
                                description:description
                            },function () {
                                stop_spinner();
                                modal.find('form')[0].reset();
                                $('#cost_centers_list').DataTable().draw('page');
                            }
                        );
                    } else {
                        toast('warning','Cost Center Name Must be filled ');
                    }
                });
                button.attr('initialized','true');
            }
        });

        //Delete cost center
        $('.delete_cost_center_button').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function () {
                    if(confirm('Are you sure?')){
                        start_spinner();
                        $.post(
                            base_url + "Finance/delete_cost_center",
                            {
                                cost_center_id: button.attr('cost_center_id')

                            }, function (data) {

                                toast('success','Deleted Successfully');

                                $('#cost_centers_list').DataTable().draw('page');
                            }
                        ).complete(function(){

                            stop_spinner();
                        });
                    }
                });

                button.attr('active', 'true');
            }
        });

        initialize_common_js();
    }
});

/****************
 * SUB-CONTRACTORS
 *****************/

$('#sub_contractors_list').DataTable({
    colReorder: true,
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "sub_contractors/sub_contractors_list/",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching sub-contractors found</div>",
        "emptyTable":     "<div class='alert alert-info'>No sub-contractors found</div>"
    }
});

$('#sub_contracts_list').each(function () {
    var sub_contractor_id = $(this).attr('sub_contractor_id');
    $(this).DataTable({
        colReorder: true,
        "processing": true,
        "serverSide": true,
        "ajax" : {
            url: base_url + "sub_contractors/sub_contracts_list/"+sub_contractor_id,
            type: 'POST'
        },
        "columns": [
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true}
            //{"orderable": false}
        ],
        "language": {
            "zeroRecords":     "<div class='alert alert-info'>No matching sub-contracts found</div>",
            "emptyTable":     "<div class='alert alert-info'>No sub-contracts found</div>"

        },"drawCallback": function () {


            $(this).find('tr').each(function () {
                $(this).find('td:last-child').attr('nowrap', 'nowrap');
            });

            initialize_common_js();
        }


    });
});

$('a[href="#project_sub_contracts"]').on('shown.bs.tab', function (e){
    $('#project_sub_contracts').find('#sub_contracts_list_table').each(function(){
        if($(this).attr('dataTable_initialized') != 'true') {

            var project_id = $(this).attr('project_id');
            $(this).DataTable({
                colReorder: true,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: base_url + "Sub_contractors/project_sub_contracts_list/"+project_id,
                    type: 'POST'
                },
                "columns": [
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    // {"orderable": true},
                    {"orderable": false}
                ],
                "language": {
                    "zeroRecords":     "<div class='alert alert-info'>No matching sub-contracts found</div>",
                    "emptyTable":     "<div class='alert alert-info'>No sub-contracts found</div>"

                },
                "drawCallback": function (){

                    $('.sub_contract_profile').on('shown.bs.modal', function () {
                        var modal = $(this);
                        var load_project_sub_contract_items = function () {
                            var items_contents_area = modal.find('.items_contents_area');
                            items_contents_area.html('<div class="alert alert-info">Loading items........ </div>');
                            var sub_contract_id = modal.find("input[name='sub_contract_id']").val();
                            start_spinner();
                            $.post(
                                base_url + "Sub_contractors/load_sub_contract_items",
                                {
                                    sub_contract_id: sub_contract_id
                                }, function (data) {
                                    items_contents_area.html(data);

                                    $('.save_sub_contract_item').each(function(){

                                        var button = $(this);
                                        if(button.attr('active') != 'true') {

                                            button.click(function () {

                                                var modal = button.closest('.modal');

                                                var sub_contract_id = modal.find('input[name="sub_contract_id"]').val();
                                                var task_id = modal.find('select[name="task_id"]').val();
                                                var start_date = modal.find('input[name="start_date"]').val();
                                                var end_date = modal.find('input[name="end_date"]').val();
                                                var contract_sum = modal.find('input[name="contract_sum"]').unmask();
                                                var description = modal.find('input[name="description"]').val();

                                                if(sub_contract_id != '' && start_date != '') {
                                                    start_spinner();
                                                    $.post(
                                                        base_url + "Sub_contractors/save_sub_contract_item/",
                                                        {
                                                            sub_contract_id:sub_contract_id,
                                                            task_id:task_id,
                                                            start_date:start_date,
                                                            end_date:end_date,
                                                            contract_sum:contract_sum,
                                                            description:description
                                                        }, function (data) {
                                                            stop_spinner();
                                                            load_project_sub_contract_items();
                                                            initialize_common_js();
                                                            modal.find('form')[0].reset();

                                                        }
                                                    )
                                                } else {
                                                    toast('error','Please make sure all fields are correctly filled');
                                                }
                                            });
                                            button.attr('active', 'true');
                                        }
                                    });

                                    $('.delete_sub_contract_item').each(function(){
                                        var button = $(this);
                                        if(button.attr('active') != 'true') {
                                            button.click(function () {

                                                if(confirm('Are you sure?')){
                                                    start_spinner();
                                                    $.post(
                                                        base_url + "sub_contractors/delete_sub_contract_item",
                                                        {
                                                            sub_contract_item_id: button.attr('sub_contract_item_id')
                                                        }, function () {
                                                            button.closest('tr').remove();
                                                        }
                                                    ).complete(function(){
                                                        toast('success','sub_contract_item Deleted ');
                                                        load_project_sub_contract_items();
                                                        stop_spinner();
                                                    });
                                                }
                                            });
                                            button.attr('active', 'true');
                                        }
                                    });

                                    stop_spinner();
                                }
                            );
                        };
                        load_project_sub_contract_items();
                    });

                    //Save Sub_contracts
                    $('.save_project_sub_contract').off('click').on('click',function () {

                        var button = $(this);
                        var modal = button.closest('.modal');

                        var project_id = modal.find("input[name='project_id']").val();
                        var sub_contract_id = modal.find("input[name='sub_contract_id']").val();
                        var sub_contractor_id = modal.find("select[name='sub_contractor_id']").val();
                        var contract_name = modal.find("input[name='contract_name']").val();
                        var contract_date = modal.find("input[name='contract_date']").val();
                        var description = modal.find("textarea[name='description']").val();

                        if(contract_date.trim() != ''){
                            start_spinner();
                            modal.modal('hide');
                            $.post(
                                base_url + "sub_contractors/save_project_sub_contract",
                                {
                                    project_id: project_id,
                                    sub_contract_id: sub_contract_id,
                                    sub_contractor_id: sub_contractor_id,
                                    contract_name: contract_name,
                                    contract_date: contract_date,
                                    description: description

                                },function () {
                                    stop_spinner();
                                    modal.find('form')[0].reset();
                                    $('#sub_contracts_list_table').DataTable().draw('page');
                                    toast('success','Contract Added successful ');
                                }
                            );
                        } else {
                            toast('warning','Contract Date Name Must be filled ');
                        }

                        button.attr('initialized','true');

                    });

                    $('.delete_project_sub_contract').each(function(){
                        var button = $(this);
                        if(button.attr('active') != 'true') {
                            button.click(function () {

                                var sub_contract_id = $(this).attr('sub_contract_id');

                                if(confirm('Are you sure?')){
                                    start_spinner();
                                    $.post(
                                        base_url + "Sub_contractors/delete_project_sub_contract",
                                        {
                                            sub_contract_id: sub_contract_id

                                        }, function (data) {
                                            $('#sub_contracts_list_table').DataTable().draw('page');
                                        }
                                    ).complete(function(){
                                        stop_spinner();
                                    });
                                }
                            });
                            button.attr('active', 'true');
                        }
                    });

                    $(this).find('tr').each(function () {
                        $(this).find('td:last-child').attr('nowrap', 'nowrap');
                    });

                    initialize_common_js();
                }
            });
            $(this).attr('dataTable_initialized','true');
        } else {
            $(this).DataTable().draw('page');
        }
    });
});


/**
 * REPORTS
 */

$('#generate_project_summary_report').click(function () {
    var box = $(this).closest('.box');
    var form = $(this).closest('form');
    var from = form.find('input[name="from"]').val();
    var to = form.find('input[name="to"]').val();
    var project_id = form.find('select[name="project_id"]').val().trim();

    if(project_id != ''){
        start_spinner();
        $.post(
            base_url + "reports/project_summary",
            {
                project_id: project_id,
                from:from,
                to:to
            }, function (data) {
                box.find('#report_container').html(data.table_view);
                stop_spinner();

                Highcharts.setOptions({
                    lang: {
                        decimalPoint: '.',
                        thousandsSep: ', '
                    }
                });

                $('#chart_container').highcharts( {
                    exporting: {
                        chartOptions: { // specific options for the exported image
                            plotOptions: {
                                series: {
                                    dataLabels: {
                                        enabled: true
                                    }
                                }
                            }
                        },
                        fallbackToExportServer: false
                    },
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: data.project_name
                    },
                    subtitle: {
                        text: 'Project Summary Report'
                    },
                    xAxis: {
                        type: 'category'
                    },
                    yAxis: {
                        title: {
                            text: 'Amount'
                        }

                    },
                    legend: {
                        enabled: false
                    },
                    plotOptions: {
                        series: {
                            borderWidth: 0,
                            dataLabels: {
                                enabled: true,
                                format: '{point.y:,.1f}'
                            }
                        }
                    },

                    tooltip: {
                        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:,.2f}</b><br/>'
                    },

                    series: [{
                        name: 'General Summary',
                        colorByPoint: true,
                        data: [{
                            name: 'Budgeted',
                            y: parseFloat(data.goods_budget),
                            drilldown: 'Budgeted'
                        }, {
                            name: 'Approved Requests',
                            y: parseFloat(data.total_approved_amount),
                            drilldown: 'Goods Requested'
                        }, {
                            name: 'Goods Ordered',
                            y: parseFloat(data.order_amount),
                            drilldown: 'Goods Ordered'
                        }, {
                            name: 'Goods Received',
                            y: parseFloat(data.ordered_received_value),
                            drilldown: 'Goods Received'
                        }, {
                            name: 'Site Goods Received',
                            y: parseFloat(data.site_goods_received_value),
                            drilldown: 'Site Goods Received'
                        }, {
                            name: 'Material Used',
                            y: parseFloat(data.material_used_value),
                            drilldown: 'Material Used'
                        }, {
                            name: 'Site Material Balance',
                            y: parseFloat(data.site_material_balance_value),
                            drilldown: null
                        }]
                    }],
                    drilldown: {
                        series: [{
                            name: 'Budgeted',
                            id: 'Budgeted',
                            data: data.budget_activities
                        }, {
                            name: 'Goods Requested',
                            id: 'Goods Requested',
                            data: data.requisitions
                        }, {
                            name: 'Goods Ordered',
                            id: 'Goods Ordered',
                            data: data.ordered_goods
                        }, {
                            name: 'Goods Received',
                            id: 'Goods Received',
                            data: data.received_materials
                        }, {
                            name: 'Site Goods Received',
                            id: 'Site Goods Received',
                            data: data.site_grns
                        } ,{
                            name: 'Material Used',
                            id: 'Material Used',
                            data: data.cost_activities
                        },{
                            name: 'Site Material Balance',
                            id: 'Site Material Balance',
                            data: [
                                [
                                    'v12.x',
                                    0.34
                                ],
                                [
                                    'v28',
                                    0.24
                                ],
                                [
                                    'v27',
                                    0.17
                                ],
                                [
                                    'v29',
                                    0.16
                                ]
                            ]
                        }]
                    }
                });
            },'json'
        );
    } else {
        toast('error','Please Make sure all parameters are filled correctly');
    }
});

$('#generate_project_material_balance_report').click(function () {
    var box = $(this).closest('.box');
    var form = $(this).closest('form');
    var as_of = form.find('input[name="as_of"]').val();
    var project_id = form.find('select[name="project_id"]').val().trim();

    if(project_id != ''){
        start_spinner();
        $.post(
            base_url + "reports/project_material_status",
            {
                project_id: project_id,
                to:as_of
            }, function (data) {
                box.find('#report_container').html(data);
                stop_spinner();
            }
        );
    } else {
        toast('error','Please Make sure all parameters are filled correctly');
    }
});


