@php ($attributes = (isset($value['attributes']) ? $value['attributes'] : []))
@php ($attributes['id'] = (isset($attributes['id']) ? $attributes['id'] : $key))
@php ($attributes['class'] = (isset($attributes['class']) ? $attributes['class'] : 'form-control form-control-lg'))
@php ($attributes['class'] .= ($errors->has($key) ? ' is-invalid' : ''))
@php ($floatOff = ['select', 'multiselect', 'file', 'radio', 'checkbox', 'editor', 'date', 'custom', 'switch'])
@php ($labelWidth = (isset($value['label_width']) ? $value['label_width'] : 'col-lg-3 col-md-3 col-sm-6 col-xs-12 text-right'))
@php ($fieldWidth = (isset($value['field_width']) ? $value['field_width'] : 'col-lg-6 col-md-6 col-xs-12 col-sm-12'))
@php ($value['value'] = isset($value['value']) ? $value['value'] : null)
@if($coverClass)
<div class="{{ $coverClass }}">
@endif
    @if(isset($value['label']) && $value['label'])
    <div class="{{ $labelWidth }}">
        <label for="{{ $attributes['id'] }}" class="form-label mb-0">{!! $value['label'] . (isset($attributes['required']) ? ' <span class="text-danger">*</span>' : '') !!}</label>
        @if(isset($value['help']))
        <p class="text-secondary"><small>{{ $value['help'] }}</small></p>
        @endif
    </div>
    @endif
    <div class="{{ $fieldWidth }}">
        <div class="{{ !in_array($value['type'], $floatOff) ? 'form-line' : '' }} {{ $errors->has($key) ? 'error' : '' }}">
            @if($value['type'] == 'text')
                {!! Form::text($key, $inputValue, $attributes) !!}
            @elseif($value['type'] == 'label')
                @php ($attributes['readonly'] = 'true')
                @php ($attributes['disabled'] = 'true')
                {!! Form::text($key, $inputValue, $attributes) !!}
            @elseif($value['type'] == 'email')
                {!! Form::email($key, $inputValue, $attributes) !!}
            @elseif($value['type'] == 'number')
                {!! Form::number($key, $inputValue, $attributes) !!}
            @elseif($value['type'] == 'date')
                @php($attributes['class'] .= ' datepicker')
                {!! Form::text($key, $inputValue, $attributes) !!}
            @elseif($value['type'] == 'time')
                @php($attributes['class'] .= ' timepicker')
                {!! Form::text($key, $inputValue, $attributes) !!}
            @elseif($value['type'] == 'datetime')
                @php($attributes['class'] .= ' datetimepicker')
                {!! Form::text($key, $inputValue, $attributes) !!}
            @elseif($value['type'] == 'password')
                {!! Form::password($key, $attributes) !!}
            @elseif($value['type'] == 'textarea')
                {!! Form::textarea($key, $inputValue, $attributes) !!}
            @elseif($value['type'] == 'editor')
                @php ($attributes['class'] .= ' editor')
                {!! Form::textarea($key, $inputValue, $attributes) !!}
                @once
                    @push('page_script')
                    <script src="{{ asset('admin-form-plugins/tinymce/tinymce.min.js')}}" ></script>
                    <script>
                        tinymce.init({
                            selector:'.editor',
                            theme: "modern",
                            height: 300,
                            plugins: [
                                "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                                "save table contextmenu directionality emoticons template paste textcolor"
                            ],
                            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor emoticons",
                            style_formats: [
                                {title: 'Bold text', inline: 'b'},
                                {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                                {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                                {title: 'Example 1', inline: 'span', classes: 'example1'},
                                {title: 'Example 2', inline: 'span', classes: 'example2'},
                                {title: 'Table styles'},
                                {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
                            ]
                        });
                    </script>
                    @endpush
                @endonce
            @elseif($value['type'] == 'select')
                @php($attributes['data-live-search'] = "true")
                @php($attributes['data-size'] = 5)
                @php($attributes['class'] .= ' live-search')
                @if(is_array($value['options']) && !empty($value['options']))
                    @php($value['options'] = ['' => 'Select Option'] + $value['options'])
                @endif
                {!! Form::select($key, $value['options'], $value['value'], $attributes) !!}
            @elseif($value['type'] == 'multiselect')
                @php ($attributes['multiple'] = 'true')
                {!! Form::select($key, $value['options'], $value['value'], $attributes) !!}
            @elseif($value['type'] == 'radio')
                <div class="clearfix"></div>
                <div class="row">
                @foreach($value['options'] as $k => $option)
                    <div class="{{ isset($value['attributes']['width']) ? $value['attributes']['width'] : 'col-lg-6 col-md-6 col-sm-12 col-xs-12' }}">
                        <div class="custom-control custom-radio custom-radio-primary mt-2">
                            {{ Form::radio($key, $k, ($value['value'] == $k ? true : false), [
                                'class' => $key .' custom-control-input',
                                'id' => $key . '-' . $k
                            ]) }}
                            <label class="custom-control-label" for="{{ $key . '-' . $k }}">{{ $option }}</label>
                        </div>
                    </div>
                @endforeach
                </div>
            @elseif($value['type'] == 'checkbox')
                <div class="clearfix"></div>
                <div class="row">
                    @foreach($value['options'] as $k => $option)
                    <div class="{{ isset($value['attributes']['width']) ? $value['attributes']['width'] : 'col-lg-6 col-md-6 col-sm-12 col-xs-12' }}">
                        <div class="custom-control custom-checkbox mb-4">
                            {{ Form::checkbox($key .'[]', $k, ((is_array($value['value']) && in_array($k, $value['value'])) ? true : false), [
                                'class' => $key .' custom-control-input',
                                'id' => $key . '-' . $k
                            ]) }}
                            <label class="custom-control-label" for="{{ $key . '-' . $k }}">{{ $option }}</label>
                        </div>
                    </div>
                    @endforeach
                </div>
            @elseif($value['type'] == 'switch')
                @foreach($value['options'] as $k => $option)
                <div class="{{ isset($value['attributes']['width']) ? $value['attributes']['width'] : 'col-lg-6 col-md-6 col-sm-12 col-xs-12' }}">
                    <div class="square-switchs">
                        <input type="checkbox" name="{{ $key }}" value="{{ $k }}" id="{{ $key . '-' . $k }}" {{ $value['value'] == $k ? 'checked' : '' }} class="{{ $key }}"  switch="dark"/>
                        <label for="{{ $key . '-' . $k }}" data-on-label="{{ $option }}" data-off-label=""></label>
                    </div>
                </div>
                @endforeach
            @elseif($value['type'] == 'color')
                <div class="input-group colorpicker-default" title="Using format option">
                    {!! Form::text($key, $inputValue, $attributes) !!}
                    <span class="input-group-append">
                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                    </span>
                </div>
                @once
                    @push('page_css')
                        <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css') }}">
                    @endpush
                    @push('page_script')
                        <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
                        <script src="{{ asset('assets/libs/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js') }}"></script>
                        <script type="text/javascript">
                            $(".colorpicker-default").colorpicker({
                                format: "hex"
                            });
                        </script>
                    @endpush
                @endonce
            @elseif($value['type'] == 'dropzone')
                <div class="box">
                    <div class="box__input">
                        <input type="file" name="{{ $key }}[]" id="file" class="box__file" data-multiple-caption="{count} files selected" multiple style="display: none;" />
                        <label for="file">
                            <span class="box__dragndrop">Drag and drop your files here to attach</span>
                            <br>
                            or <br>
                            <button type="button" onclick="document.getElementById('file').click()" class="btn btn-danger">BROWSE</button>
                        </label>
                    </div>
                </div>
                @once
                    @push('page_css')
                        <link href="{{ asset('assets/dropzone/dropzone.css') }}" rel="stylesheet" type="text/css" />
                    @endpush
                    @push('page_script')
                        <script src="{{ asset('assets/dropzone/dropzone.js') }}"></script>
                    @endpush
                @endonce
            @elseif($value['type'] == 'file')
                @php ($fileInputName = $key)
            @if(array_key_exists("cropper", $attributes) && $attributes['cropper'])
                @php ($cropRatio = (isset($attributes['ratio']) && $attributes['ratio']) ? $attributes['ratio'] : '400x300')
                @php ($attributes['onclick'] = "readImage(this, '". $cropRatio ."')")
                @php ($fileInputName .= '_input')
            @endif
            @if(!isset($attributes['id']))
                @php ($attributes['id'] = $key)
            @endif
            @php ($attributes['class'] .= ' custom-file-input')
            @if(isset($attributes['multiple']) && $attributes['multiple'])
                <div class="d-flex gallery-panel" id="posted_photo_image">
                    @if(isset($value['value']))
                        @foreach($value['value'] as $f => $file)
                            @php($file = isset($file['original']) ? $file : \App\Models\File::file($file))
                            @if(in_array($file['file_mime'], \App\Models\File::$fileValidations['image']['file_mimes']))
                                <div class="gallery-block animate" style="display: none;" id="{{ $key . $f . '_preview' }}">
                                    <img src="{{ $file['thumb'] }}">
                                    <a href="javascript:void(0);" class="gallery-close delete-attachment-form" data-id="{{ $file['id'] }}"><i class="mdi mdi-close-circle"></i></a>
                                </div>
                            @elseif($file['file_mime'])
                                <a href="{{ $file['original'] }}" target="_blank">View File</a>
                            @endif
                        @endforeach
                    @endif
                    <div class="gallery-block add-gellery" data-name="{{ $key }}">
                        <i class="dripicons-plus"></i>
                    </div>
                </div>
                @once
                    @push('page_script')
                    <script type="text/javascript">
                        var no_file = 1;
                        const processImages = async (input) => {
                            if (input.files && input.files[0]) {
                                for (var i = 0; i < input.files.length; ++i) {
                                    var reader = new FileReader();
                                    reader.onload = function(e) {
                                        var image_div = '<div id="' + no_file + '_preview" class="gallery-block animate" style="display: none;"> <img src="' + e.target.result + '" class="img-responsive" style="height: auto; width: 100%; float: left;" /><a href="javascript:void(0);" class="gallery-close" data-id="' + no_file + '_preview"><i class="mdi mdi-close-circle"></i></a></div>';
                                        $("#posted_photo_image").prepend(image_div);
                                        no_file = no_file + 1;
                                    };
                                    reader.readAsDataURL(input.files[i]);
                                }
                            }
                            return true;
                        }

                        function readURL(input) {
                            processImages(input).then(function() {
                                setTimeout(function() {
                                    animateZoomIn();
                                }, 200);
                            });
                        }

                        $("body").on("click", ".add-gellery", function() {
                            var name = $(this).data('name');
                            no_file = no_file + 1;
                            var file = '<input name="' + name + '[]" id="listing_photo' + no_file + '" multiple="true" onchange="readURL(this);" type="file" style="display:none;"/>';
                            $("#posted_photo_image").append(file);
                            $("#listing_photo" + no_file).click();
                        });

                        $("body").on('click', '.gallery-close', function() {
                            var file_no = $(this).data('id');
                            $("#posted_photo_image").find("#" + file_no).remove();
                        });
                    </script>
                    @endpush
                @endonce
            @else
                @php($originalFileName = '')
                @if($value['value'] && isset($value['value']['file_name_original']))
                    @php($originalFileName = $value['value']['file_name_original'])
                @endif

                @php($value['value'] = isset($value['value']['original']) ? $value['value'] : \App\Models\File::file($value['value']))

                @if(array_key_exists("preview", $attributes) && $attributes['preview'])
                    @php ($attributes['onchange'] = "readImage(this)")
                @endif
                @if(in_array($value['value']['file_mime'], \App\Models\File::$fileValidations['image']['file_mimes']))
                    {{--<div class="col-lg-4">
                        <img src="{{ $value['value']['thumb'] }}" class="img-responsive img-thumbnail" id="{{ $key . '_preview' }}" />
                        <a href="javascript:void(0);" class="gallery-close delete-attachment-form" data-id="{{ $value['value']['id'] }}"><i class="mdi mdi-close-circle"></i></a>
                    </div>--}}
                @elseif($value['value']['file_mime'])
                    <div class="card">
                        <div class="card-body">
                            {{ $originalFileName }}
                            <a href="{{ $value['value']['original'] }}" target="_blank" class="btn btn-primary btn-sm float-right" download="">Download</a>
                        </div>
                    </div>
                @else
                    {{--<div class="col-lg-4" style="display: none;">
                                                    <img src="" class="img-responsive img-thumbnail" id="{{ $key . '_preview' }}" />
                </div>--}}
                @endif
                <div class="col-md-6 p-0">
                    <div class="custom-dropzone">
                        <div class="custom-dropzone-inner {{ (isset($value['attributes']['ratio']) && $value['attributes']['ratio'] == 1) ? 'square-dropzone' : ((isset($value['attributes']['ratio']) && $value['attributes']['ratio'] == 1.6) ? 'rectangle-dropzone' : '') }}" id="{{ $key . '_preview' }}" style="@if(in_array($value['value']['file_mime'], \App\Models\File::$fileValidations['image']['file_mimes']))
                                                background: url('{{ $value['value']['thumb'] }}');
                                                @endif
                                                ">
                            <input type="hidden" name="{{ $key . '_preview_image' }}" id="{{ $key . '_preview_image' }}" value="{{ $value['value']['thumb'] }}">
                            {!! Form::file($fileInputName, $attributes) !!}
                            <div class="custom-dz-message custom-needsclick">
                                <div class="upload-icon">
                                    <i class="display-4 bx bxs-cloud-upload"></i>
                                </div>
                                <h4>{{ isset($attributes['placeholder']) ? $attributes['placeholder'] : "Upload " . $value['label'] }}</h4>
                            </div>
                        </div>
                    </div>
                    @if(isset($value['help']))
                    <small>{!! $value['help'] !!}</small>
                    @endif
                </div>
                @endif
            @elseif($value['type'] == 'custom')
                {!! $value['value'] !!}
            @endif
        </div>

        @if ($errors->has($key))
            <label class="invalid-feedback text-danger error">{{ $errors->first($key) }}</label>
        @endif
    </div>
@if($coverClass)
</div>
@endif