<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title"
                id="myModalLabel"><?= $user->first_name.' '.$user->last_name; ?> schedule for <?=$date?></h4>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <script type="text/javascript">
                    $(document).ready(function () {
                        oTable = $('#notesData').dataTable({
                            "aaSorting": [[0, "desc"]],
                            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                            "iDisplayLength": <?= $Settings->rows_per_page ?>,
                            'bProcessing': true, 'bServerSide': true,
                            'sAjaxSource': '<?= admin_url('daily_schedule/getList/'.urlencode($date)) ?>',
                            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                                //nRow.className = 'customer_notes_link';
                                return nRow;
                            },
                            'fnServerData': function (sSource, aoData, fnCallback) {
                                aoData.push({
                                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                                    "value": "<?= $this->security->get_csrf_hash() ?>"
                                });
                                $.ajax({
                                    'dataType': 'json',
                                    'type': 'POST',
                                    'url': sSource,
                                    'data': aoData,
                                    'success': fnCallback
                                });
                            },
                            //"aoColumns": [{"mRender": fld}, {"mRender": currencyFormat}, null, null, {"mRender": decode_html}]
                        }).fnSetFilteringDelay().dtFilter([
                            {
                                column_number: 0,
                                filter_default_label: "[Time (HH:ii:ss)]",
                                filter_type: "text",
                                data: []
                            },
                            {
                                column_number: 1,
                                filter_default_label: "[Note]",
                                filter_type: "text",
                                data: []
                            },
                        ], "footer");
                    });
                </script>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">

                            <table id="notesData"
                                   class="table table-bordered table-condensed table-hover table-striped reports-table">
                                <thead>
                                <tr class="primary">
                                    <th class="col-xs-2">Time</th>
                                    <th class="col-xs-6"><?= lang("note"); ?></th>
                                    <th style="width:85px;">
                                        <a class="tip" style="color:white;" title='<?= lang("add_note") ?>'
                                           href="<?= admin_url('daily_schedule/edit/'.$date) ?>"
                                           data-toggle='modal' data-target='#myModal2'>
                                            <i class="fa fa-plus"></i>
                                        </a>
                                        <?= lang("actions"); ?>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="3"
                                        class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr class="dtFilter">
                                    <th class="col-xs-2"></th>
                                    <th class="col-xs-8"></th>
                                    <th class="col-xs-2"></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>