    <script type="text/javascript">
	$(document).ready(function(){
	    $("table").tablesorter();
	    <?php if(count($list) > 10){ ?>
		$("table").tablesorterPager({container: $("#pager")});
	    <?php } ?>
	    $('.view').click(function(){
		user($(this).attr('user_id'));
	    });
	    $('.delete').click(function(){
		var confirmdeletion = confirm('Are you sure you want to delete this account?');
		if (confirmdeletion) {
		    $.get('<?php echo base_url()?>accounts/delete/'+$(this).attr('user_id'), function(){
			$.get('<?php echo base_url()?>accounts/lists/ajax', function(data){
			    $('#content-area').html(data);
			});
		    });
		}
	    });
	    $('#new-user').click(function(){
		user(0);
	    });
	});
	
    </script>
    <input id = "new-user" type = 'button' class = 'btn2' value = 'Create New User'>
	<table cellspacing="1" class="tablesorter">
	    <thead>
		    <tr>
			    <th>Last Name</th>
			    <th>First Name</th>
			    <th>Middle Name</th>
			    <th>Account</th>
			    <th>Sex</th>
			    <th>Email</th>
			    <th>Action</th>
    
		    </tr>
	    </thead>
	    <?php if(count($list) > 10){ ?>
		<tfoot>
			<tr>
				<th>Last Name</th>
				<th>First Name</th>
				<th>Middle Name</th>
				<th>Account</th>
				<th>Sex</th>
				<th>Email</th>
				<th>Action</th>
	
			</tr>
		</tfoot>
	    <?php } ?>
	    <tbody>
		<?php if(!empty($lists)){?>
		    <?php foreach($lists as $user){?>
			<tr>
				<td><?php echo $user->lname; ?></td>
				<td><?php echo $user->fname; ?></td>
				<td><?php echo $user->mname; ?></td>
				<td><?php echo $user->admin_flag; ?></td>
				<td><?php echo $user->sex; ?></td>
				<td><?php echo $user->email; ?></td>
				<td>
				    <img user_id = "<?php echo $user->user_id ?>" class = "view" src="<?php echo base_url()?>images/view16.png">
				    <img user_id = "<?php echo $user->user_id ?>" class = "delete" src="<?php echo base_url()?>images/delete16.png">
				</td>
			</tr>
		    <?php } ?>
		<?php } ?>
	    </tbody>
    </table>
<?php if(count($list) > 10){ ?>
    <div id="pager" class="pager">
	    <form>
		    <img src="<?php echo base_url() ?>css/tablesorter/first.png" class="first"/>
		    <img src="<?php echo base_url() ?>css/tablesorter/prev.png" class="prev"/>
		    <input type="text" style = "width: 50px; height: 18px; " class="pagedisplay"/>
		    <img src="<?php echo base_url() ?>css/tablesorter/next.png" class="next"/>
		    <img src="<?php echo base_url() ?>css/tablesorter/last.png" class="last"/>
		    <select class="pagesize">
			    <option selected="selected"  value="10">10</option>
			    <option value="20">20</option>
			    <option value="30">30</option>
			    <option  value="40">40</option>
		    </select>
	    </form>
    </div>
<?php } ?>