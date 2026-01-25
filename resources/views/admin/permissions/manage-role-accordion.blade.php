@if($menus)
	<div class="col-lg-12">
		<div class="accordion" id="{{ $accordionParent }}">
		@foreach($menus as $key => $val)
			@if(($val['permissions'] && $val['permissions']->count()) || $val['children'])
			<div class="accordion-item">
		        <h2 class="accordion-header" id="heading{{ $val['id'] }}">
		            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $val['id'] }}" aria-expanded="false" aria-controls="collapse{{ $val['id'] }}">
		            {{ $val['menu'] }}
		            </button>
		        </h2>
		        <div id="collapse{{ $val['id'] }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $val['id'] }}" data-bs-parent="#{{ $accordionParent }}">
		            <div class="accordion-body">
		                @if($val['permissions'] && $val['permissions']->count())
			            	<div class="row">
				            	@foreach($val['permissions'] as $k => $p)
				            		<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 padding-0">
				            			<div class="d-flex">
                                            <div class="flex-shrink-0 align-self-center me-3">
                                                <input type="checkbox" switch="dark" name="pid[]" value="{{ $p->id }}" id="{{ $val['id'] . '-' . $p->id }}"  {{ in_array($p->id, $permission) ? 'checked' : '' }} switch="none"/>
									            <label for="{{ $val['id'] . '-' . $p->id }}" data-on-label="" data-off-label=""></label>
                                            </div>
                                            <div class="flex-grow-1 text-dark">
                                                {{ ucwords(str_replace("_", " ", $p->p_type)) }}
                                            </div>
                                        </div>
									</div>
				            	@endforeach
			            	</div>
			            @endif

			            @if($val['children'])
			            	<div class="row">
				            	{!! $model->manageRoleView($model, $routePrefix, $permission, $accordionParent, $val) !!}
			            	</div>
			            @endif
		            </div>
		        </div>
		    </div>
		    @endif
		@endforeach
		</div>
	</div>
@endif
