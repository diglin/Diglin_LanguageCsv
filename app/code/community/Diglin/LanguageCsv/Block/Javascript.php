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
 * Description of Javascript
 *
 * @author david
 */
class Diglin_LanguageCsv_Block_Javascript extends Mage_Adminhtml_Block_Abstract
{
    const TEMPLATE_DEPTH = 2; // the template folders are in level 2 (0 based): base/default/template

    private $_modulesFolders = array();

    public function getCreateFileUrl()
    {
        return $this->getUrl('*/*/create');
    }

    public function getModulesList()
    {
        $modules = array_keys((array) Mage::getConfig()->getNode('modules')->children());
        
        sort($modules);
        
        $modulesList = array();
        foreach ($modules as $moduleName) {
            if ($moduleName === 'Mage_Adminhtml') {
                continue;
            }
            $modulesList[] = $moduleName;
        }
        
        return $modulesList;
    }

    public function getTemplateFolders($section)
    {
        $folders = array();
        $rootFolderPath = Mage::getBaseDir('design') . DS . $section . DS;
        $rootFolder = opendir($rootFolderPath);
        
        while (false !== ($package = readdir($rootFolder))) { //app/design/adminhtml_or_frontend/package/
            if (is_dir($rootFolderPath . $package) && ! $this->_linuxDir($package)) {
                $packageFolder = opendir($rootFolderPath . $package . DS);
                
                while (false !== ($theme = readdir($packageFolder))) { //app/design/adminhtml_or_frontend/package/theme/
                    if (is_dir($rootFolderPath . $package . DS . $theme . DS) && ! $this->_linuxDir($theme)) {
                        $containerFolderPath = $rootFolderPath . $package . DS . $theme . DS . 'template' . DS;
                        if (is_dir($containerFolderPath)) { //app/design/adminhtml_or_frontend/package/theme/template/
                            $this->_modulesFolders = array(); //reinitiate
                            $folders[] = $this->_getModulesFolders($containerFolderPath, $package, $theme);
                            sort($folders[sizeof($folders) - 1][$package . DS . $theme]);
                        }
                    }
                }
            }
        }
        
        //	Zend_Debug::dump($folders, 'debug');
        

        return $folders;
    }

    private function _getModulesFolders($containerFolderPath, $package, $theme)
    {
        $maxDepth = Mage::getStoreConfig('dev/languagecsv/tree_depth');
        
        $themeFolder = opendir($containerFolderPath);
        while (false !== ($folder = readdir($themeFolder))) { //app/design/adminhtml_or_frontend/package/theme/template/module
            if (is_dir($containerFolderPath . $folder) && ! $this->_linuxDir($folder)) {
                $actualPath = $containerFolderPath . $folder;
                $lengthToThemeFolder = strpos($actualPath, $package . DS . $theme . DS) + strlen($package . DS . $theme . DS);
                $pathFromThemeFolder = substr($actualPath, $lengthToThemeFolder);
                $currentDepth = substr_count($pathFromThemeFolder, DS);
                
                $this->_modulesFolders[$package . DS . $theme][] = array(
                    'value' => $actualPath , 'label' => str_repeat("--", $currentDepth - 1) . ' ' . $folder , 
                    'depth' => $currentDepth
                );
                
                if ($currentDepth < $maxDepth) {
                    $this->_getModulesFolders($actualPath . DS, $package, $theme);
                }
            }
        }
        
        return $this->_modulesFolders;
    }

    private function _linuxDir($package)
    {
        if (($package == '.') || ($package == '..') || ($package == '.svn')) {
            return true;
        }
        
        return false;
    }
}