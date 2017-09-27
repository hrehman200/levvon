<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title"
                id="myModalLabel"><?= $user->first_name.' '.$user->last_name ?> schedule for today</h4>
        </div>
        <div class="modal-body">
            <div class="table-responsive">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">

                            <table id="notesData"
                                   class="table table-bordered table-condensed table-hover table-striped reports-table">
                                <thead>
                                <tr class="primary">
                                    <th class="col-xs-1">Time</th>
                                    <th class="col-xs-11"><?= lang("note"); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if(count($schedules) > 0) {
                                    foreach($schedules as $schedule) {
                                        echo sprintf('<tr>
                                            <td>%s</td>
                                            <td>%s</td>
                                        ', $schedule['time'], $schedule['note']);
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>