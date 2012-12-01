<?php echo $header; ?>
<div id="home"><div class="container">
<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
   <h1><?php echo $heading_title; ?></h1>

	<style>
		#onepage h2 {
			color: #FFAB35;
		    font-size: 23px;
		    margin-bottom: 5px;
		    margin-top: 5px;
		}
		#onepage #login {
			background-color: #EBF4FA;
			padding: 10px;
		}

	</style>
	<div class="warning" style="display:none;"><?php if ($error_warning) {echo $error_warning;} ?></div>
   <?php include "$opc_layout";?>
<script type="text/javascript"><!--
$(document).ready(function() {

	$('#onepage').opc({
		warningDom: 'div.warning',
		errDom: 'span.error',
		errClass: 'error',

		logged: <?php echo $logged+0;?>,
		shippingReq: <?php echo $shipping_required+0; ?>,
		isAgree: <?php echo $require_agree+0; ?>,
		errAgree: '<?php echo $error_agree; ?>',

		oneStep: <?php echo $one_step+0; ?>,
		entryRet: '<?php echo $entry_return; ?>',
		oneAddress: <?php echo $one_address + 0; ?>
	}).opc('loadAll');
});
//--></script>

</div>
</div></div>

<?php echo $content_bottom; ?>
<?php echo $footer; ?>
