$('.custom-file-input').click(function (e) {
  e.preventDefault();
});
var aspectRatio = 10/16;
var Cropper = window.Cropper;
  var URL = window.URL || window.webkitURL;
  var container = document.querySelector('.img-container');
  var image = null;
  var download = document.getElementById('download');
  var actions = document.getElementById('actions');
  var dataX = document.getElementById('dataX');
  var dataY = document.getElementById('dataY');
  var dataHeight = document.getElementById('dataHeight');
  var dataWidth = document.getElementById('dataWidth');
  var dataRotate = document.getElementById('dataRotate');
  var dataScaleX = document.getElementById('dataScaleX');
  var dataScaleY = document.getElementById('dataScaleY');
  var options = null;
  var cropper = null;
  var originalImageURL = null;
  var uploadedImageType = 'image/jpeg';
  var uploadedImageName = 'cropped.jpg';
  var uploadedImageURL;
function initCropper(){
  image = container.getElementsByTagName('img').item(0);
  originalImageURL = image.src;
  options = {
    aspectRatio: aspectRatio,
    preview: '.img-preview',
    dragMode: 'move',
    viewMode: 1,
    data: {
      x: dataX.value,
      y: dataY.value,
      height: dataHeight.value,
      width: dataWidth.value,
      scaleX: dataScaleX.value,
      scaleY: dataScaleY.value
    },
    crop: function (e) {
      var data = e.detail;
      dataX.value = Math.round(data.x);
      dataY.value = Math.round(data.y);
      dataHeight.value = Math.round(data.height);
      dataWidth.value = Math.round(data.width);
      dataRotate.value = typeof data.rotate !== 'undefined' ? data.rotate : '';
      dataScaleX.value = typeof data.scaleX !== 'undefined' ? data.scaleX : '';
      dataScaleY.value = typeof data.scaleY !== 'undefined' ? data.scaleY : '';
    }
  };
  if(cropper.length){
    cropper.destroy();
  }
  cropper = new Cropper(image, options);
}
function readImage(input, ratio) {
  // console.log(input.id);
  var allowedExtension = ['jpeg', 'jpg', 'png'];
  var fileExtension = document.getElementById(input.id).value.split('.').pop().toLowerCase();
  var isValidFile = false;
  aspectRatio = ratio;
  $("#inputType").val(input.id);
  let defaultImage = $("#" + input.id + "_preview_image").val();
  $('#imgPreview').attr('src', defaultImage);
  $('#cropper_modal').modal('show');
  if($("#" + input.id + "_base_code").length) {
    $(".img-container img").attr("src", $("#" + input.id + "_base_code").data('val'));
  }
  dataX = document.getElementsByName(input.id +'_X');
  dataY = document.getElementsByName(input.id +'_Y');
  dataHeight = document.getElementsByName(input.id +'_Height');
  dataWidth = document.getElementsByName(input.id +'_Width');
  dataRotate = document.getElementsByName(input.id +'_Rotate');
  dataScaleX = document.getElementsByName(input.id +'_ScaleX');
  dataScaleY = document.getElementsByName(input.id +'_ScaleY');
  setTimeout(function(){
    initCropper();
  }, 500);
  return false;
}
window.onload = function () {
  'use strict';
  
  initCropper();
  

  // Buttons
  if (!document.createElement('canvas').getContext) {
    $('button[data-method="getCroppedCanvas"]').prop('disabled', true);
  }

  if (typeof document.createElement('cropper').style.transition === 'undefined') {
    $('button[data-method="rotate"]').prop('disabled', true);
    $('button[data-method="scale"]').prop('disabled', true);
  }

  // Options
  actions.querySelector('.docs-toggles').onchange = function (event) {
    var e = event || window.event;
    var target = e.target || e.srcElement;
    var cropBoxData;
    var canvasData;
    var isCheckbox;
    var isRadio;

    if (!cropper) {
      return;
    }

    if (target.tagName.toLowerCase() === 'label') {
      target = target.querySelector('input');
    }

    isCheckbox = target.type === 'checkbox';
    isRadio = target.type === 'radio';

    if (isCheckbox || isRadio) {
      if (isCheckbox) {
        options[target.name] = target.checked;
        cropBoxData = cropper.getCropBoxData();
        canvasData = cropper.getCanvasData();

        options.ready = function () {
          console.log('ready');
          cropper.setCropBoxData(cropBoxData).setCanvasData(canvasData);
        };
      } else {
        options[target.name] = target.value;
        options.ready = function () {
          console.log('ready');
        };
      }

      // Restart
      cropper.destroy();
      cropper = new Cropper(image, options);
    }
  };

  // Methods
  actions.querySelector('.docs-buttons').onclick = function (event) {
    var e = event || window.event;
    var target = e.target || e.srcElement;
    var cropped;
    var result;
    var input;
    var data;
    if (!cropper) {
      return;
    }

    while (target !== this) {
      if (target.getAttribute('data-method')) {
        break;
      }

      target = target.parentNode;
    }

    if (target === this || target.disabled || target.className.indexOf('disabled') > -1) {
      return;
    }

    data = {
      method: target.getAttribute('data-method'),
      target: target.getAttribute('data-target'),
      option: target.getAttribute('data-option') || undefined,
      secondOption: target.getAttribute('data-second-option') || undefined
    };

    cropped = cropper.cropped;

    if (data.method) {
      if (typeof data.target !== 'undefined') {
        input = document.querySelector(data.target);

        if (!target.hasAttribute('data-option') && data.target && input) {
          try {
            data.option = JSON.parse(input.value);
          } catch (e) {
            console.log(e.message);
          }
        }
      }

      switch (data.method) {
        case 'rotate':
          if (cropped && options.viewMode > 0) {
            cropper.clear();
          }

          break;

        case 'getCroppedCanvas':
          try {
            data.option = JSON.parse(data.option);
          } catch (e) {
            console.log(e.message);
          }

          if (uploadedImageType === 'image/jpeg') {
            if (!data.option) {
              data.option = {};
            }

            data.option.fillColor = '#fff';
          }

          break;
      }

      result = cropper[data.method](data.option, data.secondOption);

      switch (data.method) {
        case 'rotate':
          if (cropped && options.viewMode > 0) {
            cropper.crop();
          }

          break;

        case 'scaleX':
        case 'scaleY':
          target.setAttribute('data-option', -data.option);
          break;

        case 'getCroppedCanvas':
          if (result) {
            let fileId = $("#inputType").val();
            let base64 = result.toDataURL(uploadedImageType);
            var fileInput = document.getElementById(fileId);

            var reader = new FileReader();
            var originalImage = null;
            reader.readAsDataURL(fileInput.files[0]);
            reader.onloadend = () => {
                originalImage = reader.result;
                $('.' + fileId).remove();
                $('#' + fileId + '-image').remove();
                $("#" + fileId + "_preview").css("background-image", "url(" + base64 + ")");
                $('#' + fileId).after('<div class="' + fileId + ' preview-wrap" data-file-id="' + fileId + '">\n\
                  <input type="hidden" name="' + fileId + '" data-val="'+ originalImage +'" id="' + fileId + '_base_code" value="' + base64 + '" />\n\
                  <input type="hidden" name="' + fileId + '_X" value="' + dataX.value + '" />\n\
                  <input type="hidden" name="' + fileId + '_Y" value="' + dataY.value + '" />\n\
                  <input type="hidden" name="' + fileId + '_Height" value="' + dataHeight.value + '" />\n\
                  <input type="hidden" name="' + fileId + '_Width" value="' + dataWidth.value + '" />\n\
                  <input type="hidden" name="' + fileId + '_Rotate" value="' + dataRotate.value + '" />\n\
                  <input type="hidden" name="' + fileId + '_ScaleX" value="' + dataScaleX.value + '" />\n\
                  <input type="hidden" name="' + fileId + '_ScaleY" value="' + dataScaleY.value + '" />\n\
                  </div>');
                cropper.destroy();
                $('#cropper_modal').modal('hide');
            };
          }

          break;

        case 'destroy':
          cropper = null;

          if (uploadedImageURL) {
            URL.revokeObjectURL(uploadedImageURL);
            uploadedImageURL = '';
            image.src = originalImageURL;
          }

          break;
      }

      if (typeof result === 'object' && result !== cropper && input) {
        try {
          input.value = JSON.stringify(result);
        } catch (e) {
          console.log(e.message);
        }
      }
    }
  };

  document.body.onkeydown = function (event) {
    var e = event || window.event;

    if (e.target !== this || !cropper || this.scrollTop > 300) {
      return;
    }

    switch (e.keyCode) {
      case 37:
        e.preventDefault();
        cropper.move(-1, 0);
        break;

      case 38:
        e.preventDefault();
        cropper.move(0, -1);
        break;

      case 39:
        e.preventDefault();
        cropper.move(1, 0);
        break;

      case 40:
        e.preventDefault();
        cropper.move(0, 1);
        break;
    }
  };

  // Import image
  var inputImage = document.getElementById('inputImage');

  if (URL) {
    inputImage.onchange = function () {
      var files = this.files;
      var file;

      if (files && files.length) {
        file = files[0];
        let fileName = files[0].name
        let Newfile = new File([file], fileName, { type: "image/jpeg", lastModified: new Date().getTime() }, 'utf-8');
        let container = new DataTransfer();
        container.items.add(Newfile);
        let fileId = document.querySelector("#inputType").value;
        document.querySelector('#' + fileId).files = container.files;
        if (/^image\/\w+/.test(file.type)) {
          uploadedImageType = file.type;
          uploadedImageName = file.name;

          if (uploadedImageURL) {
            URL.revokeObjectURL(uploadedImageURL);
          }

          image.src = uploadedImageURL = URL.createObjectURL(file);

          if (cropper) {
            cropper.destroy();
          }
          
          options.aspectRatio = aspectRatio;

          cropper = new Cropper(image, options);
          inputImage.value = null;
        } else {
          window.alert('Please choose an image file.');
        }
      }
    };
  } else {
    inputImage.disabled = true;
    inputImage.parentNode.className += ' disabled';
  }
};

$('body').on('click', '#close-croper-modal', function(){
    cropper.destroy();
})