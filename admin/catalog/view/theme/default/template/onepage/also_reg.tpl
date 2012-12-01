  <h3><input type="checkbox" id="regChkbox" checked="checked"/> <?php echo $entry_also_register;?></h3>
  <br/><br/>
  <div id="oppassword" style="border: 1px solid #ccc;padding:5px;">
	  <span class="required">*</span> <?php echo $entry_password; ?><br />
	  <input type="password" name="password" value="" class="large-field" />
	  <br />
	  <br />
	  <span class="required">*</span> <?php echo $entry_confirm; ?> <br />
	  <input type="password" name="confirm" value="" class="large-field" />
	  <br />
	  <br />
	<input type="checkbox" name="newsletter" value="1" id="newsletter" checked="checked"/>
  	<label for="newsletter"><?php echo $entry_newsletter; ?></label>
  </div>