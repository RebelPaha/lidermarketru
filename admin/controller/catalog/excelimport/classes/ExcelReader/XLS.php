<?php

abstract class ExcelReader_XLS extends ExcelReader
{
    public $ext = 'xls';
    public $data;
    public $priceIntervals;
    public $excludedCats = array();

    public function init(){
        $this->data = new Spreadsheet_Excel_Reader( $this->getInputFile(), false );
        $this->data->setUTFEncoder( 'mb' );
    }

    abstract function run( $model, $category, $languages );

    abstract function getPriceIntervals();

    abstract function setPriceIntervals();

    abstract function getExcludedCats();

    abstract function setExcludedCats();

    public function fillEmptyCells( $cells, $length ){
        for( $index = 1; $index <= $length; $index++ ){
            if( !isset( $cells[ $index ] ) ){
                $cells[ $index ] = '';
            }
        }

        ksort($cells);

        return $cells;
    }

    public function margin( $price, $intervals ){
        if( in_array( key( $intervals ), array( '%', '+' ) )){
            $intervals = array( 'over' => $intervals );
        }

        foreach( $intervals as $key => $value ){
            if( $price < $key || $key === 'over' ){
                foreach( $value as $op => $num ){
                    if( $op === '%' )
                        $price += $price * ( $num / 100 );
                    elseif( $op === '+' )
                        $price += $num;
                }

                break;
            }
        }

        return $price;
    }

    public function getPriceWithMargin( $price, $cats = array() ){
        $price = (float) $price;

        if( $price == 0 )
            return 0;

        $excludedCats = $this->getExcludedCats();

        if( !empty( $cats ) ){
            // Проход по категориям, начиная с самого нижнего уровня вложености
            foreach( $cats as $cat ){
                $cat = mb_strtolower( $cat, 'UTF-8' );

                if( array_key_exists( $cat, $excludedCats ) ){
                    $price = $this->margin( $price, $excludedCats[ $cat ] );
//                    var_dump( $price, $excludedCats[ $cat ] );

                    return ceil( $price );
                }
            }
        }

        ///  Если в исключенных категориях нет совпадений, то учитываем ценовой промежуток
        $price = $this->margin( $price, $this->getPriceIntervals() );

        return ceil( $price );
    }
}
