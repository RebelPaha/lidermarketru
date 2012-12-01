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

    public function __construct( $inputFile, $outputDir, $firstRowNum = 1, $idColumnNum = 1, $outputFile = null ){
        $this->setInputFile(   $inputFile );
        $this->setOutputDir(   $outputDir );
        $this->setFirstRowNum( $firstRowNum );
        $this->setIdColumnNum( $idColumnNum );
        $this->setOutputFile(  $outputFile );

        $this->_outputFileHandle = fopen( $this->getOutputDir() . $this->getOutputFile() . '.csv', 'w' );

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

    abstract function init();

    //abstract function convert();

    protected function finish(){
        fclose( $this->_outputFileHandle );
        chmod( $this->getOutputDir() . $this->getOutputFile() . '.csv'  , 0777 );

        $file = $this->getOutputDir() . $this->getOutputFile() . '.csv';

        if( filesize( $this->getOutputDir() . $this->getOutputFile() . '.csv' ) === 0 )
            unlink( $file );

        $this->data = null;
    }
}