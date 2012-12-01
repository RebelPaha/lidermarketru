<?php echo $header; ?>
  <!-- ---------------------- -->
  <!--     I N T R O          -->
  <!-- ---------------------- -->
<div id="intro">
    <?php if ($this->document->shoppica_intro_banner): echo $this->document->shoppica_intro_banner; else: ?>

    <div id="intro_wrap">
      <div class="container_12">
        <div id="breadcrumbs" class="grid_12">
          <?php foreach ($breadcrumbs as $breadcrumb): ?>
          <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
          <?php endforeach; ?>
        </div>
        <h1><?php echo $heading_title; ?></h1>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- end of intro -->
  <!-- ---------------------- -->
  <!--      C O N T E N T     -->
  <!-- ---------------------- -->
  <?php if (!$this->document->shoppica_rightColumnEmpty && $this->config->get('shoppica_products_listing') == 'size_1') { $container = 12; $main = 9; $side_col = 3; } ?>
  <?php if ($this->document->shoppica_rightColumnEmpty && $this->config->get('shoppica_products_listing') == 'size_1') { $container = 12; $main = 12; $side_col = 3; } ?>
  <?php if (!$this->document->shoppica_rightColumnEmpty && $this->config->get('shoppica_products_listing') == 'size_2') { $container = 16; $main = 12; $side_col = 4; } ?>
  <?php if ($this->document->shoppica_rightColumnEmpty && $this->config->get('shoppica_products_listing') == 'size_2') { $container = 12; $main = 12; $side_col = 3; } ?>

  <div id="content" class="container_<?php echo $container; ?>">

    <?php if ($this->document->shoppica_column_position == "left" && $column_right): ?>
    <div id="left_col" class="grid_<?php echo $side_col; ?>">
    <?php echo $column_right; ?>
    </div>
    <?php endif; ?>

    <div class="checkout grid_<?php echo $main; ?>">

  <!--<div id="content" class="container_12">
  <div id="content" class="container_<?php echo $container; ?>">-->

	<style>
		#onepage a.s_button_remove, #onepage a.s_button_1 {
			display:none;
		}
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
	<div class="s_server_msg s_msg_red" style="display:none;"><?php if ($error_warning) {echo $error_warning;} ?></div>
	<?php include $opc_layout;?>
<script type="text/javascript"><!--
$(document).ready(function() {

	$('#onepage').opc({
		warningDom: 'div.s_server_msg',
		errDom: '.s_error_msg',
		errClass: 's_error_msg',

		logged: <?php echo $logged+0;?>,
		shippingReq: <?php echo $shipping_required+0; ?>,
		isAgree: <?php echo $require_agree; ?>,
		errAgree: '<?php echo $error_agree; ?>',

		oneStep: <?php echo $one_step+0; ?>,
		entryRet: '<?php echo $entry_return; ?>',
		oneAddress: <?php echo $one_address + 0; ?>
	}).opc('loadAll');
});
//--></script>


    </div>
  <!-- end of content -->
  </div>
  <script type="text/javascript">
  function highlightErrors(elements, json, wrapper_id) {
    jQuery.each(elements, function(i, val) {
      if (json[i]) {
        var selector = "input";
        var el_name = val ? val : i;

        if (val) {
          var el_parts = val.split(":");
          if (el_parts.length == 2) {
            var selector = el_parts[0];
            var el_name = el_parts[1];
          }
        }

        var element = $("#" + wrapper_id + " " + selector + "[name='" + el_name + "']");

        element.after('<p class="s_error_msg">' + json[i] + '</p>');
        element.parent("div.s_row_2").addClass("s_error_row");
      }
    });
  }

  </script>

  <link rel="stylesheet" type="text/css" href="catalog/view/theme/<?php echo $this->config->get('config_template') ?>/stylesheet/prettyPhoto.css" media="all" />
  <script type="text/javascript" src="catalog/view/theme/<?php echo $this->config->get('config_template') ?>/js/jquery/jquery.prettyPhoto.js"></script>
  <script type="text/javascript" src="http<?php if(isHTTPS()) echo 's'?>://ajax.aspnetcdn.com/ajax/jquery.validate/1.8.1/jquery.validate.min.js"></script>


  <script type="text/javascript">
    jQuery.validator.setDefaults({
        errorElement: "p",
        errorClass: "s_error_msg",
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        highlight: function(element, errorClass, validClass) {
            $(element).addClass("error_element").removeClass(validClass);
            $(element).parent("div").addClass("s_error_row");
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass("error_element").addClass(validClass);
            $(element).parent("div").removeClass("s_error_row");
        }
    });
  </script>
<?php echo $footer; ?>