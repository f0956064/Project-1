$(document).ready(function(){
	if ($(".template_type").length) {
		var setTemplateForm = function () {
			var type = $('.template_type:checked').val();
			$("#row-template_content_sms").hide();
			$("#row-pdf_paper_size").hide();
			$("#row-pdf_paper_layout").hide();
			if (type == '1') {
				$("#row-subject").show();
				$("#subject").prop('required', true);
				$("#row-template_content").show();
				$("#row-template_content label").html('Email Body <span class="text-danger">*</span>');
				$("#row-subject label").html('Email Subject <span class="text-danger">*</span>');
			} else if (type == '2') {
				$("#row-subject").hide();
				$("#subject").removeAttr('required');
				$("#row-template_content").hide();
				$("#row-template_content_sms").show();
				$("#row-subject label").html('Message Header');
			} else {
				$("#subject").removeAttr('required');
				$("#row-template_content label").html('PDF Body <span class="text-danger">*</span>');
				$("#row-subject").hide();
				$("#row-template_content").show();
				$("#row-pdf_paper_size").show();
				$("#row-pdf_paper_layout").show();
			}
		};
		setTemplateForm();
		$("body").on("change", ".template_type", function () {
			setTemplateForm();
		});
	}
	var setMenuFormPermission = function(status) {
		if(typeof status == 'undefined') {
			$("#row-permission_list").hide();
			$("#row-permission_add").hide();
			$("#row-permission_edit").hide();
			$("#row-permission_delete").hide();
			$("#row-role_html").hide();
			$("#row-role_ids").hide();
		} else {
			$("#row-permission_list").show();
			$("#row-permission_add").show();
			$("#row-permission_edit").show();
			$("#row-permission_delete").show();
			$("#row-role_html").show();
			$("#row-role_ids").show();
		}
	};
	if($("#permission_required").length){
		var status = $("#permission_required:checked").val();
		setMenuFormPermission(status);

		$("body").on("change", "#permission_required", function(){
			var status = $("#permission_required:checked").val();
			setMenuFormPermission(status);
		});
	}

	if($("#group_menu-1").length){
		var setMenuFormGroup = function(status) {
			if(typeof status == 'undefined'){
				$("#row-class").show();
				$("#row-method").show();
				$("#row-query_params").show();
				$("#row-url").show();
				$("#row-url").val('');
				$("#permission_required").prop('checked', true);
				setMenuFormPermission(1);
			} else {
				$("#row-class").hide();
				$("#row-method").hide();
				$("#row-query_params").hide();
				$("#row-url").hide();
				$("#row-url").val('#');
				$("#permission_required").removeAttr('checked');
				setMenuFormPermission();
			}
		};

		var status = $("#group_menu-1:checked").val();
		setMenuFormGroup(status);
		$("body").on("change", "#group_menu-1", function(){
			var status = $("#group_menu-1:checked").val();
			setMenuFormGroup(status);
		});
	}

	if($("#accept_new-1").length){
		$("body").on("change", "#accept_new-1", function(){
			var type = $('#accept_new-1:checked').val();
			if(typeof type == 'undefined'){
				$("#row-primary_key").hide();
			} else {
				$("#row-primary_key").show();
			}
		});
	}

	if($("#setting-options").length) {
		var setSiteSettingOptionForm = function(type) {
			var optionRequired = ["5", "6", "7", "10"];
			if(optionRequired.indexOf(type) >= 0 ){
				$("#row-field_option").show();
			} else {
				$("#row-field_option").hide();
			}
		};

		var type = $("#field_type").val();
		setSiteSettingOptionForm(type);
		$("body").on("change", "#field_type", function(){
			var type = $(this).val();
			setSiteSettingOptionForm(type);
		});

		$("body").on("click", "#setting-option-add", function(){
			var key = $("#setting-option-key").val();
			var val = $("#setting-option-val").val();
			if(key && val){
				var optionArr = [];
				var option = $("#setting-options").val();
				if(option){
					optionArr = JSON.parse(option);
				}
				optionArr.push({'key': key, 'val': val});
				$("#setting-options").val(JSON.stringify(optionArr));
				$("#setting-option-key").val('');
				$("#setting-option-val").val('');
				$("#setting-option-key").focus();
			}
		});
	}


	if($("#options").length) {
		var setSiteSettingOptionForm = function(type) {
			var optionRequired = ["3", "4", "5"];
			if(optionRequired.indexOf(type) >= 0 ){
				$("#row-options").show();
			} else {
				$("#row-options").hide();
			}
		};

		var type = $("#field_type").val();
		setSiteSettingOptionForm(type);
		$("body").on("change", "#field_type", function(){
			var type = $(this).val();
			setSiteSettingOptionForm(type);
		});

		$("body").on("click", "#options-add", function(){
			var key = $("#options-key").val();
			var val = $("#options-val").val();
			if(key && val){
				var optionArr = [];
				var option = $("#options").val();
				if(option){
					optionArr = JSON.parse(option);
				}
				optionArr.push({'key': key, 'val': val});
				$("#options").val(JSON.stringify(optionArr));
				$("#options-key").val('');
				$("#options-val").val('');
				$("#options-key").focus();
			}
		});
	}

	if($("#query-params").length) {
		
		$("body").on("click", "#query-param-add", function(){
			var key = $("#query-param-key").val();
			var val = $("#query-param-val").val();
			if(key && val){
				var option = {};
				if($("#query-params").val()){
					option = JSON.parse($("#query-params").val());
				} 
				option[key] = val;
				
				$("#query-params").val(JSON.stringify(option));
				$("#query-param-key").val('');
				$("#query-param-val").val('');
				$("#query-param-key").focus();
			}
		});
	}

	if($("#answer-options").length) {		
		$("body").on("click", "#answer-options-add", function(){
			var key = $("#answer-options-key").val();
			var val = $("#answer-options-val").val();
			if(key && val){
				var optionArr = [];
				var option = $("#answer-options").val();
								
				if(option){
					optionArr = JSON.parse(option);
					//option = JSON.parse($("#answer-options").val());					
				} 
				optionArr.push({'key': key, 'val': val});
				//option[key] = val;								
				$("#answer-options").val(JSON.stringify(optionArr));
				$("#answer-options-key").val('');
				$("#answer-options-val").val('');
				$("#answer-options-key").focus();
			}
		});
	}
			

	if($(".status").length){
		if($('.status:checked').val() != 3)
			$("#row-reject_reason").hide();
			
		var setStatusForm = function(status){
			if(status == '3'){
				$("#row-reject_reason").show();
			} else {
				$("#row-reject_reason").hide();
			}
		};

		$("body").on("change", ".status", function(){
			var status = $('.status:checked').val();
			setStatusForm(status);
		});
	}

	$("body").on('click', ".delete-attachment-form", function(e) {
		e.preventDefault();
		var id = $(this).data('id');
		Swal.fire({
            title: "Are you Sure?",
            text: "Want to remove this file?",
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
				var html = '<input type="hidden" name="delete_files[]" value="'+ id +'"/>';
				$(this).parent().parent().append(html);
				$(this).parent().remove();
            }
          })
	});
});