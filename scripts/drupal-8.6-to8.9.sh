#!/bin/bash

composer install
echo "update step 1"
vendor/drush/drush/drush updb
vendor/drush/drush/drush cr
echo "update step 2"
rm -rf modules/context modules/geolocation modules/modal_page modules/paragraphs
vendor/drush/drush/drush updb 
vendor/drush/drush/drush cr