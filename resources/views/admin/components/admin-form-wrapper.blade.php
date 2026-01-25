@if(isset($form['tabs']) && !empty($form['tabs']))
    <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
        @foreach($form['tabs'] as $tab)
        <li role="presentation" class="nav-item"><a href="{{ $tab['url'] }}" class="nav-link {{ $tab['active'] ? 'active' : '' }}">{{ $tab['name'] }}</a></li>
        @endforeach
    </ul>
    @endif
    <div class="tab-content pt-3">
@php($requestParam = Request::all())
@if(isset($form['route_param']) && $form['route_param'])
    @php($requestParam = array_merge($requestParam, $form['route_param']))
@endif
@if(isset($id) && $id)
    @php($requestParam[] = $id)
    @php($formAttributes = [
        'method'    => 'PATCH',
        'route'     => [
            $form['route'],
            $requestParam
        ],
        'class'     => 'form-horizontal custom-validation',
        'enctype'   => 'multipart/form-data'
        ])
    @if(isset($form['attributes']))
        @php($formAttributes = array_merge($formAttributes, $form['attributes']))
    @endif
    {!! Form::model($data, $formAttributes) !!}
@else
    @php($formAttributes = array(
        'route'     => [
            $form['route'],
            $requestParam
        ],
        'method'    => 'POST',
        'class'     => 'form-horizontal custom-validation',
        'enctype'   => 'multipart/form-data'
    ))
    @if(isset($form['attributes']))
        @php($formAttributes = array_merge($formAttributes, $form['attributes']))
    @endif
    {!! Form::open($formAttributes) !!}
