</div><!-- /.content-wrapper -->


</div><!-- ./wrapper -->
<footer class="main-footer no-print">
    <div class="pull-right hidden-xs">
        <b>Creating visibility to the whole team</b>
    </div>
    <strong>Copyright &copy; <?php echo strftime("%Y") ?> <a target="_blank" style="color: white !important; " href="http://bizytech.com">Bizy Tech Limited.</a></strong> All rights reserved.
</footer>
<!-- jQuery 2.1.4 -->
<script src="<?= base_url('plugins/jQuery/jQuery-2.1.4.min.js')?>"></script>
<!-- Bootstrap 3.3.5 -->
<script src="<?= base_url('bootstrap/js/bootstrap.min.js')?>"></script>


<script src="<?= base_url('js/price_format.js')?>"></script>
<script src="<?= base_url('js/spin.min.js')?>"></script>

<script>
    base_url = "<?= base_url()?>";
</script>

<!-- Gantt Chart -->
<?php

if(isset($include_gantt_chart)){
    ?>

    <script src="<?= base_url('js/jquery-ui.min.js')?>"></script>
    <script src="<?= base_url('plugins/ganttChart/libs/jquery/jquery.livequery.1.1.1.min.js') ?>"></script>
    <script src="<?= base_url('plugins/ganttChart/libs/jquery/jquery.timers.js') ?>"></script>

    <script src="<?= base_url('plugins/ganttChart/libs/utilities.js') ?>"></script>
    <script src="<?= base_url('plugins/ganttChart/libs/forms.js') ?>"></script>
    <script src="<?= base_url('plugins/ganttChart/libs/date.js') ?>"></script>
    <script src="<?= base_url('plugins/ganttChart/libs/dialogs.js') ?>"></script>
    <script src="<?= base_url('plugins/ganttChart/libs/layout.js') ?>"></script>
    <script src="<?= base_url('plugins/ganttChart/libs/i18nJs.js') ?>"></script>
    <script src="<?= base_url('plugins/ganttChart/libs/jquery/dateField/jquery.dateField.js') ?>"></script>
    <script src="<?= base_url('plugins/ganttChart/libs/jquery/JST/jquery.JST.js') ?>"></script>

    <script type="text/javascript" src="<?= base_url('plugins/ganttChart/libs/jquery/svg/jquery.svg.min.js') ?>"></script>
    <script type="text/javascript" src="<?= base_url('plugins/ganttChart/libs/jquery/svg/jquery.svgdom.1.8.js') ?>"></script>


    <script src="<?= base_url('plugins/ganttChart/ganttUtilities.js') ?>"></script>
    <script src="<?= base_url('plugins/ganttChart/ganttTask.js') ?>"></script>
    <script src="<?= base_url('plugins/ganttChart/ganttDrawerSVG.js') ?>"></script>
    <script src="<?= base_url('plugins/ganttChart/ganttGridEditor.js') ?>"></script>
    <script src="<?= base_url('plugins/ganttChart/ganttMaster.js') ?>"></script>
    <?php
    $this->load->view('projects/gantt_chart_initializer');
}
?>
<!--Datepicker-->
<script src="<?= base_url('plugins/datepicker/bootstrap-datepicker.js')?>"></script>
<script src="<?= base_url('plugins/dataTables/jquery.dataTables.min.js')?>"></script>
<script src="<?= base_url('plugins/dataTables/dataTables.bootstrap.min.js')?>"></script>

<script src="<?= base_url('node_modules/datatables.net-buttons/js/dataTables.buttons.min.js') ?>"></script>
<script src="<?= base_url('node_modules/datatables.net-buttons-bs/js/buttons.bootstrap.min.js') ?>"></script>
<script src="<?= base_url('node_modules/datatables.net-buttons/js/buttons.flash.min.js') ?>"></script>
<script src="<?= base_url('node_modules/datatables.net-buttons/js/buttons.html5.min.js') ?>"></script>
<script src="<?= base_url('node_modules/datatables.net-buttons/js/buttons.print.min.js') ?>"></script>
<script src="<?= base_url('node_modules/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js') ?>"></script>
<script src="<?= base_url('node_modules/datatables.net-keytable/js/dataTables.keyTable.min.js') ?>"></script>
<script src="<?= base_url('node_modules/datatables.net-responsive/js/dataTables.responsive.min.js') ?>"></script>
<script src="<?= base_url('node_modules/datatables.net-responsive-bs/js/responsive.bootstrap.js') ?>"></script>
<script src="<?= base_url('node_modules/datatables.net-scroller/js/dataTables.scroller.min.js') ?>"></script>
<script src="<?= base_url('vendor/jszip/dist/jszip.min.js') ?>"></script>
<script src="<?= base_url('vendor/pdfmake/build/pdfmake.min.js') ?>"></script>
<script src="<?= base_url('vendor/pdfmake/build/vfs_fonts.js') ?>"></script>

<script src="<?= base_url('plugins/datetimepicker/datetimepicker.js')?>"></script>
<script src="<?= base_url('plugins/iziToast/js/iziToast.min.js')?>"></script>
<script src="<?= base_url('plugins/jquery-confirm/jquery-confirm.min.js')?>"></script>
<script src="<?= base_url('plugins/slimscroll/jquery.slimscroll.min.js')?>"></script>
<script src="<?= base_url('plugins/select2/select2.full.min.js')?>"></script>js
<script src="<?= base_url('plugins/highCharts/js/highcharts.src.js')?>"></script>
<script src="<?= base_url('plugins/highCharts/js/modules/data.js')?>"></script>
<script src="<?= base_url('plugins/highCharts/js/modules/drilldown.js')?>"></script>
<script src="<?= base_url('plugins/highCharts/js/modules/exporting.js')?>"></script>
<script src="<?= base_url('plugins/highCharts/js/modules/offline-exporting.js')?>"></script>

<!-- jmSpinner -->
<script src="<?= base_url('plugins/jmSpinner/jm.spinner.js')?>"></script>

<!-- sweetalert -->
<script src="<?= base_url('plugins/sweetalert/sweetalert.min.js')?>"></script>

<!-- AdminLTE App -->
<script src="<?= base_url('js/app.min.js')?>"></script>
<script src="<?= base_url('js/image_viewer.js')?>"></script>
<script src="<?= base_url('js/epm.js')?>"></script>

<!--<script src="-->
<?//= base_url('js/asset_register.js')?>

<!--"></script>-->
<script src="<?= base_url('js/human_resource.js')?>"></script>

</body>
</html>
