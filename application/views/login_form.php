<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>EPM | Log in</title>
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
<body class="hold-transition login-page">
<div class="login-box">
    <div style="background-color: #2b669a" class="login-logo">
        <img height="100px" src="<?php echo base_url("images/logo.png");?>" />
    </div><!-- /.login-logo -->
    <div class="login-box-body">
        <?php
        if(isset($feedback) && $feedback == "ERROR_LOGIN"){
            ?>
            <div class="alert alert-danger">
                Username/Password Mismatch
            </div>
            <?php
        }
        ?>
        <p class="login-box-msg">Sign in to start your session</p>
        <?php  echo form_open(base_url("app/login"), ' role="form"'); ?>
            <div class="form-group has-feedback">
                <input type="text" name="username" class="form-control" placeholder="Username">
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" name="password" class="form-control" placeholder="Password">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In <i class='fa fa-arrow-circle-right'></i></button>
                </div><!-- /.col -->
            </div>
        </form>
        <hr/>
        <p  class="login-box-msg">Creating visibility to the whole team</p>
    </div><!-- /.login-box-body -->
</div><!-- /.login-box -->

<!-- jQuery 2.1.4 -->
<script src="<?= base_url('plugins/jQuery/jQuery-2.1.4.min.js')?>"></script>
<!-- Bootstrap 3.3.5 -->
<script src="<?= base_url('bootstrap/js/bootstrap.min.js')?>"></script>
<!-- iCheck -->
<script src="<?= base_url('plugins/iCheck/icheck.min.js')?>"></script>
</body>
</html>
