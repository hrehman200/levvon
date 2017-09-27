<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
    .table th {
        text-align: center;
    }

    .table td {
        padding: 2px;
    }

    .table td .table td:nth-child(odd) {
        text-align: left;
    }

    .table td .table td:nth-child(even) {
        text-align: right;
    }

    .table a:hover {
        text-decoration: none;
    }

    .cl_wday {
        text-align: center;
        font-weight: bold;
    }

    .cl_equal {
        width: 14%;
    }

    td.day {
        width: 14%;
        padding: 0 !important;
        vertical-align: top !important;
    }

    .day_num {
        width: 100%;
        text-align: left;
        cursor: pointer;
        margin: 0;
        padding: 8px;
    }

    .day_num:hover {
        background: #F5F5F5;
    }

    .content {
        width: 100%;
        text-align: left;
        color: #428bca;
        padding: 8px;
    }

    .highlight {
        color: #0088CC;
        font-weight: bold;
    }
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-calendar"></i>Daily Schedule</h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"></p>

                <div>
                    <?php echo $calender; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.table .day_num').click(function () {
            var day = $(this).html();
            var date = '<?= $year.'-'.$month.'-'; ?>'+day;
            var href = '<?= admin_url('daily_schedule/modal/')?>'+date;
            $.get(href, function( data ) {
                $("#myModal").html(data).modal();
            });

        });
    });
</script>
