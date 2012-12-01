<?php

class ExcelReader_XLS_Type2 extends ExcelReader_XLS
{
    const ID_PREFIX     = 'Ф';
    const PRICE_COM_NUM = 7;
    const NAME_COL_NUM  = 2;
    const STOCK_COL_NUM = 3;

    public $firstRowNum = 12    ;
    public $idColumnNum = 1;

    public function init(){
        parent::init();

        $this->setPriceIntervals();
        $this->setExcludedCats();
    }

    public function run( $model, $category, $languages ){
        foreach( $this->getAllowedSheets() as $sheetNum ){
            $currentSheet  = $this->data->sheets[ $sheetNum - 1 ];
            $this->rowsQty = count( $currentSheet[ 'cells' ] );
            $curCat = '';
            $cats   = array();

            for( $row = $this->getFirstRowNum(); $row <= $this->rowsQty; $row++ ){
               // if( $row > 25 ) continue;

                $product = array();
                $cells   = isset( $currentSheet[ 'cells' ][ $row ] ) ? $currentSheet[ 'cells' ][ $row ] : array();

                if( count( $cells ) === 1 ){
                    $curCat = $this->convertStr( $cells[ 1 ] );

                    if( count( $currentSheet[ 'cells' ][ $row + 2 ] ) === 1 )
                        $cats['cat1'] = array(
                            'name' => $curCat,
                            'id'   => $model->addCategory( $curCat, $category, $languages )
                        );
                    elseif( count( $currentSheet[ 'cells' ][ $row + 1 ] ) === 1 )
                        $cats['cat2'] = array(
                            'name' => $curCat,
                            'id'   => $model->addCategory( $curCat, $cats['cat1']['id'], $languages )
                        );
                    else
                        $cats['cat3'] = array(
                            'name' => $curCat,
                            'id'   => $model->addCategory( $curCat, $cats['cat2']['id'], $languages )
                        );

                    continue;
                }

                if( empty( $cells ) || empty( $cells[ $this->getIdColumnNum() ] ) )
                    continue;

                $cells = $this->fillEmptyCells( $cells, $currentSheet[ 'numCols' ] );

                // Пропускаем шапку
                if( $row !== $this->getFirstRowNum() ){
                    // Пребразуем артикул
                    $product[ 'sku' ] = $this->convertStr( $cells[ $this->getIdColumnNum() ] );
                    $product[ 'sku' ] = self::ID_PREFIX . $product[ 'sku' ];

                    // Делаем наценку
                    $product[ 'price' ] = $this->getPriceWithMargin(
                        $this->convertStr( $cells[ self::PRICE_COM_NUM ] ),
                        array( $cats['cat2']['name'], $cats['cat1']['name'] )
                    );

                    // Название и количесво и категория
                    $product[ 'name' ]   = $this->convertStr( $cells[ self::NAME_COL_NUM ] );
                    $product[ 'stock' ]  = (int) ($this->convertStr( $cells[ self::STOCK_COL_NUM ] ) === '+' );
                    $product[ 'catId' ]  = $cats['cat3']['id'];

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
            100    => array( '%' => 200 ),
            220    => array( '%' => 100 ),
            320    => array( '%' => 70 ),
            420    => array( '%' => 60 ),
            550    => array( '%' => 50 ),
            720    => array( '%' => 35 ),
            820    => array( '%' => 30 ),
            2700   => array( '%' => 25 ),
            12000  => array( '%' => 20 ),
            20000  => array( '%' => 17 ),
            30000  => array( '%' => 14 ),
            'over' => array( '%' => 10 )
        );

        $this->maxMargin = 10;
    }

    public function getExcludedCats(){
        return $this->excludedCats;
    }

    public function setExcludedCats(){
        $this->excludedCats = array(
            'совместимые расходные материалы'   => array( '%' => 100 ), // ?
            'оригинальные расходные материалы'  => array( '%' => 15 ), // ?
            'бумага'                            => array( '%' => 15 ), // =
            'принтеры'                          => array( '%' => 15 ), // =
            'мфу'                               => array( '%' => 15 ), // =
            'плоттеры'                          => array( '%' => 10 ), // =
            'продукция apple'                   => array( '%' => 15 ), // =
            'программное обеспечение microsoft' => array( '%' => 18 ), // =
            'антивирусные программы'            => array( '%' => 20 ), // =
            'ноутбуки'                          => array(
                15000  => array( '+' => 1500 ),
                20000  => array( '+' => 2000 ),
                'over' => array( '%' => 10 ),
            ),
            'нетбуки'                           => array(
                15000  => array( '+' => 1500 ),
                20000  => array( '+' => 2000 ),
                'over' => array( '%' => 10 ),
            ),
            'планшетные компьютеры'             => array(
                15000  => array( '+' => 1500 ),
                'over' => array( '%' => 10 ),
            )
        );
    }
}
