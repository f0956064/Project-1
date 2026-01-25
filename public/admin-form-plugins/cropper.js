function readImage(input, ratio) {
    var allowedExtension = ['jpeg', 'jpg', 'png'];
    var fileExtension = document.getElementById(input.id).value.split('.').pop().toLowerCase();
    var isValidFile = false;
    for (var index in allowedExtension) {

        if (fileExtension === allowedExtension[index]) {
            isValidFile = true;
            break;
        }
    }
    if (!isValidFile) {
        Toast.fire({
            icon: 'error',
            title: 'Only jpg,jpeg and png is supported in this field'
        });
        document.getElementById(input.id).value = "";
    } else {
        fileId = input.id;
        var croppieDiv = 'crop_wrap_' + fileId;
        if ($("#" + croppieDiv).length == 0) {
            $('.corp-cls').hide();
            $('.croppie-modal-wrap').append('<div id="' + croppieDiv + '" class="corp-cls"></div>');
        } else {
            previousImage[fileId] = cropper[fileId].data.url;
        }

        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                rawImage = e.target.result;
                if (!cropper[fileId]) {
                    _initializeCropper(croppieDiv, ratio);
                }
                $('#cropper_modal').modal('show');
                setTimeout(function() {
                    _bindImage();
                }, 300);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
}

function _initializeCropper(divId, ratio) {
    var el = document.getElementById(divId);
    ratio = ratio.split('x');
    cropper[fileId] = new Croppie(el, {
        enableExif: true,
        viewport: {
            width: ratio[0],
            height: ratio[1]
        },
        boundary: {
            width: 444,
            height: 255
        },
        showZoomer: true,
        enableResize: false,
        enableOrientation: true
    });
}

function _bindImage() {
    cropper[fileId].bind({
        url: rawImage,
    });
}

$('.crop-btn').on('click', function(e) {
    delete previousImage[fileId];
    cropper[fileId].result('base64').then(function(base64) {
        // do something with cropped blob
        $('.' + fileId).remove();
        $('#' + fileId+'-image').remove();
        $("#" + fileId + "_preview").attr('src', base64);
        $('#' + fileId).after('<div class="' + fileId + ' preview-wrap" data-file-id="' + fileId + '"><input type="hidden" name="' + fileId + '" value="' + base64 + '" /></div>');
        $('#cropper_modal').modal('hide');
    });
});

$('#cropper_modal').on('hide.bs.modal', function() {
    if (previousImage[fileId]) {
        cropper[fileId].bind({
            url: previousImage[fileId],
        });
    }
});

$('.rotate-left').on('click', function(e) {
    cropper[fileId].rotate(-90);
});

$('.rotate-right').on('click', function(e) {
    cropper[fileId].rotate(90);
});

$('body').on('click', '.preview-wrap', function(e) {
    console.log($(this).data('file-id'));
    fileId = $(this).data('file-id');
    $('.corp-cls').hide();
    $('#cropper_modal').modal('show');
    setTimeout(function() {
        $('#crop_wrap_' + fileId).show();
    }, 300);
});