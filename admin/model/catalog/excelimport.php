<?php
class ModelCatalogExcelImport extends Model {
    public function clearTables(){
        $this->db->query('TRUNCATE TABLE `' . DB_PREFIX . 'category`;');
        $this->db->query('TRUNCATE TABLE `' . DB_PREFIX . 'category_description`;');
        $this->db->query('TRUNCATE TABLE `' . DB_PREFIX . 'category_to_store`;');

        $this->db->query('TRUNCATE TABLE `' . DB_PREFIX . 'product`;');
        $this->db->query('TRUNCATE TABLE `' . DB_PREFIX . 'product_description`;');
        $this->db->query('TRUNCATE TABLE `' . DB_PREFIX . 'product_to_category`;');
        $this->db->query('TRUNCATE TABLE `' . DB_PREFIX . 'product_to_store`;');
    }
	public function addCategory( $catName, $parentId, $languages ) {
        $sql = "SELECT c.category_id
                FROM `" . DB_PREFIX . "category_description` cd
                INNER JOIN `" . DB_PREFIX . "category` c ON c.category_id = cd.category_id
                WHERE c.parent_id = '$parentId' AND cd.name = '" . $this->db->escape($catName) . "'";
        $result = $this->db->query( $sql );

        if( $result->num_rows ){
            $category_id = $result->row['category_id'];
        }
        else{
            $this->db->query("INSERT INTO " . DB_PREFIX . "category SET
                parent_id = '" . (int)$parentId . "',
                `top` = '" . ( (int) ( $parentId === 0 ) ) . "',
                `column` = '1',
                sort_order = '0',
                status = '1',
                date_modified = NOW(),
                date_added = NOW()"
            );

            $category_id = $this->db->getLastId();

            foreach( $languages as $language ) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "category_description SET
                    category_id = '"      . (int) $category_id . "',
                    language_id = '"      . (int) $language['language_id'] . "',
                    name = '"             . $this->db->escape( $catName ) . "',
                    meta_keyword = '',
                    meta_description = '" . $this->db->escape( $catName )  . "',
                    description = '',
                    seo_title = '"        . $this->db->escape( $catName ) . "',
                    seo_h1 = '"           . $this->db->escape( $catName ) . "'"
                );
            }

            // Save cat to store
            $this->db->query( "INSERT INTO " . DB_PREFIX . "category_to_store SET category_id = '" . (int) $category_id . "', store_id = '0'" );

            $this->cache->delete( 'category' );
        }

        return $category_id;
	}

    public function saveProduct( $product, $languages ){
        $sql = "SELECT sku FROM `" . DB_PREFIX . "product` WHERE sku = '" . $product['sku'] . "'";

        if( $this->db->query( $sql )->num_rows ){
            $sql = "UPDATE `" . DB_PREFIX . "product` SET sku = '" . $product['sku'] . "', quantity = '" . $product['sku'] . "'";
            $this->db->query( $sql );
        }
        else {
            $sql = "INSERT INTO `" . DB_PREFIX . "product` SET
                model = '',
                sku = '" . $this->db->escape( $product['sku'] ) . "',
                upc = '',
                location = '',
                quantity = '" . (int)$product['stock'] . "',
                minimum = '1',
                subtract = '1',
                stock_status_id = '5',
                date_available = NOW(),
                manufacturer_id = '0',
                shipping = '0',
                price = '" . (float)$product['price'] . "',
                points = '0',
                weight = '0',
                weight_class_id = '0',
                length = '0',
                width = '0',
                height = '0',
                length_class_id = '0',
                status = '1',
                tax_class_id = '0',
                sort_order = '0',
                date_added = NOW()";

            $result = $this->db->query( $sql );

            if( $result ){
                $product_id = $this->db->getLastId();

                foreach( $languages as $language ) {
                    $sql = "INSERT INTO `" . DB_PREFIX . "product_description` SET
                        product_id = '"       . (int) $product_id . "',
                        language_id = '"      . (int)$language['language_id'] . "',
                        name = '"             . $this->db->escape ($product['name'] ) . "',
                        meta_keyword = '',
                        meta_description = '" . $this->db->escape ($product['name'] ) . "',
                        description = '',
                        seo_title = '"        . $this->db->escape ($product['name'] ) . "',
                        seo_h1 = '"           . $this->db->escape ($product['name'] ) . "'";
                    $this->db->query( $sql );
                }

                $this->db->query( "DELETE FROM " . DB_PREFIX . "product_to_category
                    WHERE product_id = '" . (int)$product_id . "'
                    AND category_id = '" . (int)$product['catId'] . "'" );
                $this->db->query( "INSERT INTO " . DB_PREFIX . "product_to_category SET
                    product_id = '" . (int)$product_id . "',
                    category_id = '" . (int)$product['catId'] . "',
                    main_category = 1" );


                $this->db->query("INSERT INTO `" . DB_PREFIX . "product_to_store` SET product_id = '" . (int)$product_id . "', store_id = '0'");
            }
        }

        $this->cache->delete( 'product' );
    }
}