<script>
    $(document).ready(function(){
	$("#frm-reset-btn-submit").click(function(){
	    $.post('<?php echo base_url()?>control/resetpassword/<?php echo $data->token?>', $('#frm-reset').serialize(), function(data){
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
	
	$("#frm-reset-btn-back").click(function(){
	    $.get('<?php echo base_url(); ?>control/loginform', function(data){
		$('#content-area').html(data);
	    });
	});
    });
		
</script>
<h1>Reset Password</h1>
<form id = 'frm-reset'>
    <ul>
        <li>
            <label>Password</label>
	    <input type="Password" name = "password" >
	</li>
        <li>
            <label>Re-type Password</label>
	    <input type="password" >
	</li>
        <li>
            <input id = "frm-reset-btn-submit" type="button" value="Reset Password" class="btn2">
	    <input id = "frm-reset-btn-back" type="button" value="Back to Login" class="btn2">
        </li>
    </ul>
</form>