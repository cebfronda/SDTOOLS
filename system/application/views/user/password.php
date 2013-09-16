<script type="text/javascript">
    $(document).ready(function(){
        $('#frm-login-btn-pwdsave').click(function(){
            $.post('<?php echo base_url(); ?>accounts/password/<?php echo $userid ?>', $('#frm-change-password').serialize(), function(data){
                alert('Password Saved.');
                $( "#dialog" ).dialog( "close" );
            });
        });
    });
</script>
                        <form id = "frm-change-password" style = 'width: 270px;'>
			    <input style = 'width: 130px' type = 'password' name = 'password'>
			    <input id = "frm-login-btn-pwdsave" style = 'width: 70px' type="button" value="save" class="btn2">
			</form>