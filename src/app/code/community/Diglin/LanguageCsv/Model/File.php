<?php
/**
 * Diglin
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Diglin
 * @package     Diglin_LanguageCsv
 * @author      Osdave <david . parloir AT gmail.com>
 * @copyright   Copyright (c) 2011-2014 Diglin (http://www.diglin.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Description of File
 *
 * @author david
 */
class Diglin_LanguageCsv_Model_File extends Varien_Object
{
    const LANGUAGE_FILE_EXTENSION          = 'csv';
    const MODULE_DEFINITION_FILE_EXTENSION = 'xml';
    const PATTERN_QUOTE_SINGLE             = '/__\(\'(.+?)(\'\)|\',)/';
    const PATTERN_QUOTE_DOUBLE             = '/__\("(.+?)("\)|",)/';
    const PATTERN_SYSTEM_TAGS_TO_TRANSLATE = '/ translate="(.+?)"/';
    const PATTERN_SYSTEM_TAGS_NAME         = '/<(.+?) /';

    /**
     * file pointer
     *
     * @var resource
     */
    protected $_handler = null;

    private $_endingTag;

    private $_tagsToTranslate;

    private $_stringsInFile = array();

    /**
     * Load language file info
     *
     * @param string fileName
     * @param string filePath
     * @return Diglin_LanguageCsv_Model_File
     */
    public function load($fileName, $filePath)
    {
        list ($time, $type) = explode("_", substr($fileName, 0, strrpos($fileName, ".")));
        $this->addData(array(
            'id' => $filePath . DS . $fileName ,
            'name' => $fileName ,
            'path' => $filePath
        ));
        //        $this->setType($type);
        return $this;
    }

    public function getFileName()
    {
        return $this->getName() . '.' . self::LANGUAGE_FILE_EXTENSION;
    }

    /**
     * Checks language csv file exists.
     *
     * @return boolean
     */
    public function exists()
    {
        return is_file($this->getPath() . DS . $this->getName());
    }

    /**
     * Print output
     *
     */
    public function output()
    {
        if (! $this->exists()) {
            return;
        }
        
        $ioAdapter = new Varien_Io_File();
        $ioAdapter->open(array(
            'path' => $this->getPath()
        ));
        
        $ioAdapter->streamOpen($this->getName(), 'r');
        while ($buffer = $ioAdapter->streamRead()) {
            echo $buffer;
        }
        $ioAdapter->streamClose();
    }

    public function createLanguageCsvFile($languagecsv, $frontendTemplateRootFolder, $adminTemplateRootFolder)
    {
        $languagecsv->open(true);
        
        //1. get all files from module
        //1.1. get all php files
        $moduleFiles = $languagecsv->getModuleFiles();
        $languagecsv->extractStrings($moduleFiles, 'php');
        //1.2. get all phtml files
        if (! is_null($frontendTemplateRootFolder) && ($frontendTemplateRootFolder != '')) {
            $frontendPhtmlFiles = $languagecsv->getTemplateFiles($frontendTemplateRootFolder);
            $languagecsv->extractStrings($frontendPhtmlFiles, 'phtml');
        }
        if (! is_null($adminTemplateRootFolder) && ($adminTemplateRootFolder != '')) {
            $adminPhtmlFiles = $languagecsv->getTemplateFiles($adminTemplateRootFolder);
            $languagecsv->extractStrings($adminPhtmlFiles, 'phtml');
        }
        
        $languagecsv->close();
    }

