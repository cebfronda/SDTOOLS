<!DOCTYPE html>
<!-- Website template by freewebsitetemplates.com -->
<html>
<head>
	<meta charset="UTF-8">
	<title>Zvelo::SD Tools::WAQA</title>
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css" type="text/css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/dot-luv/jquery-ui-1.10.3.custom.css" type="text/css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/dot-luv/jquery-ui-1.10.3.custom.min.css" type="text/css">
	<script type='text/javascript' src='<?php echo base_url(); ?>javascripts/js/jquery-1.8.3.js'></script>
	<script type='text/javascript' src='<?php echo base_url(); ?>javascripts/js/jquery-ui-1.9.2.custom.js'></script>
	<script type='text/javascript' src='<?php echo base_url(); ?>javascripts/js/jquery-ui-1.9.2.custom.min.js'></script>
	<script type='text/javascript' src='<?php echo base_url(); ?>javascripts/general.js'></script>
	<!-- <link rel="stylesheet" href="<?php echo base_url(); ?>css/tablesorter/jq.css" type="text/css" media="print, projection, screen" /> -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/tablesorter/themes/blue/style.css" type="text/css" media="print, projection, screen" />    
	<script type="text/javascript" src="<?php echo base_url(); ?>javascripts/tablesorter/jquery.tablesorter.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>javascripts/tablesorter/pager/jquery.tablesorter.pager.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>javascripts/tablesorter/jquery.tablesorter.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			
		});
		
	</script>
</head>
<body>
	<?php $this->load->view('template/header');?>
	<div id="contents">
		<div id = 'content-area' class="wrapper clearfix">
			<?php $this->load->view($page_view);?>	
		</div>
	</div>
	<?php echo $this->load->view('template/footer');?>
</body>
</html>