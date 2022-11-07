<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/22/2017
 * Time: 10:33 PM
 */
?>

<style>
    .bordered{
        border: 1px solid black;
    }

    .left_bordered{
        border: 1px solid black;
        border-right: none !important;
    }

    .right_bordered{
        border: 1px solid black;
        border-left: none !important;
    }

    hr.header_bottom_margin {
        display: block;
        height: 3px;
        color: <?= isset($company_details) ? $company_details->corporate_color : '#870C25' ?>;
        border: 0;
        border-top: 1px solid <?= isset($company_details) ? $company_details->corporate_color : '#870C25' ?>;
        margin: 1em 0;
        padding: 0;
    }
</style>
