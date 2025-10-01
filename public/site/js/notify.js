 
 
$(document).ready(function () {
 
	
$('#btn-send-notify').on('click', function (e) {
	e.preventDefault();
 
	sendnotformbyid('#send-notify-form','msg');
	});

	$('#btn-send-withtoken').on('click', function (e) {
		e.preventDefault();
	 
		sendnotformbyid('#send-withtoken-form','msgtoken');
		});

	function sendnotformbyid(formid,messageid) {
	//	startLoading();
	//	ClearErrors();
	//	$formid='#create_form';
		 
		var form = $(formid)[0];
		var formData = new FormData(form);
		urlval = $(formid).attr("action");
	 
	
		$.ajax({
			url: urlval,
			type: "POST",
	
			data: formData,
			contentType: false,
			processData: false,
			//contentType: 'application/json',
			success: function (data) {
				 
				//endLoading();
				$('#'+messageid).html(data);
			//var msg=	$.getJSON(  data );
	
				// $('.alert').html(result.success);
			}, error: function (errorresult) {
			//	endLoading();
				//var response = $.parseJSON(errorresult.responseText);
				// $('#errormsg').html( errorresult );
				//noteError(); 
				$('#msg').html( response );
			}, finally: function () {
				//endLoading();
	
			}
		 
	
		});
	
	
	 
	}
	});
	 
	 

 


 

///////////////////////////////////////////////////////

 
 
function noteSuccess() {
	notif({
		msg: "تمت العملية بنجاح",
		type: "success"
	});
}
function noteError() {
	notif({
		msg: "لم تنجح العملية !",
		position: "right",
		type: "error",
		bottom: '10'
	});
}
 
 
function resetexpertForm() {
	jQuery('#expert_form')[0].reset();


	/*
	$('#imgshow').attr("src", emptyimg);
	$('#iconshow').attr("src", emptyimg);
	*/
}
 

 
 
