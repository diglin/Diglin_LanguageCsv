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
 * Description of Grid
 *
 * @author david
 */
class Diglin_LanguageCsv_Block_File_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        $this->setSaveParametersInSession(true);
        $this->setId('languagecsvsGrid');
        $this->setDefaultSort('name', 'asc');
    }

    /**
     * Init CSV Languages Files collection
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getSingleton('languagecsv/file_collection');
//        Mage::log($collection->load(), null, 'debug.log', true);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('file', array(
            'header'    => Mage::helper('languagecsv')->__('File'),
            'format'    => '<a href="' . $this->getUrl('*/*/download', array('name' => '$name')) .'">$name</a>',
            'index'     => 'name',
            'sortable'  => false,
            'filter'    => false
        ));

        $this->addColumn('action', array(
            'header'    => Mage::helper('languagecsv')->__('Action'),
            'type'      => 'action',
            'width'     => '80px',
            'filter'    => false,
            'sortable'  => false,
            'actions'   => array(array(
                'url'       => $this->getUrl('*/*/delete', array('name' => '$name')),
                'caption'   => Mage::helper('adminhtml')->__('Delete'),
                'confirm'   => Mage::helper('adminhtml')->__('Are you sure you want to do this?')
            )),
            'index'     => 'type',
            'sortable'  => false
        ));

        return $this;
    }

}
