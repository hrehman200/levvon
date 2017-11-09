<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
        var tblThread = $('#tblThread').dataTable({
            "bSort": false,
            "bInfo": false,
            "bPaginate": false,
            'bProcessing': true,
            'sAjaxSource': '<?= admin_url('messages/getThread/'.$thread_id) ?>',
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
                return nRow;
            },
            'fnDrawCallback': function () {

            },
            "aoColumns": [
                {mDataProp: "user_name"},
                {mDataProp: "body"},
                {mDataProp: "cdate"}
            ]
        });

        $('#myModal').on('hidden.bs.modal', function () {
            tblThread.fnDraw(false);
        });
    });
</script>
<?php if ($Owner || $GP['bulk_actions']) {
    echo admin_form_open('customers/customer_actions', 'id="action-form"');
} ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-envelope"></i><?=$subject?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"></p>

                <div class="table-responsive">
                    <table id="tblThread" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-condensed table-hover">
                        <thead>
                        <tr class="primary">
                            <th class="col-md-2">Sender</th>
                            <th class="col-md-8">Body</th>
                            <th class="col-md-2">Time</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="3" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                    </table>

                    <a href="<?=admin_url('messages/reply/'.$thread_id)?>" class="btn btn-primary btn-lg" data-toggle='modal' data-target='#myModal2'>
                        <i class="fa fa-reply"></i> Reply
                    </a>

                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
</script>


