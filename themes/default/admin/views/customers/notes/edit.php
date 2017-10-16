<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo (($note == null)?lang('add_note'):lang('edit_note')) . " (" . $company->name . ")"; ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id'=>'formEditNote');
        echo admin_form_open("customers/edit_note/".$company->id."/XXXXX/".(($note == null)?'':$note->id), $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row">
                <div class="col-sm-12">

                    <div class="form-group">
                        <?php echo lang('title', 'Title'); ?>*
                        <div class="controls">
                            <?php echo form_input('title', set_value('title', (($note == null)?'':$note->companyTitle)), 'class="form-control" id="title" required="required"'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <?php echo lang('note', 'note'); ?>*
                        <div class="controls">
                            <?php echo form_textarea('note', (($note == null)?'':$note->description) , 'class="form-control" id="note"'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_note', (($note == null)?lang('add_note'):lang('edit_note')), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>

<script type="text/javascript">
    var pathname = window.location.pathname;
    pathname = pathname.substr(pathname.lastIndexOf('/') + 1);

    var oldAction = $('#formEditNote').attr('action');
    var newAction = oldAction.replace('XXXXX', pathname);
    $('#formEditNote').attr('action', newAction);
</script>
