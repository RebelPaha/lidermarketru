<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Paha
 * Date: 25.11.12
 * Time: 0:22
 * To change this template use File | Settings | File Templates.
 */
abstract class ExcelReader
{
    public $inputFile;
    public $outputFile;
    public $outputDir;
    public $firstRowNum;
    public $idColumnNum;
    public $rowsQty;
    public $allowedSheets = array( 1 );
    protected $_outputFileHandle;
    protected $_inputDir;
    protected $_extractor;

    public function __construct( $inputFile, $outputDir ){
        $this->setInputFile(   $inputFile );
        $this->setOutputDir(   $outputDir );

        $this->init();
    }

    static function getExcelReaderName( $inputFile ){
        $ext = strtoupper( substr( strrchr( $inputFile, '.' ), 1 ) );

        return 'ExcelReader_' . $ext;
    }

    public function getInputFile(){
        return $this->inputFile;
    }

    public function setInputFile( $inputFile ){
//        var_dump( $inputFile );
        $inputFile = strtolower( $inputFile );

        if( !file_exists( $inputFile ) )
            throw new Exception( "File '$inputFile' not exists." );

        if( substr( strrchr( $inputFile, '.' ), 1 ) !== $this->ext )
            throw new Exception( "File '$inputFile' is not supported." );

        $this->inputFile = $inputFile;
    }

    public function getOutputFile(){
        return $this->outputFile;
    }

    public function setOutputFile( $outputFile ){
        if( $outputFile === null )
            $this->outputFile = pathinfo( $this->getInputFile(), PATHINFO_FILENAME );
        else
            $this->outputFile = $outputFile;
    }

    public function getOutputDir(){
        return $this->outputDir;
    }

    public function setOutputDir( $outputDir ){
        $this->outputDir = $outputDir;
    }

    public function getFirstRowNum(){
        return $this->firstRowNum;
    }

    public function setFirstRowNum( $firstRowNum ){
        if( !is_numeric( $firstRowNum ) )
            throw new Exception( 'First row number must be a number. ' . getType( $firstRowNum ) . ' passed' );

        $this->firstRowNum = $firstRowNum;
    }

    public function getIdColumnNum(){
        return $this->idColumnNum;
    }

    public function setIdColumnNum( $idColumnNum ){
        if( !is_numeric( $idColumnNum ) )
            throw new Exception( 'ID column number must be a number. ' . getType( $idColumnNum ) . ' passed' );

        $this->idColumnNum = $idColumnNum;
    }

    public function getAllowedSheets(){
        return $this->allowedSheets;
    }

    public function setAllowedSheets( $sheets ){
        if( is_array( $sheets) && !empty( $sheets ) && ( $sheets[0] !== 0 )   )
            $this->allowedSheets = $sheets;
    }

    public function addAllowedSheet( $sheetNum ){
        if( !in_array( $sheetNum, $this->allowedSheets ) )
            $this->allowedSheets[] = $sheetNum;
    }

    public function getExtractor(){
        return $this->_extractor;
    }

    public function setExtractor( $extractorName ){
        $this->_extractor = $extractorName;
    }

    public function deleteSource(){
        unlink( $this->getInputFile() );
    }

    protected function getInputDir(){
        return $this->_inputDir;
    }

    public function convertStr( $str, $reverse = false ){
        $encodings = array( 'UTF-8', 'UTF-8' );

        if( $reverse )
            sort( $encodings );

        return iconv( $encodings[0], $encodings[1], $str );
    }

    abstract function init();
}