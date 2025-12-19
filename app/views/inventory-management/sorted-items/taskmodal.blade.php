<div class="modal fade"  id="task-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-perusu" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Purchase Request List(s)</h4>
                <p>of sorted transactions batch number # <span class="label label-info" id="task-modal-subtitle"></span></p>
                <p class="text-right"><span class="small label label-default">* Fill the required fields and submit.</span></p>
                {{-- <div class="pull-left">
                    <a href="#" id="all-tasks" class="btn btn-sm btn-default active filter-btn">
                        All
                    </a>
                    <a href="#"  id="active-tasks" class="btn btn-sm btn-default filter-btn">
                        Active
                    </a>
                    <a href="#" id="completed-tasks" class="btn btn-sm btn-default filter-btn">Completed</a>
                </div> --}}
                {{-- <div class="text-right">
                    <small id="active-tasks-counter"></small>
                </div> --}}
            </div>
            <div class="modal-body">
                <div class="panel panel-default">
                   <div id="task-table-body"></div>
                </div>
            </div>
            <div class="modal-footer clear-fix"></div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->