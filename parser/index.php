<?php

ini_set( 'memory_limit', '256M' );
ini_set( 'display_errors', true );

error_reporting( E_ALL ^ E_NOTICE );
set_time_limit( 0 );

define( 'PATH_ROOT', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );

require_once( 'classes/ExcelReader.php' );
require_once( 'classes/ExcelReader/XLS.php' );
require_once( 'classes/ExcelReader/XLS_TYpe1.php' );


$inputDir  = PATH_ROOT . 'files' . DIRECTORY_SEPARATOR;
$inputFile = $inputDir . '1.xls';

$excelReader = new ExcelReader_XLS_Type1( $inputFile, $inputDir, 3, 3  );

//$margin = $excelReader->getPriceMargin(  30001, 'оригинальные расходные материалы наценка' );
//var_dump( $margin );
$excelReader->convert();

var_dump( memory_get_peak_usage() );