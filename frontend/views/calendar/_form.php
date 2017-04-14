<div id="calendarModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                <h4 id="modalTitle" class="modal-title"></h4>
            </div>
            <div id="modalBody" class="modal-body">
                <?= $this->render('_taskForm', [
                    'model' => $model,
                    'woj' => $woj,
                    'accident' =>$accident,
                    'agent' => $agent,
                ]) ?>

             </div>
            <div class="modal-footer">
                <button type="submit" id="submitButton" class="btn btn-success">Dodaj</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
            </div>
        </div>
     </div>
</div>
