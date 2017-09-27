<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo (($schedule == null)?lang('add_schedule'):lang('add_schedule')) . " (" . $user->first_name.' '.$user->last_name . ")"; ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open("daily_schedule/edit/".$date.'/'.(($schedule == null)?'':$schedule->id), $attrib); ?>
        <input type="hidden" name="date" value="<?=$date?>" />

        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row">
                <div class="col-sm-12">

                    <div class="form-group">
                        Time*
                        <div class="controls">
                            <?php echo form_input('time', set_value('time', (($schedule == null)?'':$schedule->time)), 'class="form-control time" id="time" required="required"'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <?php echo lang('note', 'note'); ?>*
                        <div class="controls">
                            <?php echo form_textarea('note', (($schedule == null)?'':$schedule->note) , 'class="form-control" id="note"'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_schedule', (($schedule == null)?lang('add_schedule'):lang('edit_schedule')), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>

