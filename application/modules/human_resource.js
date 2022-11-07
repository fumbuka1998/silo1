
/***************************
 *         BANKS
 ***************************/

$('#banks_list_table').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "human_resource/Settings/banks_list",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching Bank found</div>",
        "emptyTable":     "<div class='alert alert-info'>No bank found</div>"
    },

    "drawCallback": function () {


        //Save Bank

        $('.save_bank_button').each(function () {

            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    var modal = button.closest('.modal');
                    var bank_id = modal.find("input[name='bank_id']").val();
                    var bank_name = modal.find("input[name='bank_name']").val();
                    var description = modal.find("textarea[name='description']").val();

                    console.log(bank_id,bank_name,description);
                    if(bank_name != ''){
                        start_spinner();
                        modal.modal('hide');
                        $.post(
                            base_url + "human_resource/Settings/save_bank",
                            {
                                bank_id: bank_id,
                                bank_name: bank_name,
                                description: description

                            },function () {
                                stop_spinner();
                                modal.find('form')[0].reset();
                                $('#banks_list_table').DataTable().draw('page');
                                toast('success','Bank Added successful ');
                            }
                        );
                    } else {
                        toast('warning','Bank Name Must be filled ');
                    }
                });
                button.attr('initialized','true');
            }
        });


        //Delete Bank
        $('.delete_bank').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function () {
                    if(confirm('Are you sure?')){
                        start_spinner();
                        $.post(
                            base_url + "human_resource/Settings/delete_bank",
                            {
                                delete_bank_id: button.attr('delete_bank_id')
                            }, function () {
                                $('#banks_list_table').DataTable().draw('page');
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

/***************************
 *         BRANCHES
 ***************************/

$('#branches_list_table').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "human_resource/Settings/branches_list",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching Branch found</div>",
        "emptyTable":     "<div class='alert alert-info'>No branch found</div>"
    },

    "drawCallback": function () {


        //Save branch

        $('.save_branch_button').each(function () {

            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    var modal = button.closest('.modal');
                    var branch_id = modal.find("input[name='branch_id']").val();
                    var branch_name = modal.find("input[name='branch_name']").val();

                    console.log(branch_id,branch_name);
                    if(branch_name != ''){
                        start_spinner();
                        modal.modal('hide');
                        $.post(
                            base_url + "human_resource/Settings/save_branch",
                            {
                                branch_id: branch_id,
                                branch_name: branch_name,

                            },function () {
                                stop_spinner();
                                modal.find('form')[0].reset();
                                $('#branches_list_table').DataTable().draw('page');
                                toast('success','Branch Added successful ');
                            }
                        );
                    } else {
                        toast('warning','Branch Name Must be filled ');
                    }
                });
                button.attr('initialized','true');
            }
        });


        //Delete Branch
        $('.delete_branch').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function () {
                    if(confirm('Are you sure?')){
                        start_spinner();
                        $.post(
                            base_url + "human_resource/Settings/delete_branch",
                            {
                                delete_branch_id: button.attr('delete_branch_id')
                            }, function () {
                                $('#branches_list_table').DataTable().draw('page');
                            }
                        ).complete(function(){
                            toast('success','Branch Deleted ');
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

/***************************
 *         SSFS
 ***************************/

$('#ssfs_list_table').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "human_resource/Settings/ssfs_list",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching SSF found</div>",
        "emptyTable":     "<div class='alert alert-info'>No SSF found</div>"
    },

    "drawCallback": function () {


        //Save SSF

        $('.save_ssf_button').each(function () {

            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    var modal = button.closest('.modal');
                    var ssf_id = modal.find("input[name='ssf_id']").val();
                    var official_ssf_id = modal.find("select[name='official_ssf_id']").val();

                    console.log(ssf_id,official_ssf_id);
                    if(official_ssf_id != ''){
                        start_spinner();
                        modal.modal('hide');
                        $.post(
                            base_url + "human_resource/Settings/save_ssf",
                            {
                                ssf_id: ssf_id,
                                official_ssf_id: official_ssf_id,

                            },function () {
                                stop_spinner();
                                modal.find('form')[0].reset();
                                $('#ssfs_list_table').DataTable().draw('page');
                                toast('success','SSF Added successful ');
                            }
                        );
                    } else {
                        toast('warning','official_ssf_id Must be filled ');
                    }
                });
                button.attr('initialized','true');
            }
        });


        //Delete SSF
        $('.delete_ssf').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function () {
                    if(confirm('Are you sure?')){
                        start_spinner();
                        $.post(
                            base_url + "human_resource/Settings/delete_ssf",
                            {
                                delete_ssf_id: button.attr('delete_ssf_id')
                            }, function () {
                                $('#ssfs_list_table').DataTable().draw('page');
                            }
                        ).complete(function(){
                            toast('success','SSF Deleted ');
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

/***************************
 *         HIFS
 ***************************/

$('#hifs_list_table').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "human_resource/Settings/hifs_list",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching HIF found</div>",
        "emptyTable":     "<div class='alert alert-info'>No HIF found</div>"
    },

    "drawCallback": function () {


        //Save SSF

        $('.save_hif_button').each(function () {

            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    var modal = button.closest('.modal');
                    var hif_id = modal.find("input[name='hif_id']").val();
                    var official_hif_id = modal.find("select[name='official_hif_id']").val();

                    console.log(hif_id,official_hif_id);
                    if(official_hif_id != ''){
                        start_spinner();
                        modal.modal('hide');
                        $.post(
                            base_url + "human_resource/Settings/save_hif",
                            {
                                hif_id: hif_id,
                                official_hif_id: official_hif_id,

                            },function () {
                                stop_spinner();
                                modal.find('form')[0].reset();
                                $('#hifs_list_table').DataTable().draw('page');
                                toast('success','HIF Added successful ');
                            }
                        );
                    } else {
                        toast('warning','official_hif_id Must be filled ');
                    }
                });
                button.attr('initialized','true');
            }
        });


        //Delete HIF
        $('.delete_hif').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function () {
                    if(confirm('Are you sure?')){
                        start_spinner();
                        $.post(
                            base_url + "human_resource/Settings/delete_hif",
                            {
                                delete_hif_id: button.attr('delete_hif_id')
                            }, function () {
                                $('#hifs_list_table').DataTable().draw('page');
                            }
                        ).complete(function(){
                            toast('success','HIF Deleted ');
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


//Save TAX RATE

$('.save_tax_rate').each(function () {

    var button = $(this);
    if(button.attr('initialized') != 'true'){
        button.click(function () {
            var modal = button.closest('.modal');
            var start_date = modal.find("input[name='start_date']").val();
            var end_date = modal.find("input[name='end_date']").val();
            var tax_table_id = modal.find("input[name='tax_table_id']").val();
            var minimums = new Array();
            var maximums = new Array();
            var rates = new Array();
            var additional_amounts = new Array();
                 i = 0;
            var tbody = modal.find('tbody');

            tbody.find('input[name="minimum"]').each(function(){
                var minimum = $(this).val();
                var row = $(this).closest('tr');
                    minimums[i] = row.find('input[name="minimum"]').unmask();
                    maximums[i] = row.find('input[name="maximum"]').unmask();
                    rates[i] = row.find('input[name="rate"]').val();
                    additional_amounts[i] = row.find('input[name="additional_amount"]').unmask();
                    i++;

            });

            console.log(start_date,minimums,maximums,rates,additional_amounts);
            if(start_date != ''){
                start_spinner();
                modal.modal('hide');
                $.post(
                    base_url + "human_resource/Settings/save_tax_rates",
                    {
                        tax_table_id: tax_table_id,
                        start_date: start_date,
                        end_date: end_date,
                        rates: rates,
                        minimums:minimums,
                        maximums:maximums,
                        additional_amounts:additional_amounts,

                    },function () {
                        stop_spinner();
                        modal.find('form')[0].reset();
                       // $('#hifs_list_table').DataTable().draw('page');
                        toast('success','Tax Rate Added successful ');
                    }
                );
            } else {
                toast('warning','start_date Must be filled ');
            }
        });
        button.attr('initialized','true');
    }
});


    $('.row_adder').each(function () {
        var button=$(this);

        if (button.attr('initialized') != 'true') {
            button.click(function () {
                var table = button.closest('table');
                var tbody=table.find('tbody');
                var new_row = tbody.closest('table').find('.row_template').clone().removeAttr('style')
                    .removeClass('row_template').addClass('artificial_row').appendTo(tbody);
                new_row.find('.row_remover').click(function(){
                    $(this).closest('tr').remove();
                });
                initialize_common_js();
                new_row.find('.number_format').priceFormat({centsLimit:2});
            });
        }

        $(this).attr('initialized','true');

    });

/******************************
 * HUMAN RESOURCES
 ******************************/

$('#contract_employee_list').DataTable({
        colReorder: true,
        "processing": true,
        "serverSide": true,
        "ajax" : {
            url: base_url + "human_resource/Human_resources/contract_employee_list/",
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

$('#non_contract_employee_list').DataTable({
        colReorder: true,
        "processing": true,
        "serverSide": true,
        "ajax" : {
            url: base_url + "human_resource/Human_resources/non_contract_employee_list/",
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

$('#incomplete_contract_employee_list').DataTable({
        colReorder: true,
        "processing": true,
        "serverSide": true,
        "ajax" : {
            url: base_url + "human_resource/Human_resources/incomplete_contract_employee_list/",
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

$('#expired_contract_employee_list').DataTable({
        colReorder: true,
        "processing": true,
        "serverSide": true,
        "ajax" : {
            url: base_url + "human_resource/Human_resources/expired_contract_employee_list/",
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

$('#closed_contract_employee_list').DataTable({
        colReorder: true,
        "processing": true,
        "serverSide": true,
        "ajax" : {
            url: base_url + "human_resource/Human_resources/closed_contract_employee_list/",
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

$('#departments_list').DataTable({
    colReorder: true,
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "human_resources/departments/",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching departments found</div>",
        "emptyTable":     "<div class='alert alert-info'>No departments found</div>"
    },"drawCallback": function () {

        //Save department
        var save_department = function (button){
            var modal = button.closest('.modal');
            modal.modal('hide');
            var department_name = modal.find('input[name="department_name"]').val();
            var department_id = modal.find('input[name="department_id"]').val();
            var description = modal.find('textarea[name="description"]').val();

            $.post(
                base_url + "human_resources/save_department/",
                {
                    department_name: department_name,
                    department_id: department_id,
                    description:description
                }
            ).complete(function(){
                modal.find('form')[0].reset();
                $('#departments_list').DataTable().draw('page');
            });
        };

        $('.save_department_button').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function(){
                    save_department(button);
                });
                button.attr('active','true');
            }
        });

        //Delete Department
        $('.delete_department').each(function () {
            var button = $(this);
            if(button.attr('initialized') != 'true'){
                var department_id = button.attr('department_id');
                button.click(function () {
                    if(confirm('Are you sure?')) {
                        $.post(
                            base_url + "human_resources/delete_department",
                            {
                                department_id: department_id
                            }
                        ).complete(function () {
                            $('#departments_list').DataTable().draw('page');
                        });
                    }
                });
                button.attr('initialized','true');
            }
        });

        initialize_common_js();
    }
});

$('#job_positions_list').DataTable({
    colReorder: true,
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "human_resources/job_positions/",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching positions found</div>",
        "emptyTable":     "<div class='alert alert-info'>No positions found</div>"
    },"drawCallback": function () {

        //Save Job Position
        var save_job_position = function (button){
            var modal = button.closest('.modal');
            modal.modal('hide');
            var position_name = modal.find('input[name="position_name"]').val();
            var position_id = modal.find('input[name="position_id"]').val();
            var description = modal.find('textarea[name="description"]').val();

            $.post(
                base_url + "human_resources/save_job_position/",
                {
                    position_name: position_name,
                    position_id: position_id,
                    description:description
                }
            ).complete(function(){
                modal.find('form')[0].reset();
                $('#job_positions_list').DataTable().draw('page');
            });
        };

        $('.save_job_position_button').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function(){
                    save_job_position(button);
                });
                button.attr('active','true');
            }
        });

        //Delete Job Position
        $('.delete_job_position').each(function () {
            var button = $(this);
            if(button.attr('initialized') != 'true'){
                var position_id = button.attr('position_id');
                button.click(function () {
                    if(confirm('Are you sure?')) {
                        $.post(
                            base_url + "human_resources/delete_job_position",
                            {
                                position_id: position_id
                            }
                        ).complete(function () {
                            $('#job_positions_list').DataTable().draw('page');
                        });
                    }
                });
                button.attr('initialized','true');
            }
        });

        initialize_common_js();
    }
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

/***************************
 *         EMPLOYEE_CONTRACT
 ***************************/
$('#employee_contracts').each(function(){
    var employee_id = $(this).attr('employee_id');
$(this).DataTable({

    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "human_resource/Contracts/employee_contract_list/"+employee_id,
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
        "zeroRecords":     "<div class='alert alert-info'>No matching Employee Contract found</div>",
        "emptyTable":     "<div class='alert alert-info'>No  Employee Contract found</div>"
    },
    "drawCallback": function () {


        //Save Employee Contract

       $('.save_contract_button').each(function () {

            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    var modal = button.closest('.modal');

                    /* employee_contract_id    payroll_no    salary    subsistence    responsibility   currency
                     payment_mode tax_details ssf_contribution start_date  end_date  created_at   created_by */

                    /* employee_contract_id  department_id  job_position_id  branch_id  start_date end_date created_at  created_by */

                    var employee_id = modal.find("input[name='employee_id']").val();
                    var employee_contract_id = modal.find("input[name='employee_contract_id']").val();
                    var employee_salary_id=  modal.find("input[name='employee_salary_id']").val();
                    var employee_designation_id= modal.find("input[name='employee_designation_id']").val();
                    var start_date = modal.find("input[name='start_date']").val();
                    var end_date = modal.find("input[name='end_date']").val();
                         //salary
                    var payroll_no = modal.find("input[name='payroll_no']").val();
                    var salary = modal.find("input[name='salary']").unmask();
                    var tax_details = modal.find("select[name='tax_details']").val();
                    var subsistence = modal.find("input[name='subsistence']").unmask();
                    var responsibility = modal.find("input[name='responsibility']").unmask();
                    var currency = modal.find("select[name='currency']").val();
                    var payment_mode = modal.find("select[name='payment_mode']").val();
                    var ssf_contribution = modal.find("select[name='ssf_contribution']").val();
                        //designation
                    var department_id = modal.find("select[name='department_id']").val();
                    var job_position_id = modal.find("select[name='job_position_id']").val();
                    var branch_id = modal.find("select[name='branch_id']").val();

                    console.log(employee_id,employee_contract_id,start_date,end_date,payroll_no,salary,tax_details,subsistence,responsibility,currency,payment_mode,
                        ssf_contribution,department_id,job_position_id,branch_id);
                    if(start_date != ''){
                        start_spinner();
                        modal.modal('hide');
                        $.post(
                            base_url + "human_resource/contracts/save_employee_contract",
                            {
                                employee_id:employee_id,
                                employee_contract_id:employee_contract_id,
                                employee_salary_id:employee_salary_id,
                                employee_designation_id:employee_designation_id,
                                start_date:start_date,
                                end_date:end_date,
                                payroll_no:payroll_no,
                                salary:salary,
                                tax_details:tax_details,
                                subsistence:subsistence,
                                responsibility:responsibility,
                                currency:currency,
                                payment_mode:payment_mode,
                                ssf_contribution:ssf_contribution,
                                department_id:department_id,
                                job_position_id:job_position_id,
                                branch_id:branch_id

                            },function () {
                                stop_spinner();
                                modal.find('form')[0].reset();
                                $('#employee_contracts').DataTable().draw('page');
                                toast('success','Contract Added successful ');
                            }
                        );
                    } else {
                        toast('warning','Start Date Must be filled ');
                    }
                });
                button.attr('initialized','true');
            }
        });

        $('.delete_employee_contract').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function () {
                    if(confirm('Are you sure?')){
                        start_spinner();
                        $.post(
                            base_url + "human_resource/Contracts/delete_employee_contract",
                            {
                                contract_id: button.attr('contract_selected_id')

                            }, function () {
                                $('#employee_contracts').DataTable().draw('page');
                            }
                        ).complete(function(){
                            toast('success','Contract Deleted ');
                            stop_spinner();
                        });
                    }
                });
                button.attr('active', 'true');
            }
        });

     $('.close_contract_button').each(function () {
            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    var modal = button.closest('.modal');
                    var employee_contract_id = modal.find("input[name='employee_contract_id']").val();
                    var close_date = modal.find("input[name='close_date']").val();
                    var reason = modal.find("textarea[name='reason']").val();
                    var attachment = modal.find("input[name='attachment']").val();

                    console.log(employee_contract_id,close_date,reason,attachment);
                    if(close_date != ''){
                        start_spinner();
                        modal.modal('hide');
                        $.post(
                            base_url + "human_resource/Contracts/close_employee_contract",
                            {
                                employee_contract_id:employee_contract_id,
                                close_date:close_date,
                                reason:reason,
                                attachement:attachment,
                            },function () {
                                stop_spinner();
                                modal.find('form')[0].reset();
                                $('#employee_contracts').DataTable().draw('page');
                                toast('success','Contract Closed successful ');
                            }
                        );
                    } else {
                        toast('danger','close_date Date Must be filled ');
                    }
                });
                button.attr('initialized','true');
            }
        });

     $('.activate_employee_contract').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function () {
                    if(confirm('Are you sure you want to activate?')){
                        start_spinner();
                        $.post(
                            base_url + "human_resource/Contracts/activate_employee_contract",
                            {
                                contract_id: button.attr('activate_contract_id'),

                            }, function () {
                                $('#employee_contracts').DataTable().draw('page');
                            }
                        ).complete(function(){
                            toast('success','Contract Activated ');
                            stop_spinner();
                        });
                    }
                });
                button.attr('active', 'true');
            }
        });

     

     $('.view_employee_contract').each(function(){
            var button = $(this);
            var tab_pane=button.closest('.tab-pane');
            if(button.attr('active') != 'true') {
                button.click(function () {
                        start_spinner();
                        $.post(
                            base_url + "human_resource/Contracts/employee_contract_details",
                            {
                                employee_contract_id: button.attr('employee_contract_id'),

                            }, function (data) {

                               //alert(data.content);
                                 tab_pane.html(data.content);
                                 initialize_common_js();

                            },'json'
                        ).complete(function(){
                            //salary list

                             employee_salary_list();
                             employee_designation_list();

                            //end salary list

                            stop_spinner();
                        });

                });
                button.attr('active', 'true');
            }
        });
/*
     $('.view_employee_contract').each(function(){
            var button = $(this);
            var tab_pane=button.closest('.tab-pane');
            if(button.attr('active') != 'true') {
                button.click(function () {
                        start_spinner();
                        $.post(
                            base_url + "human_resource/Contracts/employee_contract_details",
                            {
                                employee_contract_id: button.attr('employee_contract_id'),

                            }, function (data) {

                               //alert(data.content);
                                 tab_pane.html(data.content);
                                 initialize_common_js();

                            },'json'
                        ).complete(function(){
                            //designation list

                             employee_designation_list();

                            //end designation list

                            stop_spinner();
                        });

                });
                button.attr('active', 'true');
            }
        });
*/


        $(this).find('tr').each(function () {
            $(this).find('td:last-child').attr('nowrap', 'nowrap');
        });

        initialize_common_js();
    }

    });
 });

