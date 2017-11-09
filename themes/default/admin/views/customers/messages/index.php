<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
        var tblThreads = $('#tblThreads').dataTable({
            "bSort": false,
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true,
            'sAjaxSource': '<?= admin_url('messages/getThreads') ?>',
            'fnServerData': function (sSource, aoData, fnCallback, oSettings) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });

                oSettings.jqXHR = $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                console.log(aData);
                console.log(nRow);
                if(aData['status'] == 0) {
                    $(nRow).addClass('bold');
                }
                return nRow;
            },
            'fnDrawCallback': function () {

            },
            "aoColumns": [{
                "bSortable": false,
                "mRender": checkbox,
                "mDataProp": "thread_id"
            },
                {"mDataProp": "user_name"},
                {"mDataProp": "subject"},
                {"mDataProp": "actions", "bSortable": false, }
            ]
        });

        $('#myModal').on('hidden.bs.modal', function () {
            tblThreads.fnDraw(false);
        });

    });
</script>
<?php if ($Owner || $GP['bulk_actions']) {
    echo admin_form_open('customers/customer_actions', 'id="action-form"');
} ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-envelope"></i>Messages</h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"></p>

                <div class="table-responsive">
                    <table id="tblThreads" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-condensed table-hover">
                        <thead>
                        <tr class="primary">
                            <th class="col-md-1">
                                <input class="checkbox checkth" type="checkbox" name="check"/>
                            </th>
                            <th class="col-md-2">From</th>
                            <th class="col-md-7">Subject</th>
                            <th class="col-md-2" style="min-width:135px !important;">
                                <!--<a class="tip" style="color:white;" title='<?/*= lang("add_note") */?>'
                                   href="<?/*= admin_url('messages/add/') */?>"
                                   data-toggle='modal' data-target='#myModal2'>
                                    <i class="fa fa-plus"></i>
                                </a>-->
                                Actions
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="4" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th></th>
                            <th></th>
                            <th style="min-width:135px !important;" class="text-center"><?= lang("actions"); ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
</script>

<style>
    .bold td{
        font-weight:bold;
    }
</style>


