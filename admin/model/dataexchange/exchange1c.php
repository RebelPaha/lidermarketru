<?php
class ModelDataexchangeExchange1c extends Model {

	private $CAT = array();
	private $digits = array('1','2','3','4','5','6','7','8','9','0');
	
/*Публічні функції які використовуються з контролера та інших модулів */	
	
	//Сформувати файл з ордерами для вивантаження в 1с
	public function parseOrders() {
		$orders = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?> <КоммерческаяИнформация ВерсияСхемы="2.04" ДатаФормирования="'.date('c').'"> </КоммерческаяИнформация>');
		$arrayOrders = $this->createArrayOrders();
		foreach($arrayOrders as $actual){
         $doc_level=$orders->addChild('Документ');
         //$this->add($orders->Документ,$actual);
         $this->add($doc_level,$actual);
		}
		$pathtofile = DIR_CACHE . 'exchange1c/'.date('YmdHis').'.xml';
		
		return iconv("UTF-8", "CP1251",$orders->asXML());
		//return $orders->asXML();		
		
	}

	//Парсити файл з каталогом товарів
	public function parseImport() {
		
		$PathToFile = DIR_CACHE . 'exchange1c/import.xml';
		$PathToPicture = DIR_IMAGE . 'data/';
		$object = simplexml_load_file($PathToFile);				
		
		if(isset($object->Классификатор)){
			$this->parseCategory($object->Классификатор);
		}
		
		
		$properties = $this->parseElementsAndProperties($object);
		foreach($object->Каталог->Товары->Товар as $data){
			$product = array();
			
			if(isset($data->Код)){$product['sku'] = htmlspecialchars(trim(strval($data->Код)));}
			if(isset($data->Ид)){$product['id'] = htmlspecialchars(trim(strval($data->Ид)));}
			if(isset($data->Ид)){
				$code1c = htmlspecialchars(trim(strval($data->Ид)));
				//Перевірим на наявність характеристик
				if (strpos($code1c,'#')===false){
					;
				} else{
					$product['id'] = substr($code1c, 0, strpos($code1c, '#'));
				}	
			}
			//var_dump($product['id']);
			if(isset($data->Наименование)){$product['name'] = htmlspecialchars(trim(strval($data->Наименование)));}
			if(isset($data->Артикул)){$product['model'] = htmlspecialchars(trim(strval($data->Артикул)));}
			
			//if(isset($data->ДополнительноеОписаниеНоменклатуры)){$product['description'] = htmlspecialchars(trim(strval($data->ДополнительноеОписаниеНоменклатуры)));}			
			
			if (isset($data->Группы)){
				$category_1c_id = strval($data->Группы->Ид); 
				$query = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'category_to_1c` WHERE `1c_category_id` = "' . $category_1c_id .'"');
				
				if ($query->num_rows){
					$product['product_category'] = $this->FindParentsCategories($query->row['category_id']);
				}else{
					$product['product_category'] = '0';
				}
			}
			
			if (isset($data->Картинка) and strval($data->Картинка)<>''){
				$ext = strtolower(strrchr(strval($data->Картинка),'.'));
				$PictureName = isset($product['sku'])?trim($product['sku']).$ext:$product['id'].$ext;
				If (!file_exists($PathToPicture.'products_from_1c')){
					if(!mkdir($PathToPicture.'products_from_1c',0777,true)){
						if(rename(DIR_IMAGE . strval($data->Картинка), $PathToPicture.$PictureName)){
							$product['image'] = 'data/'.$PictureName;
						}
					} 
					else{
						if(rename(DIR_IMAGE . strval($data->Картинка), $PathToPicture.'products_from_1c/'.$PictureName)){
							$product['image'] = 'data/products_from_1c/'.$PictureName;
						}
					}
				}
				else{
					if(rename(DIR_IMAGE . strval($data->Картинка), $PathToPicture.'products_from_1c/'.$PictureName)){
						$product['image'] = 'data/products_from_1c/'.$PictureName;
						
					}
				}
				
			}
			
			
			if(isset($properties[strval($data->Ид)]['Производитель'])){
				$query = $this->db->query('SELECT * FROM `'. DB_PREFIX .'manufacturer` WHERE `name`="'. trim($properties[strval($data->Ид)]['Производитель']) .'"');
				if($query->num_rows){
					$product['manufacturer_id'] = strval($query->row['manufacturer_id']);
				}
				else{
					$product['manufacturer_id'] = 0;
				}
			}
			//$product['description'] = $properties[$product['id']]['Полное наименование'];
			if (isset($properties[$product['id']]['Псевдоним'])){$product['keyword'] = $properties[$product['id']]['Псевдоним']; }
			if(isset($properties[$product['id']]['h1'])){$product['h1'] = $properties[$product['id']]['h1'];}
			if(isset($properties[$product['id']]['Статус'])){$product['status'] = $properties[$product['id']]['Статус'];}
			$this->setProduct($product);
		}	
		//
	} //ParseImport
	
	//функція яка парсить ціни і т.д.
	public function parseOffers($PathToFile='') {
		
		$result = array();
		if(isset($PathToFile)){
			$PathToFile = DIR_CACHE . 'exchange1c/offers.xml';
		}
		$object = simplexml_load_file($PathToFile);
	
		foreach($object->ПакетПредложений->Предложения->Предложение as $offer){
			$result['id'] = htmlspecialchars(trim(strval($offer->Ид)));
			$code1c = htmlspecialchars(trim(strval($offer->Ид)));
			if (strpos($code1c, '#')===false){
				;
			} else{
				$result['id'] = substr($code1c, 0, strpos($code1c, '#'));
			}
			
			$result['name'] = htmlspecialchars(trim(strval($offer->Наименование)));
			$result['quantity'] = htmlspecialchars(trim(strval($offer->Количество)));
			$result['price'] = htmlspecialchars(trim(strval($offer->Цены->Цена[0]->ЦенаЗаЕдиницу)));
			$this->updateProduct($result);
		}
		$this->cache->delete('product');
	}
	
