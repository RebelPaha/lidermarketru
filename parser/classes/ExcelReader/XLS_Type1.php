<?php

require_once( PATH_ROOT . 'classes/excel_reader2.php' );

class ExcelReader_XLS_Type1 extends ExcelReader_XLS
{
    const ID_PREFIX = 'Б';
    const PRICE_COM_NUM = 7;

    public $priceIntervals;
    public $maxMargin;
    public $excludedCats = array();

    public function init(){
        parent::init();

        $this->setPriceIntervals();
        $this->setExcludedCats();
    }


    public function convert(){
        foreach( $this->getAllowedSheets() as $sheetNum ){
            $currentSheet  = $this->data->sheets[ $sheetNum - 1 ];
            $this->rowsQty = count( $currentSheet[ 'cells' ] );
            $cat = '';
            $cats = array();

            for( $row = $this->getFirstRowNum(); $row <= $this->rowsQty; $row++ ){
                //if( $row > 10 ) continue; //

                $cells = isset( $currentSheet[ 'cells' ][ $row ] ) ? $currentSheet[ 'cells' ][ $row ] : array();
                $nextRowCells = isset( $currentSheet[ 'cells' ][ $row + 1 ] ) ? $currentSheet[ 'cells' ][ $row + 1 ] : array();

                $cond1 = count( $cells ) === 4;
                $cond2 = !empty( $cells[ 1 ] ) && empty( $cells[ 2 ] ) && empty( $cells[ 3 ] ) && empty( $cells[ 4 ] );
                $cond3 = !empty( $nextRowCells[ 1 ] )
                    && empty( $nextRowCells[ 2 ] )
                    && empty( $nextRowCells[ 3 ] )
                    && empty( $nextRowCells[ 4 ] );

                if( $cond1 &&  $cond2 && $cond3 ){
                    $cat = $cells[ 1 ];

                    if( !isset( $cats[ $cat ] ) ){
                        $cats[ $cat ] = array();
                    }
                }

                if( empty( $cells ) || empty( $cells[ $this->getIdColumnNum() ] ) )
                    continue;

                $cells = $this->fillEmptyCells( $cells, $currentSheet[ 'numCols' ] );

                // Пропускаем шапку
                if( $row !== $this->getFirstRowNum() ){
                    // Пребразуем актикул
                    $cells[ $this->getIdColumnNum() ] = self::ID_PREFIX . $cells[ $this->getIdColumnNum() ];

                    // Делаем наценку
                    $cells[ self::PRICE_COM_NUM ] = $this->convertPrice( $cells[ self::PRICE_COM_NUM ], $cat );


                    if( !in_array( $cells[ 1 ], $cats[ $cat ] ) && !empty( $cells[ 1 ] ) ){
                        array_push( $cats[ $cat ], $cells[ 1 ] );
                    }
                }

                fputcsv( $this->_outputFileHandle, $cells, ';', '"' );
            }

            var_dump($cats);continue;
        }

        $this->finish();

        return $this;
    }



    public function getPriceIntervals(){
        return $this->priceIntervals;
    }

    public function setPriceIntervals(){
        $this->priceIntervals = array(
            95 => 200, 250 => 100, 290 => 70, 390 => 60, 590 => 50, 900 => 35,
            2700 => 25,
            12000 => 20,
            25000 => 17,
            30000 => 15
        );

        $this->maxMargin = 10;
    }

    public function getExcludedCats(){
        return $this->excludedCats;
    }

    public function setExcludedCats(){
        $this->excludedCats = array(
            'совместимые расходные материалы наценка' => 100,
            'оригинальные расходные материалы наценка' => 15,
            'все виды бумаги' => 17,
            'все устройства печать' => 10
        );
    }
}
