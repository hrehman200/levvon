<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
    $(function () {
        $('.bcc').hide();
        $(".toggle_form").slideDown('hide');
        $('.toggle_form').click(function () {
            $("#bcc").slideToggle();
            return false;
        });
    });
</script>

<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel">Send Message</h4>
        </div>
        <?php
        $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        if(@$thread_id > 0) {
            echo admin_form_open("messages/reply/" . @$thread_id, $attrib);
        } else {
            echo admin_form_open("messages/add/" . $note_id, $attrib);
        }
        ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">
                To
                <select name="to[]" id="to" class="form-control selectpicker" required="required" multiple>
                <?php
                foreach($users as $u) {
                    if($u['id'] != $this->session->userdata('user_id')) {
                        echo sprintf('<option value="%d">%s</option>', $u['id'], $u['name']);
                    }
                }
                ?>
                </select>
            </div>
            <div id="bcc" style="display:none;">
                <div class="form-group">
                    CC
                    <input type="text" name="cc" id="cc" class="form-control" placeholder="abc@gmail.com, xyz@gmail.com" />
                </div>
                <div class="form-group">
                    BCC
                    <input type="text" name="bcc" id="bcc" class="form-control" placeholder="abc@gmail.com, xyz@gmail.com" />
                </div>
            </div>
            <div class="form-group">
                Subject
                <?php echo form_input($subject, '', 'class="form-control" id="subject" pattern=".{2,255}" required="required" '.((@$thread_id>0)?'readonly="readonly"':'')); ?>
            </div>
            <div class="form-group">
                <?= lang("message", 'note'); ?>
                <?php
                echo form_textarea($note, (isset($_POST['note']) ? $_POST['note'] : ""), 'class="form-control" id="note" ');
                ?>
            </div>

        </div>
        <div class="modal-footer">
            <a href="#"
               class="btn btn-sm btn-default pull-left toggle_form"><?php echo $this->lang->line("show_bcc"); ?></a>
            <?php echo form_submit('send_email', 'Send Email', 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
