<script>
    $(document).ready(function(){
	$("#frm-login-btn-reset").click(function(){
	    $.post('<?php echo base_url()?>control/forgot_password', $('#frm-forgotpwd').serialize(), function(data){
		if (data.indexOf('success') > 0) {
		    alert(data);
		    $.get('<?php echo base_url()?>control/loginform', function(data) {
			$('#content-area').html(data);   
		    });				
		}else{
		    alert(data);
		}
	    });
	});
	$("#frm-login-btn-cancel").click(function(){
	    $.get('<?php echo base_url(); ?>control/loginform', function(data){
		$('#content-area').html(data);
	    });
	});
    });
		
</script>
<h1>Forgot Password</h1>
<form id = 'frm-forgotpwd'>
    <ul>
        <li>
            <label>Username/Email Address</label>
	    <input type="text" name = "username" value="Username/Email Address" onBlur="javascript:if(this.value==''){this.value=this.defaultValue;}" onFocus="javascript:if(this.value==this.defaultValue){this.value='';}">
	</li>
        <li>
            <input id = "frm-login-btn-reset" type="button" value="Reset Password" class="btn2">
	    <input id = "frm-login-btn-cancel" type="button" value="Cancel" class="btn2">
        </li>
    </ul>
</form>