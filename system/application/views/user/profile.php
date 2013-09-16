<?php
    $header_label = (($user_id == 'account')? 'Account Settings' : 'User Account Details' );
?>
<script type="text/javascript">
    $(document).ready(function(){
	$( "#profle-bday" ).datepicker({
		showOn: "button",
		buttonImage: "<?php echo base_url(); ?>images/calendar.jpg",
		buttonImageOnly: true,
		dateFormat: "MM dd, yy"
	});
	$('.ui-datepicker-trigger').attr('style', 'position:absolute');
	$('#frm-login-btn-cancel').click(function(){
	    var confirmcancel = confirm('Are you sure you want to leave this page?');
	    if (confirmcancel) {
		home();
	    }
	});
	
	$('#frm-login-btn-submit').click(function(){
	    var confirmcancel = confirm('Are you sure you want to save?');
	    if (confirmcancel) {
		$.post('<?php echo base_url()?>accounts/save/<?php echo $user_id; ?>', $('#form-profile').serialize(), function(data){
		    <?php if($user_id == "account"){?>
			alert(data);
		    <?php }else{ ?>
			home();
		    <?php } ?>   
		});
	    }
	});
	
	$( "#dialog" ).dialog({
	    autoOpen: false,
	    show: "blind",
	    hide: "explode"
	});

	$( "#frm-login-btn-edit-password" ).click(function() {
	    $.get('<?php echo base_url()?>accounts/password/<?php echo $user_id;?>', function(data){$('#dialog').html(data);})
	    $( "#dialog" ).dialog( "open" );
	    return false;
	});
    });
    
    function home() {
	$.get('<?php echo base_url()?>accounts/lists/ajax', function(data){
	    $('#content-area').html(data);
	});
    }
</script>
                   <div id = "frm-profile">
                        <div id="sidebar">
				<ul>
					<li>
						<img src="<?php echo base_url().DEFAULT_PROFILE_PIC?>" alt="Img" height="154" width="213">
						<input id = "frm-login-btn-change-profile-pic" type="button" value="Change Profile Pic" class="btn2">
						<?php if($user_id > 0 || $user_id == "account"){?>
						    <input id = "frm-login-btn-edit-password" type="button" value="Edit Password" class="btn2">
						<?php }?>
					</li>
				</ul>
			</div>
			<div class="main">
				<h1><?php echo $header_label; ?></h1>
                                <form id = 'form-profile'>
                                    <ul>
                                        <li style = "width:90%">
                                            <label>Username</label>
                                            <input style = "width:350px;" type="text" name = "username" value="<?php echo $user->username; ?>">
                                        </li>
					<?php if($user_id == 0 && $user_id != "account"){?>
					    <li style = "width:90%">
						<label>Password</label>
						<input style = "width:350px;" type="password" name = "password" value="<?php echo $user->password; ?>">
					    </li>
					<?php } ?>
                                        <li style = "width:90%">
                                            <label>First Name</label>
                                            <input style = "width:350px;" type="text" name = "fname" value="<?php echo $user->fname; ?>">
                                        </li>
                                        <li style = "width:90%">
                                            <label>Last Name</label>
                                            <input style = "width:350px;" type="text" name = "lname" value="<?php echo $user->lname; ?>">
                                        </li>
					<li style = "width:90%">
                                            <label>Middle Name</label>
                                            <input style = "width:350px;" type="text" name = "mname" value="<?php echo $user->mname; ?>">
                                        </li>
					<li style = "width:90%">
                                            <label>Birthday</label>
                                            <input style = "width:290px;" type="text" readonly = "readonly" id = "profle-bday" name = "birthday" value="<?php echo (empty($user->birthday)? "": date("F d, Y", strtotime($user->birthday)) ); ?>">
                                        </li>
					<li style = "width:90%;">
                                            <label>Sex</label>
					    <input type = "radio" value = "f" name = "sex" <?php echo ($user->sex == "f" ? "checked = 'checked'": "" ); ?>> Female
					    <input type = "radio" value = "m" name = "sex" <?php echo ($user->sex == "m" ? "checked = 'checked'": "" ); ?>> Male
                                        </li>
					<li style = "width:90%">
                                            <label>E-mail Address</label>
                                            <input style = "width:350px;" type="text" name = "email" value="<?php echo $user->email; ?>">
                                        </li>
					<li style = "width:90%">
                                            <label>Address</label>
                                            <textarea style = "width:360px;height:100px" type="text" name = "address"><?php echo $user->address; ?></textarea>
                                        </li>
					<li style = "width:90%">
                                            <label>Phone Number</label>
                                            <input style = "width:350px;" type="text" name = "phone_number" value="<?php echo $user->phone_number; ?>">
                                        </li>
					<li style = "width:90%;">
                                            <label>Account Type</label>
					    <input type = "radio" value = "0" name = "admin_flag" <?php echo ($user->admin_flag != 1 ? "checked = 'checked'": "" ); ?>> Non-Admin
					    <input type = "radio" value = "1" name = "admin_flag" <?php echo ($user->admin_flag == 1 ? "checked = 'checked'": "" ); ?>> Admin
                                        </li>
                                        <li>
                                            <input id = "frm-login-btn-submit" type="button" value="Save" class="btn2">
					    <?php if($user_id != "account"){ ?>
						<input id = "frm-login-btn-cancel" type="button" value="Cancel" class="btn2">
					    <?php }?>
                                        </li>
                                    </ul>
                                </from>
			</div>
                    </div>
		   
		   <div id="dialog" title="Change Password"></div>