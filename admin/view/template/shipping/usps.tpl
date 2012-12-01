<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/shipping.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><span class="required">*</span> <?php echo $entry_user_id; ?></td>
            <td><input type="text" name="usps_user_id" value="<?php echo $usps_user_id; ?>" />
              <?php if ($error_user_id) { ?>
              <span class="error"><?php echo $error_user_id; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_postcode; ?></td>
            <td><input type="text" name="usps_postcode" value="<?php echo $usps_postcode; ?>" />
              <?php if ($error_postcode) { ?>
              <span class="error"><?php echo $error_postcode; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_domestic; ?></td>
            <td><div class="scrollbox">
                <?php $class = 'odd'; ?>
                <div class="even">
                  <?php if ($usps_domestic_00) { ?>
                  <input type="checkbox" name="usps_domestic_00" value="1" checked="checked" />
                  <?php echo $text_domestic_00; ?>
                  <?php } else { ?>
                  <input type="checkbox" name="usps_domestic_00" value="1" />
                  <?php echo $text_domestic_00; ?>
                  <?php } ?>
                </div>
                <div class="even">
                  <?php if ($usps_domestic_01) { ?>
                  <input type="checkbox" name="usps_domestic_01" value="1" checked="checked" />
                  <?php echo $text_domestic_01; ?>
                  <?php } else { ?>
                  <input type="checkbox" name="usps_domestic_01" value="1" />
                  <?php echo $text_domestic_01; ?>
                  <?php } ?>
                </div>
                <div class="even">
                  <?php if ($usps_domestic_02) { ?>
                  <input 