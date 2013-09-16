				<?php if($_SESSION[SESSION_NAME]){?>
					<li <?php echo ($home)? ' class="selected"' : ''; ?>>
                                                <a href="<?php echo base_url();?>">Home</a>
                                        </li>
					<li <?php echo ($users)? ' class="selected"' : ''; ?>>
                                                <a href="<?php echo base_url();?>accounts/lists">Users</a>
                                        </li>
                                        <li>
						<a href="<?php echo base_url()."control/logout"?>">Log Out</a>
					</li>
				<?php }else{ ?>
					<li <?php echo ($login)? ' class="selected"' : ''; ?>>
						<a href="javascript: void();">Log In</a>
					</li>
				<?php } ?>
				<li <?php echo ($contact)? ' class="selected"' : ''; ?>>
					<a href="contact.html">Contact Us</a>
				</li>