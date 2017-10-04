<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-2x">&times;</i>
            </button>
            <!--<button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;"
                    onclick="window.print();">
                <i class="fa fa-print"></i> <?/*= lang('print'); */?>
            </button>-->
            <h4 class="modal-title"
                id="myModalLabel"><?= $customer->company && $customer->company != '-' ? $customer->company : $customer->name; ?></h4>
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
                            'sAjaxSource': '<?= admin_url('customers/get_notes/' . $customer->id.'/'.$page) ?>',
                            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                                //nRow.id = aData[0];
                                nRow.className = 'customer_notes_link';
                                return nRow;
                            },
                            'fnDrawCallback':function() {
                                markNotesRead();
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
                            "aoColumns": [{'bVisible':false}, null, null, null, {bSortable:false}]
                        }).fnSetFilteringDelay().dtFilter([
                            {
                                column_number: 1,
                                filter_default_label: "[<?=lang('Created');?> (yyyy-mm-dd)]",
                                filter_type: "text",
                                data: []
                            },
                            {
                                column_number: 2,
                                filter_default_label: "[<?=lang('Title');?>]",
                                filter_type: "text",
                                data: []
                            },
                            {
                                column_number: 3,
                                filter_default_label: "[<?=lang('Note');?>]",
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
                                    <th class="col-xs-1"></th>
                                    <th class="col-xs-2"><?= lang("created"); ?></th>
                                    <th class="col-xs-2"><?= lang("title"); ?></th>
                                    <th class="col-xs-6"><?= lang("note"); ?></th>
                                    <th style="width:85px;">
                                        <a class="tip" style="color:white;" title='<?= lang("add_note") ?>'
                                           href="<?= admin_url('customers/edit_note/' . $customer->id."/".$page) ?>"
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
                                    <th class="col-xs-1"></th>
                                    <th class="col-xs-2"></th>
                                    <th class="col-xs-2"></th>
                                    <th class="col-xs-6"></th>
                                    <th style="width:85px;"></th>
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

<script type="text/javascript">
    function markNotesRead() {
        var noteIds = $('.edit-note').map(function() {
            return $(this).data("id");
        }).get();

        $.ajax({
            type: 'get',
            url: '<?= admin_url('customers/markNotesAsRead'); ?>',
            dataType: "json",
            data: {
                "note_ids[]": noteIds
            },
            success: function (response) {
                getUnreadCompanyNoteCount();
            }
        });
    }
</script>