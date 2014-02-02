Diglin LanguageCsv
==================

Developer Magento module to extract strings to be translated of your module from PHP and XML files like layout files, config.xml, system.xml, ...

This module is based on the original module of OSDave https://bitbucket.org/OSdave/languagecsv.
My version improves some behavior and fixes some bug (refer to the changelog).

# Documentation #

## Installation ##

- Use modman `modman clone https://github.com/diglin/Diglin_LanguageCsv.git`

### Via modman
- Install [modman](https://github.com/colinmollenhour/modman)
- Use the command from your Magento installation folder: `modman clone https://github.com/diglin/Diglin_LanguageCsv.git`

### Via composer
- Install [composer](http://getcomposer.org/download/)
- Create a composer.json into your project like the following sample:

```json
{
    ...
    "require": {
        "diglin/diglin_languagecsv":"*"
    },
    "repositories": [
	    {
            "type": "composer",
            "url": "http://packages.firegento.com"
        }
    ],
    "extra":{
        "magento-root-dir": "./"
    }
}

```

- Then from your composer.json folder: `php composer.phar install` or `composer install`

### Manually
- You can copy the files from the folders of this repository to the same folders of your installation

## Use ##

- After to have refresh your cache and to have log out / log in from the backend, 
- Go to the backend of Magento, menu System > Tools > Language CSV. If you don't see it, check that the module is enabled: app/etc/modules/Osdave_LanguageCsv.xml
- Click on the button "Create CSV language file"
- Select the module to translate and the admin and frontend template folders of the module. Layout and other XML files should be automatically detected. 
- You can then download the file or lookup into the folder var/languagecsv/ to get the CSV file with strings to translate

# Change Log #

- Format the code following Zend Framework Coding Standards
- Parse also all XML files of a module (config.xml, system.xml, api.xml, layout XML files - frontend and backend, etc) to extract strings to be translated,
Improved XML parsing
- Exclude SVN and git hidden folders
- Fix wrong filesize logic for CSV file in controller. It makes some trouble to download the file.
- Improve drop down menu display for template files cause of display errors on Safari (padding not working in drop down)
- Some code improvements to prevent modules code pool discovery errors
