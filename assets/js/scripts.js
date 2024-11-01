	
$(".wc_sub_btn").on("click",function(){
	event.preventDefault();
	var subc_email = $(".sub_wc_email").val();
	
	if(!validateEmail(subc_email)){
		
		$(".sub_wc_email").css("border","1px solid red");
		$(".wc_sub_error_message").html("E-mail is not valid");
		
	}else{
		$(".sub_wc_email").css("border","1px solid green");
		$(".wc_sub_error_message").html(" ");
		
		$.ajax({
			 type : "post",				 
			 url : myAjax.ajaxurl,				 
			 data : {action: "wpwes_email_subcribe_function",subc_email:subc_email },
			 success: function(response) {
				 
				$(".wc_subc_success").html(response);
				  window.location.reload(true);			 
		  } 
		});
	}
		
});
	


function validateEmail(sEmail) {
    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if (filter.test(sEmail)) {
        return true;
    }
    else {
        return false;
    }
}