$(document).ready(function(){
    $("body").on("click", ".show-panel", function(e){
        e.preventDefault();
        var id = $(this).data('id');
        $("#" + id).slideToggle();
    });
    var showModal = function(url, modalSize) {
        $.ajax({
            url: url,
            type: "GET",
            success: function (msg) {
                if (msg == 'err') {
                    Swal.fire({
                        title: 'Some problem occured, please try again.',
                        timer: 6000
                    });
                } else {
                    if(typeof modalSize != "undefined"){
                        $("#myModal .modal-dialog").removeClass('modal-md');
                        $("#myModal .modal-dialog").removeClass('modal-lg');
                        $("#myModal .modal-dialog").removeClass('modal-sm');
                        $("#myModal .modal-dialog").addClass(modalSize);
                    }
                    $("#myModal .modal-content").html(msg)
                    $("#myModal").modal('show');
                }
            }
        });
    }

    $("body").on('click', ".show-modal-sm", function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        showModal(url, 'modal-sm');
    });

    $("body").on('click', ".show-modal", function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        showModal(url, 'modal-md');
    });

    $("body").on('click', ".show-modal-lg", function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        showModal(url, 'modal-lg');
    });

    $("body").on('click', ".show-modal-xl", function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        showModal(url, 'modal-xl');
    });

    // 
    // confirmation box for anchor tag
    // <a href="url" class="btn-confirmation" data-confirm-text="Are you sure?|Are you sure want to proceed?">Click here</a>
    // 
    $('body').on('click', '.btn-confirmation', function(e){
        e.preventDefault();
        var me = $(this),
            me_data = me.data('confirm-text');

        var url = $(this).attr('href');
        me_data = me_data ? me_data.split("|") : [];
        Swal.fire({
            title: (0 in me_data) ? me_data[0] : 'Are you sure want to proceed?',
            text: (1 in me_data) ? me_data[1] : 'If you proceed, the operation might be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Sure!',
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            }
        }).then((result) => {
            if (result.isConfirmed) {
               window.location.href = url;
            }
        })
    });

    
    $("body").on('change', '.parent-menu', function(){
        let val = $(this).val();
        $.ajax({
            type: 'GET',
            url: PATH + '/menus/' + val + '/children',
            success: function(res) {
                if(res.status == 200) {
                    let html = '<div class="col-lg-3">\n\
                        <select class="form-control parent-menu" name="menu_id[]">\n\
                            <option value="">Select menu</option>';

                    $(res.data).each(function(key, val){
                        html = html + '<option value="'+ val.id +'">'+ val.menu +'</option>';
                    });

                    html = html + '</select></div>';

                    $("#menu-dropdowns").append(html);
                }
            } 
        });
    });

    $('body').on('click', '.btn-delete-confirmation', function(e){
        e.preventDefault();
        let formId = $(this).data('form-id');
        Swal.fire({
            title: "Are you Sure?",
            text: "You want to delete this records?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $("#" + formId).submit();
            }
        })
    });

    $('body').on('change', '.assign_permission_id', function(){
        let permissionIds = '';
        $(".assign_permission_id:checked").each(function(key, val){
            if(permissionIds.length) {
                permissionIds = permissionIds + ',' + $(this).val();
            } else {
                permissionIds = $(this).val();
            }
        });
        $("#permission_ids").val(permissionIds);
    });


    // 
    // confirmation box to call another function
    // <a href="url" data-confirm="Are you sure?|Are you sure want to proceed?" data-confirm-yes="your_function_name">Click here</a>
    // 
    $('body').on('click', '[data-confirm]', function() {
        var me = $(this),
            me_data = me.data('confirm');

        me_data = me_data.split("|");
        Swal.fire({
            title: me_data[0],
            text: me_data[1],
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Sure!',
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            }
          }).then((result) => {
            if (result.isConfirmed) {
                eval(me.data('confirm-yes'));
            }
          })
    });

    $('body').on('click', '[data-confirm-reject]', function() {
        var me = $(this),
            me_data = me.data('confirm-reject');

        me_data = me_data.split("|");
        Swal.fire({
            title: me_data[0],
            text: me_data[1],
            icon: 'warning',
            input: 'textarea',
            inputLabel: 'Please specify your reason for rejection.',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Sure!',
            inputValidator: (value) => {
              if (!value) {
                return 'Please Enter Reject Reason'
              }
            },
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            }
          }).then((result) => {
            if (result.isConfirmed) {
                $('#reject_reason').val(result.value);
                eval(me.data('confirm-yes'));
            }
          })
    });

    $("body").on("click", ".ajax-pagination a.page-link", function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        showModal(url);
    });
});

function showNotification(type, text, placementFrom, placementAlign, animateEnter, animateExit) {
    if (type === null || type === '') { type = 'success'; }
    if (text === null || text === '') { text = 'Turning standard Bootstrap alerts'; }
    if (animateEnter === null || animateEnter === '') { animateEnter = 'animated fadeInDown'; }
    if (animateExit === null || animateExit === '') { animateExit = 'animated fadeOutUp'; }
    var allowDismiss = true;

    toastr[type](text)

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "showDuration": 300,
        "hideDuration": 1000,
        "timeOut": 5000,
        "extendedTimeOut": 1000,
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
}
function animateZoomIn() {
    $(".animate").each(function(index){
        var div = $(this);
        setTimeout( function() {
            div.addClass('animate__animated animate__zoomIn').show().removeClass('animate');
        }, 200 * index);
    });
}
animateZoomIn();