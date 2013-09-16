<script>
    $(document).ready(function(){
	$("#frm-login-btn-submit").click(function(){
	    $.post('<?php echo base_url()?>control/login', $('#frm-login').serialize(), function(data){
		if (data.indexOf('success') > 0) {
		    alert(data);
		    $.get('<?php echo base_url()?>accounts/profile/account', function(data) {
			$('#content-area').html(data);
			$.get('<?php echo base_url()?>control/headers', function(data) {
			    $('#navigation').html(data);
			});    
		    });				
		}else{
		    alert(data);
		}
	    });
	});
	
	$("#frm-login-btn-forgotpassword").click(function(){
	    $.get('<?php echo base_url(); ?>control/forgot_password', function(data){
		$('#content-area').html(data);
	    });
	});
    });
		
</script>
<h1>Log In</h1>
<form id = 'frm-login'>
    <ul>
        <li>
            <label>Username/Email Address</label>
	    <input type="text" name = "username" value="Username/Email Address" onBlur="javascript:if(this.value==''){this.value=this.defaultValue;}" onFocus="javascript:if(this.value==this.defaultValue){this.value='';}">
	</li>
        <li>
            <label>Password</label>
	    <input type="password" name = "password" value="Password" onBlur="javascript:if(this.value==''){this.value=this.defaultValue;}" onFocus="javascript:if(this.value==this.defaultValue){this.value='';}">
	</li>
        <li>
            <input id = "frm-login-btn-submit" type="button" value="Log In" class="btn2">
	    <input id = "frm-login-btn-forgotpassword" type="button" value="Forgot Password" class="btn2">
        </li>
    </ul>
</form>