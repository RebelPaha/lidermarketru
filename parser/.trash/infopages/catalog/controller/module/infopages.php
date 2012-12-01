<?php   
class ControllerModuleInfopages extends Controller {
	protected function index() {
		
		$this->language->load('module/infopages');
		
    	$this->data['heading_title'] = $this->language->get('heading_title');
    	
		
		
		$this->load->model('catalog/information');
		
		$this->data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
      		$this->data['informations'][] = array(
        		'title' => $result['title'],
	    		'href'  => $result['information_id']
      		);
    	}
			$this->data['href'] = 'index.php?route=information/information&information_id=';
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/infopages.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/module/infopages.tpl';
			} else {
				$this->template = 'default/template/module/infopages.tpl';
			}
			
			$this->render();
		
	}
}
?>