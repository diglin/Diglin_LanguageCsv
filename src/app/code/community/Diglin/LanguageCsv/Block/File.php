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
class Diglin_LanguageCsv_Block_File extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('diglin/languagecsv/list.phtml');
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setChild('createButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('languagecsv')->__('Create CSV Language File'),
                    'onclick' => "csvFile.selectModule(this)",
                    'class'  => 'task'
                ))
        );
        $this->setChild('languagecsvsGrid',
            $this->getLayout()->createBlock('languagecsv/file_grid')
        );
    }

    public function getCreateButtonHtml()
    {
        return $this->getChildHtml('createButton');
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('languagecsvsGrid');
    }
}