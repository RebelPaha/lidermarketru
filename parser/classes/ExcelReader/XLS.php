<?php

require_once( PATH_ROOT . 'classes/excel_reader2.php' );

abstract class ExcelReader_XLS extends ExcelReader
{
    public $ext = 'xls';
    public $data;

    public function init(){
        $this->data = new Spreadsheet_Excel_Reader( $this->getInputFile(), false, 'CP1251' );
    }

    abstract function convert();

    public function fillEmptyCells( $cells, $length ){
        for( $index = 1; $index <= $length; $index++ ){
            if( !isset( $cells[ $index ] ) ){
                $cells[ $index ] = '';
            }
        }

        ksort($cells);

        return $cells;
    }

    public function getPriceMargin( $price, $category = '' ){
        $margin   = null;
        $category = strtolower( $category );
        $cats     = $this->getExcludedCats();

        if( array_key_exists( $category, $cats ) ){
            return $cats[ $category ];
        }

        foreach( $this->getPriceIntervals() as $key => $value ){
            if( $price < $key ){
                $margin = $value;
                break;
            }
        }

        if( is_null( $margin ) ){
            $margin = $this->maxMargin;
        }

        return $margin;
    }

    public function convertPrice( $price, $category ){
        return ceil( $price + ( $price * ( $this->getPriceMargin( $price, $category ) / 100 ) ) );
    }
}
