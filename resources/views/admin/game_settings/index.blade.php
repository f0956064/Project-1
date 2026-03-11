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

<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Game Notice</h4>
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <form action="{{ route('game-settings.update-notice') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <textarea class="form-control" name="description" rows="5" placeholder="Enter game notice here..." required>{{ $game_notice->description ?? '' }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-md">Submit Notice</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Upload QR Code</h4>
                <form action="{{ route('game-settings.update-qr-code') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group text-center border p-3 bg-light rounded" style="min-height: 200px; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                @if($data->qrCode)
                                    <img src="{{ \App\Models\File::getFile($data->qrCode) }}" alt="QR Code" class="img-fluid rounded mb-2" style="max-height: 150px;">
                                    <p class="text-success mb-0"><small>Current QR Code</small></p>
                                @else
                                    <div class="text-muted">
                                        <i class="mdi mdi-qrcode-scan" style="font-size: 48px;"></i>
                                        <p class="mb-0">No QR Code Uploaded</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="qr_code">Select QR Code Image</label>
                                <input type="file" class="form-control" id="qr_code" name="qr_code" accept="image/*" required>
                                <small class="text-muted">Max size: 2MB. Allowed formats: jpeg, png, jpg, gif, svg.</small>
                            </div>
                            <button type="submit" class="btn btn-primary w-md mt-3">Upload QR Code</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Home Banner Images</h4>
                <form action="{{ route('game-settings.update-banner') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="banner_image">Upload Banner Images</label>
                                <input type="file" class="form-control" id="banner_image" name="banner_image[]" accept="image/*" multiple required>
                                <small class="text-muted">You can select multiple images. Recommended size: 1920x1080px. Max size: 2MB. Allowed formats: jpeg, png, jpg, gif, svg.</small>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-md">Upload Banners</button>
                </form>

                @if($data->banners && $data->banners->count() > 0)
                    <div class="mt-4">
                        <h5>Current Banners</h5>
                        <div class="row">
                            @foreach($data->banners as $banner)
                                <div class="col-md-3 mb-3">
                                    <div class="card bg-light border">
                                        <div class="card-body p-2 text-center">
                                            <img src="{{ \App\Models\File::getFile($banner) }}" alt="Home Banner" class="img-fluid rounded mb-2" style="height: 120px; object-fit: cover; width: 100%;">
                                            <form action="{{ route('game-settings.delete-banner', $banner->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this banner?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm w-100">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
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
