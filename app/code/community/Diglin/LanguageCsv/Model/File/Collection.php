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
 * Description of Collection
 *
 * @author david
 */
class Diglin_LanguageCsv_Model_File_Collection extends Varien_Data_Collection_Filesystem
{

    /**
     * Folder, where all language files are stored are stored
     *
     * @var string
     */
    protected $_baseDir;

    /**
     * Set collection specific parameters and make sure language files folder will exist
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->_baseDir = Mage::getBaseDir('var') . DS . 'languagecsv';
        
        // check for valid base dir
        $ioProxy = new Varien_Io_File();
        $ioProxy->mkdir($this->_baseDir);
        if (! is_file($this->_baseDir . DS . '.htaccess')) {
            $ioProxy->open(array(
                'path' => $this->_baseDir
            ));
            $ioProxy->write('.htaccess', 'deny from all', 0644);
        }
        
        // set collection specific params
        $this->setOrder('name', self::SORT_ORDER_ASC)
            ->addTargetDir($this->_baseDir)
            ->setFilesFilter('/^[a-zA-Z0-9\-\_]+\.' . preg_quote(Diglin_LanguageCsv_Model_File::LANGUAGE_FILE_EXTENSION, '/') . '$/')
            ->setCollectRecursively(false);
    }

    /**
     * Get language files-specific data from model for each row
     *
     * @param string $filename
     * @return array
     */
    protected function _generateRow($filename)
    {
        $row = parent::_generateRow($filename);
        foreach (Mage::getSingleton('languagecsv/file')->load($row['basename'], $this->_baseDir)->getData() as $key => $value) {
            $row[$key] = $value;
        }
        $row['size'] = filesize($filename);
        return $row;
    }
}