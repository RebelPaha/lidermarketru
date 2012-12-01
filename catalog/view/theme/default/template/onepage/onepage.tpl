
<style>
  <?php if($one_address)  echo "#payment-address label[for='shipping'], #payment-address input#shipping[type='checkbox']{display:none;}" ; ?>
  <?php if($fixed_loading)  echo ".loading-indicator {position: fixed !important; top:38% !important;}" ;?>
</style>

<div id="onepage">
  <div id="pConf" style="display:none;"></div>
  <div id="pOP" class="two-col" style="display:none;">
  	<div class="left">
	  <div id="oplogin"></div>
		<h2><?php echo $text_checkout_payment_address;?></h2>
		<div id="payment-address"></div>

		<div id="sAddCont">
		  <hr/>
		  <h2><?php echo $text_checkout_shipping_address;?></h2>
		  <div id="shipping-address" ></div>
		</div>
	</div>
	<div class="right">
      <?php
		if(!$logged) {
	  ?>
	      <div style="clear:both;"></div>
		  <?php include 'also_reg.tpl';?>

	  <?php
	    }
	  ?>
		<div id="opc">
		  <h2><?php echo $text_checkout_summary;?></h2>
		  <div id="opcb">
		  </div>
		  <br/>
		  <?php if ($is_coupon) {?>
		  <div id="opCoupon">
			  <?php echo $entry_coupon; ?>&nbsp;
		      <input type="text" name="coupon" value="" />
		      &nbsp;<br/><a id="button-coupon" class="button"><span><?php echo $button_coupon; ?></span></a>
		  </div>
		  <?php } if ($is_voucher) {?>
		  <br/>
		  <div id="opVoucher">
		      <?php echo $entry_voucher; ?>&nbsp;
    		  <input type="text" name="voucher" value="" />
    		  &nbsp;<br/><a id="button-voucher" class="button"><span><?php echo $button_voucher; ?></span></a>
    	  </div>
    	  <?php } if ($is_reward) {?>
    	  <br/>
		  <div id="opReward">
		      <?php echo $entry_reward; ?>&nbsp;
    		  <input type="text" name="reward" value="" />
    		  &nbsp;<br/><a id="button-reward" class="button"><span><?php echo $button_reward; ?></span></a>
    	  </div>
    	  <?php }?>
		</div>
		<br/><hr>

		<h2><?php echo $text_checkout_shipping_method;?></h2>
		<div id="opdeliv"></div>
	</div>
	<div style="clear:both;"></div>
	<hr/>
	<div style="position:relative;">
	  <div style="display:table;width:100%;">
		<div style="width:50%;display:table-cell;">
			<h2><?php echo $text_checkout_payment_method; ?></h2>
			<div id="oppymnt"></div>

		</div>
		<div style="width:49%;display:table-cell;">
			<div id="oppymntInfo" ></div>
			<div id="opcnfrm" style="bottom:0px;">
				<div><a  id="opcnfrmbtn" class="button-big" ><span><?php echo $text_checkout_confirm;?></span></a></div>
			</div>
		</div>
	  </div>
	</div>
  </div>
</div>