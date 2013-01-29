Diglin LanguageCsv
==================

Magento module to extract strings to translate from PHP files and XML files like (layout files, config.xml, system.xml, ...)
This module is based on the original module of OSDave https://bitbucket.org/OSdave/languagecsv. My version improve some behavior and fixes some bug (refer to the changelog).

# Documentation #

## Installation ##

- Use modman `modman clone https://github.com/diglin/Diglin_LanguageCsv.git`

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