$('#employee_ssf').each(function(){
    var employee_id = $(this).attr('employee_id');
$(this).DataTable({

    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "human_resource/Contracts/employee_ssf_list/"+employee_id,
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
        "zeroRecords":     "<div class='alert alert-info'>No matching Employee SSf found</div>",
        "emptyTable":     "<div class='alert alert-info'>No  Employee SSF found</div>"
    },
    "drawCallback": function () {
        //Save Employee SSF

        $('.save_employee_ssf_button').each(function () {

            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    var modal = button.closest('.modal');
                    var employee_id = modal.find("input[name='employee_id']").val();
                    var employee_ssf_id = modal.find("input[name='employee_ssf_id']").val();
                    var ssf_id = modal.find("select[name='ssf_id']").val();
                    var ssf_no = modal.find("input[name='ssf_no']").val();
                    var start_date = modal.find("input[name='start_date']").val();

                    console.log(employee_id,employee_ssf_id,ssf_no,ssf_id,start_date);
                    if(ssf_id != ''){
                        start_spinner();
                        modal.modal('hide');
                        $.post(
                            base_url + "human_resource/Contracts/save_employee_ssf",
                            {
                                employee_id:employee_id,
                                employee_ssf_id:employee_ssf_id,
                                ssf_id:ssf_id,
                                ssf_no:ssf_no,
                                start_date:start_date
                            },function () {
                                stop_spinner();
                                modal.find('form')[0].reset();
                                $('#employee_ssf').DataTable().draw('page');
                                toast('success','SSF Added successful ');
                            }
                        );
                    } else {
                        toast('warning','SSF  Must be filled ');
                    }
                });
                button.attr('initialized','true');
            }
        });

        //Delete Employee SSF
        $('.delete_employee_ssf').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function () {
                    if(confirm('Are you sure?')){
                        start_spinner();
                        $.post(
                            base_url + "human_resource/Contracts/delete_employee_ssf",
                            {
                                employee_ssf_id: button.attr('employee_ssf_id')

                            }, function () {
                                $('#employee_ssf').DataTable().draw('page');
                            }
                        ).complete(function(){
                            toast('success','Employee SSF Deleted ');
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
 });


$('#employee_bank').each(function(){
    var employee_id = $(this).attr('employee_id');
    $(this).DataTable({

        "processing": true,
        "serverSide": true,
        "ajax" : {
            url: base_url + "human_resource/Contracts/employee_bank_list/"+employee_id,
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
            {"orderable": false}
        ],
        "language": {
            "zeroRecords":     "<div class='alert alert-info'>No matching Employee BANK found</div>",
            "emptyTable":     "<div class='alert alert-info'>No  Employee BANK found</div>"
        },

         "drawCallback": function () {

        //Save Employee BANK

             $('.save_employee_bank_button').each(function () {

                 var button = $(this);
                 if(button.attr('initialized') != 'true'){
                     button.click(function () {
                         var modal = button.closest('.modal');


                         var employee_id = modal.find("input[name='employee_id']").val();
                         var employee_bank_id = modal.find("input[name='employee_bank_id']").val();
                         var bank_id = modal.find("select[name='bank_id']").val();
                         var account_no = modal.find("input[name='account_no']").val();
                         var branch = modal.find("input[name='branch']").val();
                         var swift_code = modal.find("input[name='swift_code']").val();
                         var start_date = modal.find("input[name='start_date']").val();

                         console.log(employee_id,employee_bank_id,bank_id,account_no,branch,swift_code,start_date);
                         if(bank_id != ''){
                             start_spinner();
                             modal.modal('hide');
                             $.post(
                                 base_url + "human_resource/Contracts/save_employee_bank",
                                 {
                                     employee_id:employee_id,
                                     employee_bank_id:employee_bank_id,
                                     bank_id:bank_id,
                                     account_no:account_no,
                                     branch:branch,
                                     swift_code:swift_code,
                                     start_date:start_date
                                 },function () {
                                     stop_spinner();
                                     modal.find('form')[0].reset();
                                     $('#employee_bank').DataTable().draw('page');
                                     toast('success','BANK Added successful ');
                                 }
                             );
                         } else {
                             toast('warning','BANK  Must be filled ');
                         }
                     });
                     button.attr('initialized','true');
                 }
             });

             //Delete Employee BANK
             $('.delete_employee_bank').each(function(){
                 var button = $(this);
                 if(button.attr('active') != 'true') {
                     button.click(function () {
                         if(confirm('Are you sure?')){
                             start_spinner();
                             $.post(
                                 base_url + "human_resource/Contracts/delete_employee_bank",
                                 {
                                     employee_bank_id: button.attr('employee_bank_id')

                                 }, function () {
                                     $('#employee_bank').DataTable().draw('page');
                                 }
                             ).complete(function(){
                                 toast('success','Employee BANK Deleted ');
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
});


function employee_salary_list(){

           $('.employee_salary_list').each(function () {

            var employee_contract_id = $(this).attr('employee_contract_id');
            var table = $(this);
            var panel_body = table.closest('.panel-body');
            var load_salary_content = function () {
                start_spinner();
                $.post(
                    base_url + "human_resource/contracts/employee_contract_salary_list",
                    {
                        employee_contract_id: employee_contract_id

                    }, function (data) {

                        table.html(data.salary_table);
                       
                            //Save Employee Salary

                            $('.save_salary_button').each(function () {

                                var button = $(this);
                                if(button.attr('initialized') != 'true'){
                                    button.click(function () {
                                        var modal = button.closest('.modal');
                                        var employee_contract_id = modal.find("input[name='employee_contract_id']").val();
                                        var employee_salary_id=  modal.find("input[name='employee_salary_id']").val();
                                        var start_date = modal.find("input[name='start_date']").val();
                                        var end_date = modal.find("input[name='end_date']").val();
                                        var payroll_no = modal.find("input[name='payroll_no']").val();
                                        var salary = modal.find("input[name='salary']").unmask();
                                        var tax_details = modal.find("select[name='tax_details']").val();
                                        var subsistance = modal.find("input[name='subsistance']").unmask();
                                        var responsibility = modal.find("input[name='responsibility']").unmask();
                                        var currency_id = modal.find("select[name='currency_id']").val();
                                        var payment_mode = modal.find("select[name='payment_mode']").val();
                                        var ssf_contribution = modal.find("select[name='ssf_contribution']").val();

                                       
                                        if(start_date != ''){
                                            start_spinner();
                                            modal.modal('hide');
                                            $.post(
                                                base_url + "human_resource/contracts/save_employee_salary",
                                                {
                                                    
                                                    employee_contract_id:employee_contract_id,
                                                    employee_salary_id:employee_salary_id,
                                                    start_date:start_date,
                                                    end_date:end_date,
                                                    payroll_no:payroll_no,
                                                    salary:salary,
                                                    tax_details:tax_details,
                                                    subsistance:subsistance,
                                                    responsibility:responsibility,
                                                    currency_id:currency_id,
                                                    payment_mode:payment_mode,
                                                    ssf_contribution:ssf_contribution


                                                },function () {
                                                    stop_spinner();
                                                    modal.find('form')[0].reset();
                                                    load_salary_content();
                                                    toast('success','Salary Added successful ');
                                                }
                                            );
                                        } else {
                                            toast('warning','Start Date Must be filled ');
                                        }
                                    });
                                    button.attr('initialized','true');
                                }
                            });

                        //end save employee salary

                         $('.delete_employee_salary').each(function(){
                            var button = $(this);
                            if(button.attr('active') != 'true') {
                                button.click(function () {
                                    if(confirm('Are you sure?')){
                                        start_spinner();
                                        $.post(
                                            base_url + "human_resource/contracts/delete_employee_salary",
                                            {
                                                employee_salary_id: button.attr('employee_salary_id')

                                            }, function () {

                                                 load_salary_content();
                                            }
                                        ).complete(function(){
                                            toast('success','Salary Deleted  Successfully');
                                            stop_spinner();
                                        });
                                    }
                                });
                                button.attr('active', 'true');
                            }
                        });

                        stop_spinner();
                        initialize_common_js();
                    },
                    'json'
                ).complete();
            };
            
            load_salary_content();
            
         });

}


function employee_designation_list(){

           $('.employee_designation_list').each(function () {

            var employee_contract_id = $(this).attr('employee_contract_id');
            var table = $(this);
            var panel_body = table.closest('.panel-body');
            var panel_body = table.closest('.panel-body');
            var load_designation_content = function () {
                start_spinner();
                $.post(
                    base_url + "human_resource/contracts/employee_contract_designation_list",
                    {
                        employee_contract_id: employee_contract_id

                    }, function (data) {

                        table.html(data.designation_table);

                            //Save Employee Designation

                            $('.save_designation_button').each(function () {

                                var button = $(this);
                                if(button.attr('initialized') != 'true'){
                                    button.click(function () {
                                        var modal = button.closest('.modal');
                                        var employee_contract_id = modal.find("input[name='employee_contract_id']").val();
                                        var employee_designation_id=  modal.find("input[name='employee_designation_id']").val();
                                        var start_date = modal.find("input[name='start_date']").val();
                                        var end_date = modal.find("input[name='end_date']").val();
                                        var department_id = modal.find("select[name='department_id']").val();
                                        var job_position_id = modal.find("select[name='job_position_id']").val();
                                        var branch_id = modal.find("select[name='branch_id']").val();


                                        if(job_position_id != ''){
                                            start_spinner();
                                            modal.modal('hide');
                                            $.post(
                                                base_url + "human_resource/contracts/save_employee_designation",
                                                {

                                                    employee_contract_id:employee_contract_id,
                                                    employee_designation_id:employee_designation_id,
                                                    start_date:start_date,
                                                    end_date:end_date,
                                                    department_id:department_id,
                                                    job_position_id:job_position_id,
                                                    branch_id:branch_id

                                                },function () {
                                                    stop_spinner();
                                                    modal.find('form')[0].reset();
                                                    load_designation_content();
                                                    toast('success','Designation Added successful ');
                                                }
                                            );
                                        } else {
                                            toast('warning','Job Position Must be filled ');
                                        }
                                    });
                                    button.attr('initialized','true');
                                }
                            });

                        //end save employee designation

                         $('.delete_employee_designation').each(function(){
                            var button = $(this);
                            if(button.attr('active') != 'true') {
                                button.click(function () {
                                    if(confirm('Are you sure?')){
                                        start_spinner();
                                        $.post(
                                            base_url + "human_resource/contracts/delete_employee_designation",
                                            {
                                                employee_designation_id: button.attr('employee_designation_id')

                                            }, function () {

                                                 load_designation_content();
                                            }
                                        ).complete(function(){
                                            toast('success','Designation Deleted  Successfully');
                                            stop_spinner();
                                        });
                                    }
                                });
                                button.attr('active', 'true');
                            }
                        });

                        stop_spinner();
                        initialize_common_js();
                    },
                    'json'
                ).complete();
            };

            load_designation_content();

         });

}



