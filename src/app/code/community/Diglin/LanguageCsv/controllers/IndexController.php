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
 * Description of IndexController
 *
 * @author david
 */
class Diglin_LanguageCsv_Adminhtml_Languagecsv_IndexController extends Mage_Adminhtml_Controller_Action
{

    /**
     * CSV Languages Files list action
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))
            ->_title($this->__('Tools'))
            ->_title($this->__('Language csv'));
        
        if ($this->getRequest()->getParam('ajax')) {
            $this->_forward('grid');
            return;
        }
        
        $this->loadLayout();
        $this->_setActiveMenu('system');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('System'), Mage::helper('adminhtml')->__('System'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Tools'), Mage::helper('adminhtml')->__('Tools'));
        $this->_addBreadcrumb(Mage::helper('languagecsv')->__('Language csv'), Mage::helper('languagecsv')->__('CSV Languages Files'));
        
        $this->_addContent($this->getLayout()
            ->createBlock('languagecsv/file', 'languagecsvfile'));
        
        $this->renderLayout();
    }

    /**
     * CSV Languages Files list action
     */
    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()
            ->createBlock('languagecsv/file_grid')
            ->toHtml());
    }

    /**
     * Create Language File action
     */
    public function createAction()
    {
        try {
            $module = $this->getRequest()->getParam('module');
            $languagecsv = Mage::getModel('languagecsv/file')->setPath(Mage::getBaseDir("var") . DS . "languagecsv")->setName($module);
            
            Mage::register('languagecsv_model', $languagecsv);
            
            $frontendTemplateRootFolder = $this->getRequest()->getParam('frontend', null);
            $adminTemplateRootFolder = $this->getRequest()->getParam('adminhtml', null);
            
            $languagecsv->createLanguageCsvFile($languagecsv, $frontendTemplateRootFolder, $adminTemplateRootFolder);
            $this->_getSession()->addSuccess(Mage::helper('languagecsv')->__('The csv language file has been created.'));
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $e->getMessage());
            $this->_getSession()->addException($e, Mage::helper('languagecsv')->__('An error occurred while creating the csv language file.'));
        }
        $this->_redirect('*/*');
    }

    /**
     * Download Language File action
     */
    public function downloadAction()
    {
        $fileName = $this->getRequest()->getParam('name');
        $path = Mage::getBaseDir('var') . DS . 'languagecsv';
        
        $languageCsvFile = Mage::getModel('languagecsv/file')
            ->setName($fileName)
            ->setPath($path);
        
        if (! $languageCsvFile->exists()) {
            $this->_redirect('*/*');
        }
        $this->_prepareDownloadResponse($fileName, null, 'application/octet-stream', filesize($path . DS . $fileName));
        
        $this->getResponse()->sendHeaders();
        
        $languageCsvFile->output();
        exit();
    }

    /**
     * Delete Language File action
     */
    public function deleteAction()
    {
        try {
            $fileName = $this->getRequest()->getParam('name');
            $languageCsvFile = Mage::getModel('languagecsv/file')->setName($fileName)
                ->setPath(Mage::getBaseDir("var") . DS . "languagecsv")
                ->deleteFile();
            
            Mage::register('languagecsv_model', $languageCsvFile);
            
            $this->_getSession()->addSuccess(Mage::helper('languagecsv')->__('CSV Languages Files record was deleted.'));
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        
        $this->_redirect('*/*/');
    }
}
