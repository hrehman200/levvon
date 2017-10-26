<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
        var cTable = $('#CusData').dataTable({
            "aaSorting": [[1, "asc"]],
            "aLengthMenu": -1, // [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]]
            "bLengthChange": false,
            "iDisplayLength": -1,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('customers/getCustomersForecast') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                nRow.id        = aData[0];
                nRow.className = "customer_details_link";
                return nRow;
            },
            'fnDrawCallback': function () {
                getUnreadCompanyNoteCount();
            },
            "aoColumns": [{
                "bSortable": false,
                "bVisible": false
            }, null, null, null, null, null, null, {bSortable: false, bSearchable: false}]
        }).dtFilter([
            {column_number: 1, filter_default_label: "[Company]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[Name]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[Phone]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[Avg. Buying Days]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[Avg. Product Name]", filter_type: "text", data: []},
            {column_number: 6, filter_default_label: "[Days Inactive]", filter_type: "text", data: []},
        ], "footer");
        $('#myModal').on('hidden.bs.modal', function () {
            cTable.fnDraw(false);
        });
    });
</script>
<?php if ($Owner || $GP['bulk_actions']) {
    echo admin_form_open('customers/customer_actions', 'id="action-form"');
} ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-users"></i>Customers Forecast</h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="CusData" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr class="primary">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkth" type="checkbox" name="check"/>
                            </th>
                            <th>Company</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Avg. Buying Days</th>
                            <th>Avg. Product</th>
                            <th>Days Inactive</th>
                            <th style="min-width:135px !important;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
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

    $('#CusData').on('hover', '.list-notes', function (e) {

        if (e.type === 'mouseenter') {

            var lastNote = $(this).data("last-note");
            var el       = $(this);
            if (lastNote) {
                el.unbind('hover').popover({
                    content: lastNote.description,
                    title: '<b>' + lastNote.companyTitle + '</b>',
                    html: true,
                    placement: "left"
                }).popover('show');
            }
        } else {
            $(this).popover('hide');
        }
    });

    function getUnreadCompanyNoteCount() {

        var companyIds = $('.list-notes').map(function () {
            return $(this).data("company-id");
        }).get();

        $.ajax({
            type: 'get',
            url: '<?= admin_url('customers/getUnreadCompanyNoteCount/'); ?>',
            dataType: "json",
            data: {
                company_ids: companyIds
            },
            success: function (response) {
                for (var i = 0; i < response.data.length; i++) {
                    if (response.data[i].unread > 0) {
                        $('a[data-company-id="' + response.data[i].company_id + '"]')
                            .parents('tr:eq(0)').find('td').css({
                            'backgroundColor': 'lightgreen',
                            'fontWeight': 'bold'
                        }).end().end();
                    }
                }

            }
        });
    }
</script>
	

