Diglin LanguageCsv
==================

Magento module to extract strings to translate from PHP files and XML files like (layout files, config.xml, system.xml, ...)
This module is based on the original module of OSDave https://bitbucket.org/OSdave/languagecsv

# Documentation #

## Installation ##

- Use modman `modman clone https://github.com/diglin/Diglin_LanguageCsv.git`

## Use ##

- After to have refresh your cache and to have log out / log in from the backend, 
- Go to the backend of Magento, menu System > Tools > Language CSV. If you don't see it, check that the module is enabled: app/etc/modules/Osdave_LanguageCsv.xml
- Click on the button "Create CSV language file"
- Select the module to translate and the admin and frontend template folders of the module. Layout and other XML files should be automatically detected. 
- You can then download the file or lookup into the folder var/languagecsv/ to get the CSV file with strings to translate
