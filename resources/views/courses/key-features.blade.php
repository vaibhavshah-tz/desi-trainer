<div id="pane-B" class="card tab-pane fade" role="tabpanel" aria-labelledby="tab-B">
	<div class="card-header" role="tab" id="heading-B">
		<h5 class="mb-0">
			<a class="collapsed" data-toggle="collapse" href="#collapse-B" aria-expanded="false" aria-controls="collapse-B">
				Course Features
			</a>
		</h5>
	</div>
	<div id="collapse-B" class="collapse" data-parent="#content" role="tabpanel" aria-labelledby="heading-B">
		<div class="card-body">
			<div class="errors"></div>
			@if(isset($courseDetails))
			{{ Form::model($courseDetails, ['route' => ['courses.key-features'], 'method' => 'patch', 'id' => 'course-key-features', 'enctype' => 'multipart/form-data']) }}
			@else
			{{ Form::open(['route' => 'courses.key-features' ,'name' => 'course-key-features', 'id' => 'course-key-features', 'enctype' => 'multipart/form-data']) }}
			@endif
			<div class="kyc-frm">
				<div class="keyfeture-scroll">
					<label>Key Features </label>
					@php $course_features = !empty(old('courseFeatures')) ? old('courseFeatures') : $courseDetails->courseFeatures ?? [] @endphp
					@forelse($course_features as $key => $feature)
					<div class="form-group row align-items-center">
						<div class="col-11">
							{{ Form::text("course_features[$key][title]",$feature['title'] ?? '', ['class' => 'form-control', 'placeholder' => 'Enter key feature']) }}
						</div>
						@if(!$loop->first)
						<div class="col-1 p-0">
							<i class="la la-trash del-ic remove-keyword" data-toggle="tooltip" data-theme="dark" title="{{ __('Delete') }}"></i>
						</div>
						@endif
					</div>

					@empty
					<div class="form-group row align-items-center">
						<div class="col-11">
							{{ Form::text('course_features[0][title]',null, ['class' => 'form-control', 'placeholder' => 'Add here']) }}
						</div>

					</div>
					@endforelse
					<div class="add-new-key-features"></div>
					<div class="mt-2 row">
						<div class="col-11 d-flex justify-content-end">
							<a href="javascript:;" class="btn btn-primary add-keyword" data-toggle="tooltip" data-theme="dark" title="{{ __('Add New') }}">
								<i class="la la-plus"></i>{{ __('Add New') }}</a>
						</div>
					</div>
				</div>
				<div class="mt-2">
					{{ Form::button(__('Previous'), ['id' => 'previous','class' => 'btn btn-primary ml-2 btnPrevious', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'title' => __('Previous'), 'data-id' => 'tab-A']) }}
					{{ Form::button(__('Save & Next'), ['class' => 'btn btn-primary ml-2 saveKeyFeatures btnNext', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'title' => __('Save & Next'), 'data-id' => 'tab-C']) }}
					<a href="{{ route('courses.index') }}" class="btn btn-danger ml-2" data-toggle="tooltip" data-theme="dark" title="{{ __('Cancel') }}">{{ __('Cancel') }}</a>
				</div>
			</div>
			{{ Form::close() }}
		</div>
	</div>
</div>