    public function getModuleFiles()
    {
        $codePool = $this->_getCodePool();
        $module = str_replace('_', DS, $this->getName());
        $pathToDirectory = Mage::getBaseDir('code') . DS . $codePool . DS . $module . DS;
        
        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pathToDirectory));
        
        return $objects;
    }

    public function getTemplateFiles($rootFolder)
    {
        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootFolder));
        
        return $objects;
    }

    public function extractStrings($objects, $type = null)
    {
        foreach ($objects as $file => $object) {
            
            if (strpos($file, '.svn') !== false || strpos($file, '.git') !== false) {
                continue;
            }
            
            if (strpos($object->getFilename(), 'xml') !== false && $object->getFilename() != 'wsdl.xml' && $object->getFilename() != 'wsdl2.xml') {
                
                // Extract strings to translate from XML files like config.xml, system.xml, api.xml, wsdl.xml etc... 
                $this->extractXml($file);

                
                // Diglin - Add logic to get the layout XML files of the module
                // @todo - to improve in case of several layout files for one module which should be an extreme rare case
                if ($object->getFilename() == 'config.xml') {
                    $config = new Varien_Simplexml_Config($file);
                    $frontendPath = $config->getXpath('frontend/layout/updates/*/file');
                    $adminPath = $config->getXpath('adminhtml/layout/updates/*/file');
                    
                    if (is_array($frontendPath) && count($frontendPath) == 1) {
                        $filename = Mage::getModel('core/design_package')->getFilename($frontendPath[0][0], array('_type' => 'layout'));
                        if (file_exists($filename)) {
                            $this->extractXml($filename);
                        }
                    }
                    
                    if (is_array($adminPath) && count($adminPath) == 1) {
                        $filename = Mage::getModel('core/design_package')->getFilename($adminPath[0][0], array('_type' => 'layout'));
                        if (file_exists($filename)) {
                            $this->extractXml($filename);
                        }
                    }
                    
                }
                
            } else if (! is_null($type) && (substr(strrchr($file, '.'), 1) == $type) || is_null($type)) {
                //parse file
                $file_handle = fopen($file, "r");
                while (! feof($file_handle)) {
                    $line = fgets($file_handle);
                    preg_match_all(self::PATTERN_QUOTE_SINGLE, $line, $singleQuoteMatches);
                    if (sizeof($singleQuoteMatches[1])) {
                        foreach ($singleQuoteMatches[1] as $string) {
                            $string = str_replace('"', '""', $string);
                            $this->write('"' . $string . '","' . $string . "\"\r\n");
                        }
                    }
                    preg_match_all(self::PATTERN_QUOTE_DOUBLE, $line, $doubleQuoteMatches);
                    if (sizeof($doubleQuoteMatches[1])) {
                        foreach ($doubleQuoteMatches[1] as $string) {
                            $this->write('"' . $string . '","' . $string . "\"\r\n");
                        }
                    }
                }
                fclose($file_handle);
            }
        }
    }
    
    /**
     * 
     * Extract string to translate from XML files having translate attributes
     * This solution is compatible with all xml files using this format
     * <PARENTTAG translate='childtag childtag2' module='anymodule'><childtag>Text to translate</childtag><childtag2>My comment for example</childtag2></PARENTTAG>
     * 
     * @author Diglin
     * @param string $file exple /root/home/web/app/code/community/MyNamespace/MyModule/etc/config.xml
     */
    public function extractXml ($file) 
    {
        if (file_exists($file)) {
            $xml = new Varien_Simplexml_Config($file);
            $stringXml = $xml->getXmlString();
            
            $parent = new Varien_Simplexml_Element($stringXml);
            $nodes = $parent->xpath('//*[@translate]');
            foreach ($nodes as $node) {
                //$module = $node['module'];
                $attribute = explode(' ', $node['translate']); // a node with an attribute translate can have several values. exple: translate="label content"
                foreach ($attribute as $value) {
                    $tagStringToTranslate = $node->$value;
                    $string = str_replace('"', '""', $tagStringToTranslate);
                    $this->write('"' . $string . '","' . $string . "\"\r\n");
                }
            }
        }
        return;
    }

    private function _getCodePool()
    {
        $moduleName = $this->getName();
        $moduleXmlFile = Mage::getConfig()->getNode('modules')->children();
        return (string) $moduleXmlFile->$moduleName->codePool;
    }

    public function open($write = false)
    {
        if (is_null($this->getPath())) {
            Mage::exception('Diglin_LanguageCsv', Mage::helper('languagecsv')->__('Language CSV file path was not specified.'));
        }
        
        $ioAdapter = new Varien_Io_File();
        try {
            $path = $ioAdapter->getCleanPath($this->getPath());
            $ioAdapter->checkAndCreateFolder($path);
            $filePath = $path . DS . $this->getFileName();
        } catch (Exception $e) {
            Mage::exception('Diglin_LanguageCsv', $e->getMessage());
        }
        
        if ($write && $ioAdapter->fileExists($filePath)) {
            $ioAdapter->rm($filePath);
        }
        if (! $write && ! $ioAdapter->fileExists($filePath)) {
            Mage::exception('Diglin_LanguageCsv', Mage::helper('languagecsv')->__('Language CSV file "%s" does not exist.', $this->getFileName()));
        }
        
        $mode = $write ? 'a+' : 'r';
        
        try {
            $this->_handler = fopen($filePath, $mode);
        } catch (Exception $e) {
            Mage::exception('Diglin_LanguageCsv', Mage::helper('languagecsv')->__('Language CSV file "%s" cannot be read from or written to.', $this->getFileName()));
        }
        
        return $this;
    }

    /**
     * Write to file
     *
     * @param string $string
     * @return Diglin_LanguageCsv_Model_File
     */
    public function write($string)
    {
        if (is_null($this->_handler)) {
            Mage::exception('Diglin_LanguageCsv', Mage::helper('languagecsv')->__('Language csv file handler was unspecified.'));
        }
        
        try {
            if (! in_array($string, $this->_stringsInFile)) {
                gzwrite($this->_handler, $string);
                $this->_stringsInFile[] = $string;
            }
        } catch (Exception $e) {
            Mage::exception('Diglin_LanguageCsv', Mage::helper('languagecsv')->__('An error occurred while writing to the language csv file "%s".', $this->getFileName()));
        }
        
        return $this;
    }

    /**
     * Close open Language CSV file
     *
     * @return Diglin_LanguageCsv_Model_File
     */
    public function close()
    {
        @fclose($this->_handler);
        $this->_handler = null;
        
        return $this;
    }

    /**
     * Delete language csv file
     *
     * @throws Diglin_LanguageCsv_Exception
     */
    public function deleteFile()
    {
        if (! $this->exists()) {
            Mage::throwException(Mage::helper('languagecsv')->__("CSV Language file does not exist."));
        }
        
        $ioProxy = new Varien_Io_File();
        $ioProxy->open(array(
            'path' => $this->getPath()
        ));
        $ioProxy->rm($this->getName());
        return $this;
    }
}