<div class="modal fade" id="callModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="callModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="callModalLabel"></h5>
                <button type="button" class="close" id="modal-close" style="display:none;" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" data-theme="dark" title="{{ @trans('Close') }}">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-center">
                    <span id="call-status" class="text-center font-size-lg label label-lg label-dark label-inline mr-2 mb-6">Call status:</span>
                    <h4 id="timer" class="text-center mb-6"></h4>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="button" id="hangup-btn" class="btn btn-sm btn-danger" aria-label="Hangup" data-toggle="tooltip" data-theme="dark" title="Hangup">
                        Hangup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>