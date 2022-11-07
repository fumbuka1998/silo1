<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 23/10/2018
 * Time: 11:09
 */
$this->load->view('includes/header'); ?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Support
            <small>Tickets</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url() ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="">Support</a></li>
            <li class="active">Tickets</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="col-xs-12">
                            <div class="box-tools pull-right">
                                <button data-toggle="modal" data-target="#new_ticket" class="btn btn-default btn-xs">
                                    <i class="fa fa-plus"></i> New
                                </button>
                                <div id="new_ticket" class="modal fade" role="dialog">
                                    <?php
                                    $data = array('email_options'=>$email_options);
                                    $this->load->view('support/ticket_form', $data); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12 table-responsive">
                                <table id="support_tickets" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Ticket ID</th><th>Subject</th><th>Last Updated</th><th>Date Opened</th><th>Opened By</th><th>Status</th><td>Rate</td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


		<style>
			.star-rating {
				font-size: 0;
				white-space: nowrap;
				display: inline-block;
				/* width: 250px; remove this */
				height: 50px;
				overflow: hidden;
				position: relative;
				background: url('data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB3aWR0aD0iMjBweCIgaGVpZ2h0PSIyMHB4IiB2aWV3Qm94PSIwIDAgMjAgMjAiIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDIwIDIwIiB4bWw6c3BhY2U9InByZXNlcnZlIj48cG9seWdvbiBmaWxsPSIjREREREREIiBwb2ludHM9IjEwLDAgMTMuMDksNi41ODMgMjAsNy42MzkgMTUsMTIuNzY0IDE2LjE4LDIwIDEwLDE2LjU4MyAzLjgyLDIwIDUsMTIuNzY0IDAsNy42MzkgNi45MSw2LjU4MyAiLz48L3N2Zz4=');
				background-size: contain;
			}
			.star-rating i {
				opacity: 0;
				position: absolute;
				left: 0;
				top: 0;
				height: 100%;
				/* width: 20%; remove this */
				z-index: 1;
				background: url('data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB3aWR0aD0iMjBweCIgaGVpZ2h0PSIyMHB4IiB2aWV3Qm94PSIwIDAgMjAgMjAiIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDIwIDIwIiB4bWw6c3BhY2U9InByZXNlcnZlIj48cG9seWdvbiBmaWxsPSIjRkZERjg4IiBwb2ludHM9IjEwLDAgMTMuMDksNi41ODMgMjAsNy42MzkgMTUsMTIuNzY0IDE2LjE4LDIwIDEwLDE2LjU4MyAzLjgyLDIwIDUsMTIuNzY0IDAsNy42MzkgNi45MSw2LjU4MyAiLz48L3N2Zz4=');
				background-size: contain;
			}
			.star-rating input {
				-moz-appearance: none;
				-webkit-appearance: none;
				opacity: 0;
				display: inline-block;
				/* width: 20%; remove this */
				height: 100%;
				margin: 0;
				padding: 0;
				z-index: 2;
				position: relative;
			}
			.star-rating input:hover + i,
			.star-rating input:checked + i {
				opacity: 1;
			}
			.star-rating i ~ i {
				width: 40%;
			}
			.star-rating i ~ i ~ i {
				width: 60%;
			}
			.star-rating i ~ i ~ i ~ i {
				width: 80%;
			}
			.star-rating i ~ i ~ i ~ i ~ i {
				width: 100%;
			}
			::after,
			::before {
				height: 100%;
				padding: 0;
				margin: 0;
				box-sizing: border-box;
				text-align: center;
				vertical-align: middle;
			}

			.star-rating.star-5 {width: 250px;}
			.star-rating.star-5 input,
			.star-rating.star-5 i {width: 20%;}
			.star-rating.star-5 i ~ i {width: 40%;}
			.star-rating.star-5 i ~ i ~ i {width: 60%;}
			.star-rating.star-5 i ~ i ~ i ~ i {width: 80%;}
			.star-rating.star-5 i ~ i ~ i ~ i ~ i {width: 100%;}
			.star-rating.star-5 i ~ i ~ i ~ i ~ i ~ i {width: 100%;}

			.star-rating.star-3 {width: 150px;}
			.star-rating.star-3 input,
			.star-rating.star-3 i {width: 33.33%;}
			.star-rating.star-3 i ~ i {width: 66.66%;}
			.star-rating.star-3 i ~ i ~ i {width: 100%;}

			table.dataTable thead .sorting,
			table.dataTable thead .sorting_asc,
			table.dataTable thead .sorting_desc {
				background : none;
			}
		</style>
    </section><!-- /.content -->

<?php $this->load->view('includes/footer'); ?>


