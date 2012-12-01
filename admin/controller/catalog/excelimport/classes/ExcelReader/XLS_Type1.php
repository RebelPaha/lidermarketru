<?php

class ExcelReader_XLS_Type1 extends ExcelReader_XLS
{
    const ID_PREFIX     = 'Б';
    const CAT_COL_NUM   = 1;
    const PRICE_COM_NUM = 7;
    const NAME_COL_NUM  = 6;
    const STOCK_COL_NUM = 10;

    public $firstRowNum = 3;
    public $idColumnNum = 3;

    public function init(){
        parent::init();

        $this->setPriceIntervals();
        $this->setExcludedCats();
    }

    public function run( $model, $category, $languages ){
        foreach( $this->getAllowedSheets() as $sheetNum ){
            $currentSheet  = $this->data->sheets[ $sheetNum - 1 ];
            $this->rowsQty = count( $currentSheet[ 'cells' ] );
            $cats = array();

            for( $row = $this->getFirstRowNum(); $row <= $this->rowsQty; $row++ ){
                //if( $row > 10 ) continue;

                $product = array();
                $cells   = isset( $currentSheet[ 'cells' ][ $row ] ) ? $currentSheet[ 'cells' ][ $row ] : array();

                if( count( $cells ) === 4 ){
                    $curCat = $this->convertStr( $cells[ 1 ] );

                    if( count( $currentSheet[ 'cells' ][ $row + 1 ] ) === 4 ){
                        $cats['cat1'] = array(
                            'name' => $curCat,
                            'id'   => $model->addCategory( $curCat, $category, $languages )
                        );
                    }
                    else{
                        $cats['cat2'] = array(
                            'name' => $curCat,
                            'id'   => $model->addCategory( $curCat, $cats['cat1']['id'], $languages )
                        );
                    }
                }

                if( empty( $cells ) || empty( $cells[ $this->getIdColumnNum() ] ) )
                    continue;

                $cells = $this->fillEmptyCells( $cells, $currentSheet[ 'numCols' ] );

                // Пропускаем шапку
                if( $row !== $this->getFirstRowNum() ){
                    // Пребразуем актикул
                    $product[ 'sku' ] = $this->convertStr( $cells[ $this->getIdColumnNum() ] );
                    $product[ 'sku' ] = self::ID_PREFIX . $product[ 'sku' ];

                    // Делаем наценку
                    $product[ 'price' ] = $this->getPriceWithMargin(
                        $this->convertStr( $cells[ self::PRICE_COM_NUM ] ),
                        array( $cats['cat2']['name'], $cats['cat1']['name'] )
                    );

                    // Название и количесво и категория
                    $product[ 'name' ]   = $this->convertStr( $cells[ self::NAME_COL_NUM ] );
                    $product[ 'stock' ]  = (int) $this->convertStr( $cells[ self::STOCK_COL_NUM ] );
                    $product[ 'catId' ]  = isset( $cats['cat2']['id'] ) ? $cats['cat2']['id'] : $cats['cat1']['id'];

                    $model->saveProduct( $product, $languages );
                }
            }
        }

        return $this;
    }


    public function getPriceIntervals(){
        return $this->priceIntervals;
    }

    public function setPriceIntervals(){
        $this->priceIntervals = array(
            95     => array( '%' => 200 ),
            250    => array( '%' => 100 ),
            290    => array( '%' => 70 ),
            390    => array( '%' => 60 ),
            590    => array( '%' => 50 ),
            900    => array( '%' => 35 ),
            2700   => array( '%' => 25 ),
            12000  => array( '%' => 20 ),
            25000  => array( '%' => 17 ),
            30000  => array( '%' => 15 ),
            'over' => array( '%' => 10 )
        );

        $this->maxMargin = 10;
    }

    public function getExcludedCats(){
        return $this->excludedCats;
    }

    public function setExcludedCats(){
        $this->excludedCats = array(
            'совместимые расходные материалы' => array( '%' => 100 ), // =
            'оригинальные расходные материалы' => array( '%' => 15 ), // =
            'бумага' => array( '%' => 17 ), // =
            'устройства печати' => array( '%' => 10 ) // =
        );
    }
}
