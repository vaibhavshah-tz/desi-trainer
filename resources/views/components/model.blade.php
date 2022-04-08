<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ @trans($title) ?? ''}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" data-theme="dark" title="{{ @trans('Close') }}">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            {{ $form }}
        </div>
    </div>
</div>