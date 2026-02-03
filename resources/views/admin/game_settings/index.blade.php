@php ($headerOption = [
  'title' => $module,
  'header_buttons' => [],
  'filters' => []
])
@extends('admin.layouts.layout', $headerOption)

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Setting Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Show Games</td>
                                <td>
                                    <div class="square-switchs">
                                        <input type="checkbox" 
                                               id="show_games" 
                                               switch="bool" 
                                               {{ $data->show_games ? 'checked' : '' }}
                                               onchange="updateSetting('show_games', this.checked ? 1 : 0)">
                                        <label for="show_games" data-on-label="Active" data-off-label="Inactive"></label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Deposit</td>
                                <td>
                                    <div class="square-switchs">
                                        <input type="checkbox" 
                                               id="deposit" 
                                               switch="bool" 
                                               {{ $data->deposit ? 'checked' : '' }}
                                               onchange="updateSetting('deposit', this.checked ? 1 : 0)">
                                        <label for="deposit" data-on-label="Active" data-off-label="Inactive"></label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Withdrawal</td>
                                <td>
                                    <div class="square-switchs">
                                        <input type="checkbox" 
                                               id="withdrawal" 
                                               switch="bool" 
                                               {{ $data->withdrawal ? 'checked' : '' }}
                                               onchange="updateSetting('withdrawal', this.checked ? 1 : 0)">
                                        <label for="withdrawal" data-on-label="Active" data-off-label="Inactive"></label>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('page_script')
<script>
    function updateSetting(column, value) {
        $.ajax({
            url: "{{ route('game-settings.update') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                column: column,
                value: value
            },
            success: function (response) {
                if (response.status == 'success') {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                toastr.error('Something went wrong. Please try again.');
            }
        });
    }
</script>
@endpush
@endsection
