<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 11/05/2018
 * Time: 17:20
 */
?>
<html>
    <head>
        <link rel="stylesheet" href="<?= base_url('bootstrap/css/bootstrap.min.css')?>">
        <link rel="stylesheet" href="<?= base_url('css/AdminLTE.min.css')?>">
    </head>
    <body style="width: 640px; margin: auto">
        <div class="wrapper">
            <header style="background-color: #2b669a; text-align: center;">
                <!-- logo for regular state and mobile devices -->
                <img height="50px" src="<?php echo base_url("images/logo.png");?>" />
            </header>
            <div style="padding: 5px">
            <?= isset($content) ? $content : ''  ?>
                <br/><br/>
            </div>
            <footer class="main-footer" style="background-color: #2b669a; font-size: 12px; color: white; text-align: center; margin: 0; ">
                <div class="pull-right hidden-xs">
                    <b>Creating visibility to the whole team</b>
                </div>
                <strong>Copyright &copy; <?php echo strftime("%Y") ?>, Powered By <a target="_blank" style="color: white !important; text-decoration: none " href="http://bizytech.com">Bizy Tech Limited.</a></strong> All rights reserved.
            </footer>
        </div>
    </body>
</html>
