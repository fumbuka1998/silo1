<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>EPM</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="icon" type="image/png" href="<?=  base_url('favicon.png')?>" sizes="16x16">

    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('css/AdminLTE.min.css')?>">

    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="<?= base_url('bootstrap/css/bootstrap.min.css')?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('css/font-awesome.css')?>">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?= base_url('css/ionicons.css')?>">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?= base_url('plugins/iCheck/square/blue.css')?>">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<?php

    $company_details = get_company_details();
?>
<br>
<table width="100%">
    <tr>
     
    
        <td style="text-align: center">
            <img style="width: 130px" src="<?= base_url('images/company_logo.png')?>">
        </td>
        </tr>

     <tr>
        <td style="text-align: center; font-size: 13px; color: #ed1c24;">
            <h2><?= $company_details->company_name ?></h2><br/>
            
        </td>
     </tr>
</table>

    <div class="row">

    <div class="col-lg-2"></div>


       <div class="col-lg-8">


            <?php
                foreach ($project_categories as $project_category){
                    $icon = explode(' ',trim($project_category->category_name))[0];
            ?>


                <div class="col-lg-6 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-aqua-gradient">
                        <div class="inner">
                            <h3>&nbsp;</h3>

                            <p><?= $project_category->category_name ?></p>
                        </div>
                        <div class="icon">
                            <i><?= $icon ?></i>
                        </div>
                        <a href="<?= base_url('app/login') ?>" class="small-box-footer">
                            
                            <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

              <!-- ./col -->
            <?php } ?>
          </div>
        </div>
        
<div  style="

position: fixed;
right: 0;
height: 50px;
left: 0;
bottom: 0;
font-weight: bolder;
z-index: 1030;
background-color: #2b669a;
padding: 0px;
color: #ffffff;
overflow:auto;
color: white;">

<div style="padding-top: 5px; padding-bottom: 5px;text-align: left;float: left">
        <img height="40px" src="<?php echo base_url("images/logo.png");?>"/>
</div>

<div style="padding-top: 15px; padding-bottom: 5px;float: right;">
       
    <strong>Copyright &copy; <?php echo strftime("%Y") ?> <a target="_blank" style="color: white !important; " href="http://bizytech.com">Bizy Tech Limited.</a></strong> All rights reserved.
</div>

    </div><!-- /.login-logo -->
       
   

<!-- jQuery 2.1.4 -->
<script src="<?= base_url('plugins/jQuery/jQuery-2.1.4.min.js')?>"></script>
<!-- Bootstrap 3.3.5 -->
<script src="<?= base_url('bootstrap/js/bootstrap.min.js')?>"></script>
<!-- iCheck -->
<script src="<?= base_url('plugins/iCheck/icheck.min.js')?>"></script>
</body>
</html>