/*Приватні функції які виконують брудну роботу*/	
	
	//Створює масив ордерів і товари в ньому підмасиви. Коротше структура.
	private function createArrayOrders(){
			$result = array();
		
		$query = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'order` WHERE (SELECT COUNT(`order_id`) FROM `'. DB_PREFIX .'order_to_1c`)<1 or `order_id` <> (SELECT `order_id` FROM `'. DB_PREFIX .'order_to_1c`)'); 
		$orders = $query->rows;
		$o=0;
		foreach ($orders as $order){
			
			$query = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'order_product` op LEFT JOIN `' . DB_PREFIX . 'product_to_1c` p1c ON (op.product_id = p1c.product_id)  WHERE `order_id` = "'.$order['order_id'].'" ');
			$products = $query->rows;
			$tax = 0;
			$p=0;
			foreach ($products as $product){
				$aproduct['Товар'.$p] = array(
					'Ид' => $product['1c_id'],
					'БазоваяЕдиница' => 'шт',
					'Наименование' => $product['name'],
					'ЦенаЗаЕдиницу' =>$product['price'],
					'Сумма' =>$product['total'],
					'Количество' => $product['quantity']
				);	
				$tax += $product['tax'];
				$p +=1;
			}
			
			$result['Документ'.$o] = array( 
					'Номер' => $order['order_id'],
					'Дата'  => date('Y-m-d', strtotime($order['date_added'])),
					'Время' => date('H:i:s', strtotime($order['date_added'])),
					//Роль => 'Продавец',
					'ХозОперация' => 'Заказ товара', 
					'Валюта' => $order['currency_code'],
					'Курс' => $order['currency_value'],
					//Сумма => $order['total'],
					'Контрагенты' => array(
						'Контрагент'=>array(
							'Наименование' => $order['customer_id'].'#'.$order['email'].'#'.$order['firstname'],
							//Комментарий =>'',
							'ПолноеНаименование' => $order['firstname'].' '.$order['lastname'],
							'Адрес' => array(
								'Представление' =>$order['shipping_company'].', '.$order['shipping_address_1'].', '.$order['shipping_city'].', '.$order['shipping_postcode'].', '.$order['shipping_country'],
								'АдресноеПоле' => array(
									'Тип' =>'ТелефонРабочий',
									'Значение' => $order['telephone']
								)
							),	
							'Роль' => 'Покупатель'	
						)	
					),
					'Комментарий' =>$order['shipping_firstname'].' '.$order['shipping_lastname'],
					'Налоги' => array(
						'Налог'=> array(
							'Наименование' =>'ПДВ',
							'УчтеноВСумме' =>'true',
							'Ставка' =>'20',
							'Сумма' =>$tax
						)
					),
					
					
					'Товары' => $aproduct
				);
				unset($aproduct)
				$o +=1;
		}
		return $result;
	}
	
	//Рекурсивно формує структуру класу хмл. 
	private function add($xml, $item){

		foreach($item as $name=>$val){
			if(is_array($val)){
				
				$child = $xml->addChild(str_replace($this->digits,"",$name));
				$this->add($child, $val);
			}
			else{
				$xml->addChild(str_replace($this->digits,"",$name), $val);
			}
		}
		return $xml;
	}	
	
	//Рекурсивно формує список ідів батьківських категорій в яких міститься товар
	private function FindParentsCategories($category_id){
		$query = $this->db->query('SELECT * FROM `'.DB_PREFIX.'category` WHERE `category_id` = "'.$category_id.'"');
		If ($query->row['parent_id']){
			$result = $this->FindParentsCategories($query->row['parent_id']);
		}
		$result[] = $category_id;
		return $result;
	}	
	
	//рекурсивна функція яка парсить всі категорії
	private function parseCategory($xml, $parent=0){
	
		foreach($xml->Группы->Группа as $category){
			$this->inserCategory($category, $parent);
			if(isset($category->Группы)){
				$this->parseCategory($category, (string)$category->Ид);
			}
			
		}
	}
	
	/*парсить всі властивості і реквізити
	функція видає всі властивості і реквізити
	в вигляді масиву виду $data[<ід_товару_1с>][<назва_властивості>]=Значення властивості*/
	private function parseElementsAndProperties($xml){
		
		$arrayOfProperty = array();
		$result = array();
		$ids_propertys = array();
		
		//спочатку знайдем іди властивостей в класифікаторі по їх назві.
		//Потім ми їх звичайно перепишем на нормальні назви
		if (isset($xml->Классификатор->Свойства->СвойствоНоменклатуры)){			
			foreach($xml->Классификатор->Свойства->СвойствоНоменклатуры as $property){
				$ids_propertys[htmlspecialchars(trim((string)$property->Ид))] = htmlspecialchars(trim((string)$property->Наименование));		
			}
		}
		
		if (isset($xml->Каталог->Товары->Товар->ЗначенияРеквизитов->ЗначениеРеквизита)){			
			foreach($xml->Каталог->Товары->Товар as $product_el){
				$product_id = (string)$product_el->Ид;
				foreach($product_el->ЗначенияРеквизитов->ЗначениеРеквизита as $element){
					$result[$product_id][htmlspecialchars(trim((string)$element->Наименование))] = htmlspecialchars(trim((string)$element->Значение));
				}
			}
		}
		
		
		if (isset($xml->Каталог->Товары->Товар)){
			foreach($xml->Каталог->Товары->Товар as $product){
				$product_id = (string)$product->Ид;
				if(isset($product->ЗначенияСвойств->ЗначенияСвойства)){
					foreach($product->ЗначенияСвойств->ЗначенияСвойства as $property){
						$arrayOfProperty = @json_decode(@json_encode($property),1);
						//Знаходим по іду наше значення.					
						$result[$product_id][(string)$ids_propertys[htmlspecialchars(trim((string)$arrayOfProperty['Ид']))]] = htmlspecialchars(trim((string)$arrayOfProperty['Значение']));
					}
				}
			}	
		}
	return $result;
	}
		
	// Функция добавляет корневую диреторию и всех детей
	private function inserCategory($category, $parent = 0) {
	
		$this->load->model('catalog/category');
		
		if( isset($category->Ид) AND isset($category->Наименование) ){ 
			$id =  strval($category->Ид);
			$name = htmlspecialchars(trim(strval($category->Наименование)));
			$data = array();
			$data['status'] = 1;
			
			$query = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'category_to_1c` WHERE `1c_category_id` = "' . (string)$parent . '"');
			if($query->num_rows) {
				$data['parent_id'] = strval($query->row['category_id']);
			} else {
				$data['parent_id'] = 0;
			}
			$data['category_store'] = array(0);
			$data['keyword'] = '';
			//$data['image'] = '';
			//$data['sort_order'] = 0;
			
			$query = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'category_to_1c` WHERE `1c_category_id` = "' . (string)$id . '"');
			if($query->num_rows) {
				$category_id = (int)$query->row['category_id'];
				$query2 = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'category` WHERE `category_id` = "' . (string)$category_id . '"');
				if($query2->num_rows) {//товар есть в таблице 1с и в таблице товаров можно обновлять

					$data['category_description'] = $this->model_catalog_category->getCategoryDescriptions($category_id);
					$data['category_description'][(int)$this->config->get('config_language_id')] = array(
						'name' =>  htmlspecialchars(trim(strval($category->Наименование))),
						'meta_keywords' => '',
						'meta_description' => '',
						'description' => '',
						'title'	=> '',
						'h1' => ''
					);
					$this->model_catalog_category->editCategory($category_id,$data);
					return;
				}
				else{//Товар есть в таблице 1с но нет в таблице товаров(был удален). Надо удалить из таблицы 1с
					$this->db->query('DELETE FROM `' . DB_PREFIX . 'category_to_1c` WHERE `1c_id` = "' .(string)$id. '"');
				}
			}
						
			$this->model_catalog_category->addCategory($data);
			$this->db->query('INSERT INTO `' . DB_PREFIX . 'category_to_1c` SET category_id = ' . (string)$this->getLastId('category','category_id') . ', `1c_category_id` = "' . (string)$id . '"');
			$this->CAT[$id] = $category_id;
				
		}
	}
	
	
	/**
	*	Функция работы с продуктом
	* 	Доповнює в товар що не задано раніше.
	*/
	private function initProduct($product, $data = array()) {
	
		//$data['product_description']['language_id']=
		$data['product_description'][(int)$this->config->get('config_language_id')] =  array(
				'name' => isset($product['name']) ? trim($product['name']): (isset($data['product_description'][(int)$this->config->get('config_language_id')]['name'])? $data['product_description'][(int)$this->config->get('config_language_id')]['name']: 'Имя не задано'),			
				'meta_keyword' => isset($product['meta_keyword']) ? trim($product['meta_keyword']): (isset($data['product_description'][(int)$this->config->get('config_language_id')]['meta_keyword'])? $data['product_description'][(int)$this->config->get('config_language_id')]['meta_keyword']: ''),
				'meta_description' => isset($product['meta_description']) ? trim($product['meta_description']): (isset($data['product_description'][(int)$this->config->get('config_language_id')]['meta_description'])? $data['product_description'][(int)$this->config->get('config_language_id')]['meta_description']: ''),
				'description' => isset($product['description']) ? trim($product['description']): (isset($data['product_description'][(int)$this->config->get('config_language_id')]['description'])? $data['product_description'][(int)$this->config->get('config_language_id')]['description']: ''),
				'title' => isset($product['title']) ? $product['title']: (isset($data['product_description'][(int)$this->config->get('config_language_id')]['title'])? $data['product_description'][(int)$this->config->get('config_language_id')]['title']: ''),
				'h1' => isset($product['h1']) ? $product['h1']: (isset($data['product_description'][(int)$this->config->get('config_language_id')]['h1'])? $data['product_description'][(int)$this->config->get('config_language_id')]['h1']: '')
			);
		// Модель
		$data['model'] = (isset($product['model'])) ?$product['model'] : (isset($data['model'])? $data['model']: '');
		
		// SKU
		$data['sku'] = (isset($product['sku'])) ?$product['sku'] : (isset($data['sku'])? $data['sku']: '0');
		$data['ups'] = (isset($product['ups'])) ?$product['ups'] : (isset($data['ups'])? $data['ups']: '');
		$data['points'] = (isset($product['points'])) ?$product['points'] : (isset($data['points'])? $data['points']: '');
		
		$data['location'] = (isset($product['location'])) ?$product['location'] : (isset($data['location'])? $data['location']: '');
		
		// Магазин в который выгружаем
		$data['product_store'] = array(0);
		
		$data['meta_keyword'] = (isset($product['meta_keyword'])) ?$product['meta_keyword'] : (isset($data['meta_keyword'])? $data['meta_keyword']: '');
		
		$data['tag'] = (isset($product['tag'])) ?$product['tag'] : (isset($data['tag'])? $data['tag']: array());
		
		// Изображение

		if (isset($product['image'])){
			$data['image'] = $product['image'];
		}
		$this->load->model('tool/image');
		
		$data['preview'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		
		$data['manufacturer_id'] = (isset($product['manufacturer_id'])) ?$product['manufacturer_id'] : (isset($data['manufacturer_id'])? $data['manufacturer_id']: 0);
		
		$data['shipping'] = (isset($product['shipping'])) ?$product['shipping'] : (isset($data['shipping'])? $data['shipping']: 1);
		
		$data['date_available'] = date('Y-m-d', time()-86400);
		
		$data['quantity'] = (isset($product['quantity'])) ?$product['quantity'] : (isset($data['quantity'])? $data['quantity']: 0);
		
		$data['minimum'] = (isset($product['minimum'])) ?$product['minimum'] : (isset($data['minimum'])? $data['minimum']: 1);
		
		$data['subtract'] = (isset($product['subtract'])) ?$product['subtract'] : (isset($data['subtract'])? $data['subtract']: 1);
		
		$data['sort_order'] = (isset($product['sort_order'])) ?$product['sort_order'] : (isset($data['sort_order'])? $data['sort_order']: 1);
		
		$data['stock_status_id'] = $this->config->get('config_stock_status_id');
		
		$data['price'] = (isset($product['price'])) ?$product['price'] : (isset($data['price'])? $data['price']: 0);
		
		$data['cost'] = (isset($product['cost'])) ?$product['cost'] : (isset($data['cost'])? $data['cost']: 0);
		
		$data['status'] = (isset($product['status'])) ?$product['status'] : (isset($data['status'])? $data['status']: 1);
		
		$data['tax_class_id'] = (isset($product['tax_class_id'])) ?$product['tax_class_id'] : (isset($data['tax_class_id'])? $data['tax_class_id']: 0);
		
		$data['weight'] = (isset($product['weight'])) ?$product['weight'] : (isset($data['weight'])? $data['weight']: '');
		
		$data['weight_class_id'] = (isset($product['weight_class_id'])) ?$product['weight_class_id'] : (isset($data['weight_class_id'])? $data['weight_class_id']: 1);
		
		$data['length'] = (isset($product['length'])) ?$product['length'] : (isset($data['length'])? $data['length']: '');
		
		$data['width'] = (isset($product['width'])) ?$product['width'] : (isset($data['width'])? $data['width']: '');
		
		$data['height'] = (isset($product['height'])) ?$product['height'] : (isset($data['height'])? $data['height']: '');
		
		$data['length_class_id'] = (isset($product['length_class_id'])) ?$product['length_class_id'] : (isset($data['length_class_id'])? $data['length_class_id']: 1);
		
		$data['product_options'] = (isset($product['product_options'])) ?$product['product_options'] : (isset($data['product_options'])? $data['product_options']: array());
		
		$data['product_discounts'] = (isset($product['product_discounts'])) ?$product['product_discounts'] : (isset($data['product_discounts'])? $data['product_discounts']: array());
		
		$data['product_specials'] = (isset($product['product_specials'])) ?$product['product_specials'] : (isset($data['product_specials'])? $data['product_specials']: array());
		
		$data['product_download'] = (isset($product['product_download'])) ?$product['product_download'] : (isset($data['product_download'])? $data['product_download']: array());
		
		$data['product_related'] = (isset($product['product_related'])) ?$product['product_related'] : (isset($data['product_related'])? $data['product_related']: array());

		if (isset($product['product_category'])){
			$data['product_category'] = $product['product_category'];
			
		}
		
		return $data;
	}

	
	
	/**
	*	Функция работы с продуктом
	*/
	private function setProduct($product) {
		
		if(!$product) return;
		
		//Проверяем есть ли такой товар в БД
		$query = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'product_to_1c` WHERE `1c_id` = "' . $this->db->escape($product['id']) . '"');
		
		if($query->num_rows) {
			$product_id = (int)$query->row['product_id'];
			$query2 = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'product` WHERE `product_id` = "' . $product_id . '"');
			if($query2->num_rows) {//товар есть в таблице 1с и в таблице товаров можно обновлять
				return $this->updateProduct($product, $product_id);
			} 
			else{//Товар есть в таблице 1с но нет в таблице товаров(был удален). Надо удалить из таблицы 1с
				$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_to_1c` WHERE `1c_id` = "' .$product_id. '"');
			}
		} 	
		
		// Заполняем значения продукта
		$data = $this->initProduct($product);
		
		$this->load->model('catalog/product');		 
		$this->model_catalog_product->addProduct($data);
		
		// Добавляемя линкт в дб
		$this->db->query('INSERT INTO `' .  DB_PREFIX . 'product_to_1c` SET product_id = ' . (int)$this->getLastId('product','product_id'). ', `1c_id` = "' . $this->db->escape($product['id']) . '"');
		
	}
	
	//Обновляє товар якщо він є в табицімproduct_to_1c	
	private function updateProduct($product, $product_id = 0) {
		
		$this->load->model('catalog/product');
		// Проверяем что обновлять?
		if( ! $product_id ) {
			
			$query = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'product_to_1c` WHERE `1c_id` = "' . $this->db->escape($product['id']) . '"');
			
			if($query->num_rows) {	
				$product_id = (int)$query->row['product_id'];
			} else {

				echo '<pre>';
				var_dump($product);
				exit;
			}
		}
		
		// Обновляєм опис продукта
		$product_old = $this->model_catalog_product->getProduct($product_id);
		
		$product_old = array_merge($product_old, array('product_description' => $this->model_catalog_product->getProductDescriptions($product_id)));
		$product_old = array_merge($product_old, array('product_category' => $this->model_catalog_product->getProductCategories($product_id)));
		$product_old = array_merge($product_old, array('product_image' => $this->model_catalog_product->getProductImages($product_id)));
		$product_old = array_merge($product_old, array('product_attribute' => $this->model_catalog_product->getProductAttributes($product_id)));
		$product_old = array_merge($product_old, array('product_discount' => $this->model_catalog_product->getProductDiscounts($product_id)));
		$product_old = array_merge($product_old, array('product_option' => $this->model_catalog_product->getProductOptions($product_id)));
		$product_old = array_merge($product_old, array('product_related' => $this->model_catalog_product->getProductRelated($product_id)));
		$product_old = array_merge($product_old, array('product_tag' => $this->model_catalog_product->getProductTags($product_id)));
			
		$new_product = $this->initProduct($product, $product_old);
		
		$this->load->model('catalog/product');
		// Редактируем продукт
		
		
		$this->model_catalog_product->editProduct($product_id, $new_product);
		
	}
	
	// --- Специальные функции
	private function getProductIdBy1CProductId($id) {}
	
	private function getProductIdBy1CProductName($name) {
		$sql = 'SELECT p.product_id FROM ' . DB_PREFIX . 'product p LEFT JOIN ' . DB_PREFIX . 'product_description pd ON (p.product_id = pd.product_id) WHERE pd.name LIKE "'.$this->db->escape($name).'"';
		
		$query = $this->db->query($sql);
		
		//var_dump($query);
		
		if( ! $query->num_rows) return 0;
		
		return (int)$query->row['product_id'];
	}
	
	public function getProduct($product_id) {
		
		$query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "') AS keyword FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		$product = $query->row;
		
		
		
		
		return $product; 
		
	}	
	
	private function getLastId($table, $field) {
		
		$query = $this->db->query('SELECT * FROM `' . DB_PREFIX . $table.'` GROUP BY '.$field.' ORDER BY `'.$field.'` DESC LIMIT 1');
		if($query->num_rows) {	
			$value =(int)$query->row[$field];
			return $value;
		}
		return 0;
	}	

	// Утилиты 
	public function checkDbSheme() {
	
		// 
		$query = $this->db->query('SHOW TABLES LIKE "' . DB_PREFIX . 'product_to_1c"');
		
		if( ! $query->num_rows ) {
			// Создаем БД
			
			$this->db->query(
					'CREATE TABLE 
						`' . DB_PREFIX . 'product_to_1c` ( 
							`product_id` int(10) unsigned NOT NULL,
 							`1c_id` varchar(255) NOT NULL,
 							KEY (`product_id`),
 							KEY `1c_id` (`1c_id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8'
			);			
		}


		// 
		$query = $this->db->query('SHOW TABLES LIKE "' . DB_PREFIX . 'category_to_1c"');
		
		if( ! $query->num_rows ) {
			// Создаем БД
			
			$this->db->query(
					'CREATE TABLE 
						`' . DB_PREFIX . 'category_to_1c` ( 
							`category_id` int(10) unsigned NOT NULL,
 							`1c_category_id` varchar(255) NOT NULL,
 							KEY (`category_id`),
 							KEY `1c_id` (`1c_category_id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8'
			);			
		}		
		
		$query = $this->db->query('SHOW TABLES LIKE "' . DB_PREFIX . 'order_to_1c"');
				if( ! $query->num_rows ) {
			// Создаем БД
			
			$this->db->query(
					'CREATE TABLE 
						`' . DB_PREFIX . 'order_to_1c` ( 
							`order_id` int(10) unsigned NOT NULL,
 							`1c_order_id` varchar(255) NOT NULL,
 							KEY (`order_id`),
 							KEY `1c_id` (`1c_order_id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8'
			);			
		}
		
		return 0;
	
	}

}
?>