<?php

class ExcelReader_XLS_Type3 extends ExcelReader_XLS
{
    const ID_PREFIX     = 'П';
    const CAT_COL_NUM  = 2;
    const NAME_COL_NUM  = 3;
    const STOCK_COL_NUM = 4;

    public $firstRowNum = 1    ;
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
            $curCat = array( 'name' => '' );

            for( $row = $this->getFirstRowNum(); $row <= $this->rowsQty; $row++ ){
                $product = array();
                $cells   = isset( $currentSheet[ 'cells' ][ $row ] ) ? $currentSheet[ 'cells' ][ $row ] : array();
                $cat     = $cells[ self::CAT_COL_NUM ];
                $cond    = !array_key_exists( mb_strtolower($cat, 'UTF-8'),  $this->getExcludedCats() );

                if( $cond || empty( $cells ) || empty( $cells[ $this->getIdColumnNum() ] ) )
                    continue;

                //if( $row > 9526 ) continue;

                $cells = $this->fillEmptyCells( $cells, $currentSheet[ 'numCols' ] );

                // Пропускаем шапку
                if( $row !== $this->getFirstRowNum() ){
                    if( $curCat['name'] !== $cat ){
                        $curCat['name'] = $cat;
                        $curCat[ 'id' ] = $model->addCategory( $curCat[ 'name' ], $category, $languages );
                    }

                    // Пребразуем артикул
                    $product[ 'sku' ] = $this->convertStr( $cells[ $this->getIdColumnNum() ] );
                    $product[ 'sku' ] = self::ID_PREFIX . $product[ 'sku' ];

                    // Делаем наценку
                    $product[ 'price' ] = 0;

                    // Название и количесво и категория
                    $product[ 'catId' ]  = $curCat['id'];
                    $product[ 'name' ]   = $this->convertStr( $cells[ self::NAME_COL_NUM ] );
                    $product[ 'stock' ]  = $this->convertStr( $cells[ self::STOCK_COL_NUM ] );

                    if( substr( $product[ 'stock' ], 0, 1 ) === '>' )
                        $product[ 'stock' ] = (int) substr( $product[ 'stock' ], 1 ) + 10;

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
        $this->priceIntervals = array();
    }

    public function getExcludedCats(){
        return $this->excludedCats;
    }

    public function setExcludedCats(){
        $this->excludedCats = array(
            'нетбуки & ноутбуки'    => array(
                12000  => array( '+' => 1500 ),
                20000  => array( '+' => 2000 ),
                'over' => array( '%' => 12 ),
            ),
            'планшетные компьютеры' => array(
                15000  => array( '+' => 1500 ),
                'over' => array( '%' => 15 ),
            ),
        );
    }
}