@endif
    @if(!empty($form['fields']))
        @php ($multiSelectScript = 0)
        @php ($dateTimePicker = 0)
        @php ($fileCropper = 0)
        @php ($fileCropper = 0)
        @php ($oneElementEachRow = true)
        @php ($rowClass = 'form-group row mb-4')
        @foreach($form['fields'] as $key => $value)
            @if(isset($value['row_width']))
                @if($oneElementEachRow)
                    <div class="form-group row mb-4'">
                @endif
                @php ($rowClass =  $value['row_width'])
                @php ($oneElementEachRow = false)
            @else
                @if(!$oneElementEachRow)
                    </div>
                @endif
                @php ($rowClass = 'form-group row mb-4')
                @php ($oneElementEachRow = true)
            @endif
            @php ($extraWidth = (isset($value['extra']['field_width']) ? $value['extra']['field_width'] : 'col-lg-3 col-md-3 col-xs-12 col-sm-12'))
            @php ($dateTimePicker = (!$dateTimePicker && in_array($value['type'], ['date', 'time', 'datetime'])) ? 1 : $dateTimePicker)
            @php ($fileCropper = (!$fileCropper && $value['type'] == 'file' && isset($value['attributes']) && array_key_exists("cropper", $value['attributes']) && $value['attributes']['cropper']) ? 1 : $fileCropper)
            @php ($coverClass = '')
            @php ($inputValue = (isset($value['value']) ? $value['value'] : null))
            @if(!in_array($value['type'], ['html', 'include', 'hidden', 'group']))

                <div class="{{ $rowClass }}" id="row-{{ str_replace('[]', '', $key) }}">
                    @include('admin.components.admin-form-field')
                    @if(isset($value['extra']) && $value['extra'])
                    <div class="{{ $extraWidth }}">
                        @php($value = $value['extra'])
                        @php($coverClass = 'row')
                        @include('admin.components.admin-form-field')
                    </div>
                    @endif
                </div>
            @elseif($value['type'] == 'group')
                @php($groupWidth = $value['width'] ?? 'col-xl-6 col-md-6 col-sm-6 col-xs-12')
                <div class="row">
                @foreach($value['fields'] as $k => $v)
                    @php ($groupWidth = (isset($v['field_width']) ? $v['field_width'] : $groupWidth))
                    <div class="{{ $groupWidth }}" id="row-{{ str_replace('[]', '', $k) }}">
                        @php($key = $k)
                        @php($value = $v)
                        @php ($inputValue = (isset($value['value']) ? $value['value'] : null))
                        @php ($dateTimePicker = (!$dateTimePicker && in_array($value['type'], ['date', 'time', 'datetime'])) ? 1 : $dateTimePicker)
                        @php($coverClass = 'form-group row')
                        @include('admin.components.admin-form-field')
                    </div>
                @endforeach
                </div>
            @elseif($value['type'] == 'hidden')
                {!! Form::hidden($key, $inputValue) !!}
            @elseif($value['type'] == 'html')
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="row-{{ $key }}">{!! $value['value'] !!}</div>
                <div class="clearfix"></div>
            @elseif($value['type'] == 'include')
                @include($value['value'])
            @endif
        @endforeach

        @if(!$oneElementEachRow)
            </div>
        @endif

        @if($dateTimePicker)
            @include('admin.components.date-time-picker')
        @endif

        @if($fileCropper)
            @push('page_css')
            <link rel="stylesheet" href="{{ asset('assets/js/cropper/cropper.css') }}">
            @endpush
            @push('page_script')
           <div class="modal fade rewards-cop-mod" id="cropper_modal" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Crop uploaded image</h4>
                        </div>
                        <div class="modal-body text-center">
                            <div class="container">
                                <div class="row" id="upload-from-computer">
                                    <div class="col-md-12 m-auto">
                                        <!-- <h3>Demo:</h3> -->
                                        <input type="hidden" name="inputtype" id="inputType" value="">
                                        <div class="docs-demo">
                                            <div class="img-container">
                                                <img src="#" id="imgPreview" alt="Picture" style="max-height: 70vh">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3" style="display: none;">
                                        <!-- <h3>Preview:</h3> -->

                                        <!-- <h3>Data:</h3> -->
                                        <div class="docs-data" style="display: none;">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-prepend">
                                                    <label class="input-group-text" for="dataX">X</label>
                                                </span>
                                                <input type="text" class="form-control" id="dataX" placeholder="x">
                                                <span class="input-group-append">
                                                    <span class="input-group-text">px</span>
                                                </span>
                                            </div>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-prepend">
                                                    <label class="input-group-text" for="dataY">Y</label>
                                                </span>
                                                <input type="text" class="form-control" id="dataY" placeholder="y">
                                                <span class="input-group-append">
                                                    <span class="input-group-text">px</span>
                                                </span>
                                            </div>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-prepend">
                                                    <label class="input-group-text" for="dataWidth">Width</label>
                                                </span>
                                                <input type="text" class="form-control" id="dataWidth" placeholder="width">
                                                <span class="input-group-append">
                                                    <span class="input-group-text">px</span>
                                                </span>
                                            </div>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-prepend">
                                                    <label class="input-group-text" for="dataHeight">Height</label>
                                                </span>
                                                <input type="text" class="form-control" id="dataHeight" placeholder="height">
                                                <span class="input-group-append">
                                                    <span class="input-group-text">px</span>
                                                </span>
                                            </div>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-prepend">
                                                    <label class="input-group-text" for="dataRotate">Rotate</label>
                                                </span>
                                                <input type="text" class="form-control" id="dataRotate" placeholder="rotate">
                                                <span class="input-group-append">
                                                    <span class="input-group-text">deg</span>
                                                </span>
                                            </div>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-prepend">
                                                    <label class="input-group-text" for="dataScaleX">ScaleX</label>
                                                </span>
                                                <input type="text" class="form-control" id="dataScaleX" placeholder="scaleX">
                                            </div>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-prepend">
                                                    <label class="input-group-text" for="dataScaleY">ScaleY</label>
                                                </span>
                                                <input type="text" class="form-control" id="dataScaleY" placeholder="scaleY">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="upload-from-library"></div>
                            </div>
                            <!-- <div class="croppie-modal-wrap" style="width: 100%; height: 100%;"></div>
                                <button class="btn btn-outline-primary rotate-left" data-toggle="tooltip" data-original-title="Rotate Left"><i class="bx bx-rotate-left"></i></button>
                                <button class="btn btn-outline-primary rotate-right" data-toggle="tooltip" data-original-title="Rotate Right"><i class="bx bx-rotate-right"></i></button> -->
                        </div>
                        <div class="modal-footer justify-content-center">
                            <div class="rows" id="actions">
                                    <div class="docs-buttons ">
                                        <div class="btn-groups">
                                            <button type="button" class="btn btn-primary" data-method="rotate" data-option="-45" title="Rotate Left">
                                                <span class="docs-tooltip" data-toggle="tooltip" title="cropper.rotate(-45)">
                                                    <span class="bx bx-rotate-left"></span>
                                                </span>
                                            </button>
                                            <button type="button" class="btn btn-primary" data-method="rotate" data-option="45" title="Rotate Right">
                                                <span class="docs-tooltip" data-toggle="tooltip" title="cropper.rotate(45)">
                                                    <span class="bx bx-rotate-right"></span>
                                                </span>
                                            </button>
                                            <label class="btn btn-primary btn-upload mt-2" for="inputImage" title="Upload image file">
                                                <input type="file" class="sr-only" id="inputImage" name="file" accept="image/*">
                                                <span class="docs-tooltip" data-toggle="tooltip" title="Import image with Blob URLs">
                                                    <i class="bx bx-cloud-upload"></i> Uplaod
                                                </span>
                                            </label>
                                            {{--<button type="button" class="btn btn-success">Upload from Library</button>--}}
                                            <button type="button" class="btn btn-success" data-method="getCroppedCanvas" data-option="{ &quot;maxWidth&quot;: 4096, &quot;maxHeight&quot;: 4096 }">
                                                <span class="docs-tooltip">
                                                    <i class="bx bx-crop"></i> Crop
                                                </span>
                                            </button>
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        </div>

                                    </div><!-- /.docs-buttons -->

                                    <div class="col-md-3 docs-toggles">


                                    </div>
                                    <!-- /.docs-toggles -->
                                </div>


                            <!-- <button type="button" class="btn btn-primary crop-btn">Crop</button> -->
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->
            <script src="{{ asset('assets/js/cropper/cropper.js') }}"></script>
            <script src="{{ asset('assets/js/cropper/main.js') }}"></script>
            <script>
                var cropper = [];
                var previousImage = [];
                var rawImage, fileId;
            </script>
            @endpush
        @endif
    @endif
    @if(isset($include_page))
        @include($include_page)
    @endif
    </div>
    <div class="clearfix"></div>
    @if(!isset($form['custom_buttons']))
        <div class="card-footer">
            <button type="submit" class="{{ \Config::get('view.buttons.primary') }} btn-lg">{!! (isset($form['submit_text']) ? $form['submit_text'] : \Config::get('settings.icon_save') . ' <span>Save Changes</span>') !!}</button>
            @if(isset($form['back_route']))
                <a href="{{ $form['back_route'] }}" class="{{ \Config::get('view.buttons.secondary') }} btn-lg">{!! \Config::get('settings.icon_back') !!} <span>Back</span></a>
            @endif
        </div>
    @else
        <div class="card-footer">
            @foreach($form['custom_buttons'] as $button)
                <button type="{{ isset($button['type']) ? $button['type'] : 'submit' }}" {!! isset($button['attributes']) ? \App\Helpers\Helper::getAttr($button['attributes']) : '' !!}>{!! $button['text'] !!}</button>
            @endforeach
        </div>
    @endif
{!! Form::close() !!}

<div class="clearfix"></div>
@push('page_script')
    <script src="{{ asset('assets/libs/parsleyjs/parsley.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-validation.init.js') }}"></script>
@endpush
@if(isset($form['include_scripts']))
    @push('page_script')
        {!! $form['include_scripts'] !!}
    @endpush
@endif