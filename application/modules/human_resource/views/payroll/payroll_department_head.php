<div class="container-fluid">
    <table>
        <thead>
        <tr>
            <td style="font-weight: bold; font-size: large">
                <?= strtoupper($departments->department_name) ?> DEPARTMENT, PAYROLL <?= strtoupper($type) ?> FOR
                <?= strtoupper(DateTime::createFromFormat('!m', date('m', strtotime($payroll_date)))->format('F')) . ' ' . date('Y', strtotime($payroll_date)) ?>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; font-size: large">
                Currency: Tanzanian Shilings (TSh)
            </td>
        </tr>
        </thead>
    </table>
</div>
<br/>