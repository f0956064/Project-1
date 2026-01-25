@if(isset($data) && $data)
	@section('card-footer')
	<div class="card-footer">
		<div class="row">
			<div class="col-lg-12">
				<div class="d-flex">
                    <div class="flex-shrink-0 align-self-center me-3 {{ request()->ajax() ? 'ajax-pagination' : '' }}">
                        {{ $data->appends(request()->input())->links() }}
                    </div>
                    <div class="flex-grow-1 text-right">
                        Total records: {{ $data->total() }}
                    </div>
                </div>
			</div>
		</div>
	</div>
	@endsection
@endif