@if (count($errors) > 0)
			<!-- Start Box Body -->
                  <div class="box-body">
						<div class="alert alert-danger" id="dangerAlert">

							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">×</span>
								</button>

							<i class="glyphicon glyphicon-alert myicon-right"></i> {{{ trans('auth.error_desc') }}} <br>
							<ul id="wrap_validation" class="px-4 pt-1">
								@foreach ($errors->all() as $error)
									<li> {{{ $error }}}</li>
								@endforeach
							</ul>
						</div>
                </div><!-- /.box-body -->


					@endif
