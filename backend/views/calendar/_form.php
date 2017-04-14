<div id="calendarModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                <h4 id="modalTitle" class="modal-title"></h4>
            </div>
            <div id="modalBody" class="modal-body">
                <form id="addNews" onsubmit="return false;">

                    <div class="form-group required field-news">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-pencil fa-lg"></i> Informacja
                            </span>
                            <input type="text" id="newsText" class="form-control" required name="newsText" maxlength="100">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="when">Kiedy:</label>
                        <input type="hidden" id="agentID" name="agent" value="<?php echo $id;?>"/>
                        <input type="hidden" id="startTime" name="start"/>
                        <input type="hidden" id="endTime" name="end"/>
                        <div class="controls controls-row" id="when" style="margin-top:5px;">
                        </div>
                     </div>
               </form>

             </div>
            <div class="modal-footer">
                <button type="submit" id="submitButton" class="btn btn-success">Dodaj</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
            </div>
        </div>
     </div>
</div>
