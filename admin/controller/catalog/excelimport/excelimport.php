<?php

class ControllerCatalogExcelImportExcelImport extends Controller {
    private $error = array();

    public function index(){
        $this->load->language('catalog/excelimport');

        $this->document->setTitle($this->language->get('heading_title'));

       // var_dump( $this->request->post, $_FILES );exit;
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && !empty( $_FILES['file'] )) {
            set_time_limit( 0 );
            // TODO
            ini_set( 'memory_limit', '256M' );

            include_once( dirname(__FILE__) . '/classes/excel_reader2.php' );
            include_once( dirname(__FILE__) . '/classes/ExcelReader.php' );
            include_once( dirname(__FILE__) . '/classes/ExcelReader/XLS.php' );
            include_once( dirname(__FILE__) . '/classes/ExcelReader/XLS_Type' . $this->request->post['type'] . '.php' );

            //var_dump( $this->request->post, $this->request->files );exit;

            $excelReaderClassName = 'ExcelReader_XLS_Type' . $this->request->post['type'];

            if( !class_exists($excelReaderClassName) ){
                throw new Exception('Class ' . $excelReaderClassName . ' not exist!');
            }

            $tmpFile = $_FILES[ 'file' ][ 'tmp_name' ];
            $inputFile   = dirname( $tmpFile ) . DIRECTORY_SEPARATOR . $_FILES[ 'file' ][ 'name' ];
            move_uploaded_file( $tmpFile, $inputFile );

            $excelReader = new $excelReaderClassName( $inputFile, dirname( $inputFile ) );

            $this->load->model('catalog/excelimport');
            $this->load->model('localisation/language');

            $languages = $this->model_localisation_language->getLanguages();


            if( 0 ){
                $this->model_catalog_excelimport->clearTables();
                exit;
            }

            $excelReader->run( $this->model_catalog_excelimport, $this->request->post['category_id'], $languages );


            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('catalog/excelimport/excelimport/index', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function getForm(){
        $this->data['heading_title']  = $this->language->get('heading_title');
        $this->data['entry_name']     = $this->language->get('entry_name');
        $this->data['entry_type']     = $this->language->get('entry_type');
        $this->data['entry_category'] = $this->language->get('entry_category');
        $this->data['button_run']     = $this->language->get('button_run');
        $this->data['text_none']      = $this->language->get('text_none');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $this->data['error_name'] = $this->error['name'];
        } else {
            $this->data['error_name'] = array();
        }

        $url = '';

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('catalog/excelimport/excelimport', 'token=' . $this->session->data['token'] . $url, 'SSL'),
            'separator' => ' :: '
        );

        $this->load->model('localisation/language');
        $this->load->model('catalog/category');

        $this->data['languages'] = $this->model_localisation_language->getLanguages();
        $categories = $this->model_catalog_category->getAllCategories();
        $this->data['categories'] = $this->getAllCategories($categories);

        $this->data['action'] = $this->url->link('catalog/excelimport/excelimport', 'token=' . $this->session->data['token'] . $url, 'SSL');

        if (isset($this->request->post['sort_order'])) {
            $this->data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($attribute_group_info)) {
            $this->data['sort_order'] = $attribute_group_info['sort_order'];
        } else {
            $this->data['sort_order'] = '';
        }

        $this->template = 'catalog/excelimport.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    private function getAllCategories($categories, $parent_id = 0, $parent_name = '') {
        $output = array();

        if (array_key_exists($parent_id, $categories)) {
            if ($parent_name != '') {
                $parent_name .= $this->language->get('text_separator');
            }

            foreach ($categories[$parent_id] as $category) {
                $output[$category['category_id']] = array(
                    'category_id' => $category['category_id'],
                    'name'        => $parent_name . $category['name']
                );

                $output += $this->getAllCategories($categories, $category['category_id'], $parent_name . $category['name']);
            }
        }

        return $output;
    }
}