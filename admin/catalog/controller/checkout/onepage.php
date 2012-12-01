<?php

class ControllerCheckoutOnePage extends Controller {
	public function index() {
		if ((!$this->cart->hasProducts() && (!isset($this->session->data['vouchers']) || !$this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
	  		$this->redirect($this->url->link('checkout/cart'));
    	}

    	// Check if guest checkout is disabled
    	if (!$this->customer->isLogged()) {
	    	if (!$this->config->get('config_guest_checkout') || $this->cart->hasDownload()) {
				$this->redirect($this->url->link('account/login', '', 'SSL'));
			}
    	}

    	// Check if terms
    	$this->language->load('account/register');
    	$this->data['require_agree'] = false;
    	$this->data['error_agree'] = '';
    	if ($this->config->get('config_checkout_id')) {
			$this->load->model('catalog/information');
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));
	    	if ($information_info) {
	    		$this->data['require_agree'] = true;
	    		$this->data['error_agree'] = sprintf($this->language->get('error_agree'), $information_info['title']);
	    	}
	    }


		// Check if requires approval



		require_once('catalog/controller/checkout/onepage_settings.php');
		if(!isset($this->session->data['guest']['shipping']['country_id']) || !isset($this->session->data['guest']['shipping']['zone_id'])) {
			$this->updateShippingZone($onepage_settings['shipping_country'], $onepage_settings['shipping_zone']);
		}
		if(!isset($this->session->data['guest']['payment']['zone_id']) || !isset($this->session->data['guest']['payment']['country_id'])) {
 			$this->session->data['guest']['payment']['zone_id'] = $onepage_settings['payment_zone'];
 			$this->session->data['guest']['payment']['country_id'] = $onepage_settings['payment_country'];
		}


		$this->document->addScript('catalog/view/javascript/onepage/opc-min.js');
		$this->document->addStyle('catalog/view/javascript/onepage/style-min.css');
		$this->language->load('total/coupon');
		$this->language->load('total/voucher');
		$this->language->load('total/reward');
		$this->language->load('checkout/checkout');


		$this->document->setTitle($this->language->get('heading_title'));
		$this->data['logged'] = $this->customer->isLogged();

    	$this->data['breadcrumbs'] = array();
      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
        	'separator' => false
      	);

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_cart'),
			'href'      => $this->url->link('checkout/cart'),
        	'separator' => $this->language->get('text_separator')
      	);

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('checkout/onepage', '', 'SSL'),
        	'separator' => $this->language->get('text_separator')
      	);


		$this->data['error_warning'] = '';

		// Minimum quantity validation
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($this->session->data['cart'] as $key => $quantity) {
				$product_2 = explode(':', $key);

				if ($product_2[0] == $product['product_id']) {
					$product_total += $quantity;
				}
			}

			if ($product['minimum'] > $product_total) {
				$this->session->data['error'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);

				$this->redirect($this->url->link('checkout/cart'));
			}
		}


	    $this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_checkout_option'] = sprintf($this->language->get('text_checkout_option'));
		$this->data['text_checkout_account'] = $this->language->get('text_checkout_account');

		$this->data['text_checkout_payment_address'] = $onepage_settings['text_checkout_payment_address'][$this->session->data['language']];
		$this->data['text_checkout_shipping_address'] = $onepage_settings['text_checkout_shipping_address'][$this->session->data['language']];
		$this->data['text_checkout_shipping_method'] = $onepage_settings['text_checkout_shipping_method'][$this->session->data['language']];
		$this->data['text_checkout_payment_method'] = $onepage_settings['text_checkout_payment_method'][$this->session->data['language']];
		$this->data['text_checkout_confirm'] = $onepage_settings['text_checkout_confirm'][$this->session->data['language']];
		$this->data['text_checkout_summary']          = $onepage_settings['text_checkout_summary'][$this->session->data['language']];

		$this->data['is_reward'] = $onepage_settings['is_reward'];
		$this->data['is_voucher'] = $onepage_settings['is_voucher'];
		$this->data['is_coupon'] = $onepage_settings['is_coupon'];

		$this->data['text_modify'] = $this->language->get('text_modify');


		$this->data['entry_newsletter'] = sprintf($this->language->get('entry_newsletter'), $this->config->get('config_name'));
		$this->data['entry_password'] = $this->language->get('entry_password');
		$this->data['entry_confirm'] = $this->language->get('entry_confirm');

		$this->data['entry_coupon'] = $this->language->get('entry_coupon');
		$this->data['button_coupon'] = $this->language->get('button_coupon');
		$this->data['entry_voucher'] = $this->language->get('entry_voucher');
		$this->data['button_voucher'] = $this->language->get('button_voucher');


		/*if ($this->data['is_reward'])
				$points = $this->customer->getRewardPoints();

		$points_total = 0;

		foreach ($this->cart->getProducts() as $product) {
			if ($product['points']) {
				$points_total += $product['points'];
			}
		}

		if ($points && $points_total && $this->config->get('reward_status')) {
			$this->language->load('total/reward');

			$this->data['heading_title'] = sprintf($this->language->get('heading_title'), $points);

			$this->data['entry_reward'] = sprintf($this->language->get('entry_reward'), $points_total);*/

		$this->data['entry_reward'] = $this->language->get('entry_reward');
		$this->data['button_reward'] = $this->language->get('button_reward');


		$this->data['entry_also_register'] = $onepage_settings['entry_also_register'][$this->session->data['language']];
		$this->data['entry_return'] = $onepage_settings['entry_return'][$this->session->data['language']];


		$this->data['one_address'] = $onepage_settings['one_address'];
		$this->data['one_step'] = $onepage_settings['one_step'];
		$this->data['fixed_loading'] = $onepage_settings['fixed_loading'];
		$this->data['opc_layout'] = $onepage_settings['layout'];

		$this->data['logged'] = $this->customer->isLogged();
		$this->data['shipping_required'] = $this->cart->hasShipping();


		$theme_file = $onepage_settings['theme'];
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/' . $theme_file)) {
			$this->template = $this->config->get('config_template') . '/template/' . $theme_file;
		} else {
			$this->template = 'default/template/' . $theme_file;
		}

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);

		$this->response->setOutput($this->render());
  	}

  	public function updateGPaymentZone() {
  		require_once('catalog/controller/checkout/onepage_settings.php');
  		$json = array();
  		$json['mod'] = false;
  		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
  			if (isset($this->request->post['payment_address']) && $this->request->post['payment_address'] == 'existing') {
				if (isset($this->session->data['payment_address_id']) && $this->session->data['payment_address_id'] == $this->request->post['address_id']) {
					$json['mod'] = false;
				}
				else {
					$address_id = $this->request->post['address_id'];
					$this->session->data['payment_address_id'] = $address_id;

					unset($this->session->data['payment_methods']);
					unset($this->session->data['payment_method']);
					unset($this->session->data['guest']);
					$json['mod'] = true;
				}
				if ($onepage_settings['one_address']) {
					$json['mod'] |= updateExistigShipping($address_id);
				}
				// = true;
			}
  			else {
	  			$country_id = $this->request->post['country_id'];
	  			$zone_id = $this->request->post['zone_id'];

				if ($this->request->post['country_id'] != '' && $this->request->post['zone_id'] != '') {
					if (isset($this->session->data['guest']['payment']['country_id']) && isset($this->session->data['guest']['payment']['zone_id']) &&
						$country_id == $this->session->data['guest']['payment']['country_id'] && $zone_id == $this->session->data['guest']['payment']['zone_id'])
						$json['mod'] = false;
					else {
						$this->session->data['guest']['payment']['country_id'] = $country_id;
						$this->session->data['guest']['payment']['zone_id'] = $zone_id;
						$json['mod'] = true;
					}
					unset($this->session->data['payment_address_id']);
					$x = false;
					if ((isset($this->request->post['shipping_address']) && $this->request->post['shipping_address']) || $onepage_settings['one_address']) {
						$x = $this->updateShippingZone ($country_id, $zone_id);
					}
					$json['mod'] |= $x;
				}
  			}
  		}

  		$this->response->setOutput(json_encode($json));
  	}
  	public function updateGShippingZone() {
  		$json = array();
  		$json['mod'] = false;
  		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
  			if (isset($this->request->post['shipping_address']) && $this->request->post['shipping_address'] == 'existing') {
  				$json['existing'] = true;
				$json['mod'] = $this->updateExistigShipping ($this->request->post['address_id']);
				unset($this->session->data['guest']);
				// = true;
			}
			else if ($this->request->post['country_id'] != '' && $this->request->post['zone_id'] != '') {
				$json['mod'] = $this->updateShippingZone ($this->request->post['country_id'], $this->request->post['zone_id']);
				unset($this->session->data['shipping_address_id']);
				// = true;
			}
  		}
		$this->response->setOutput(json_encode($json));
  	}
	public function gp() {
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$code = $this->request->post['payment_method'];
			$json['output'] = $this->getChild('payment/' . $code);
			$this->response->setOutput(json_encode($json));
		}
	}

	private function updateShippingZone($country_id, $zone_id) {

		if (isset($this->session->data['guest']['shipping']['country_id']) && isset($this->session->data['guest']['shipping']['zone_id']) &&
			$country_id == $this->session->data['guest']['shipping']['country_id'] && $zone_id == $this->session->data['guest']['shipping']['zone_id'])
			return false;

		$this->session->data['guest']['shipping']['country_id'] = $country_id;
		$this->session->data['guest']['shipping']['zone_id'] = $zone_id;
		$this->session->data['guest']['shipping']['postcode'] = '';
		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($country_id);

		if ($country_info) {
			$this->session->data['guest']['shipping']['country'] = $country_info['name'];
			$this->session->data['guest']['shipping']['iso_code_2'] = $country_info['iso_code_2'];
			$this->session->data['guest']['shipping']['iso_code_3'] = $country_info['iso_code_3'];
			$this->session->data['guest']['shipping']['address_format'] = $country_info['address_format'];
		} else {
			$this->session->data['guest']['shipping']['country'] = '';
			$this->session->data['guest']['shipping']['iso_code_2'] = '';
			$this->session->data['guest']['shipping']['iso_code_3'] = '';
			$this->session->data['guest']['shipping']['address_format'] = '';
		}

		$this->load->model('localisation/zone');

		$zone_info = $this->model_localisation_zone->getZone($zone_id);

		if ($zone_info) {
			$this->session->data['guest']['shipping']['zone'] = $zone_info['name'];
			$this->session->data['guest']['shipping']['zone_code'] = $zone_info['code'];
		} else {
			$this->session->data['guest']['shipping']['zone'] = '';
			$this->session->data['guest']['shipping']['zone_code'] = '';
		}

		/*if ($this->cart->hasShipping()) {
			$this->tax->setZone($country_id, $zone_id);
		}*/
		return true;
	}


	private function updateExistigShipping($address_id) {
		if (isset($this->session->data['shipping_address_id']) && $this->session->data['shipping_address_id'] == $address_id) return false;
		$this->load->model('account/address');
		$this->session->data['shipping_address_id'] = $address_id;
		$address_info = $this->model_account_address->getAddress($address_id);

		/*if ($address_info) {
			$this->tax->setZone($address_info['country_id'], $address_info['zone_id']);
		}*/
		return true;
	}
	public function clearSuccess() {
		unset($this->session->data['success']);
	}
}
?>