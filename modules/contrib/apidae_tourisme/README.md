# Apidae Tourisme

# Prerequisite
  - telephone
  - geolocation (3.x branche for drupal 9)
  - address
  - link

## Installation
Download this module with composer and install it with drush or through the UI.

If the module content_tranlation is not installed, remember to update entities

## Configuration
Head to /admin/config/services/apidae

You'll need information from your apidae backoffice :

  - API Key
  - Project ID
  - Selections list IDs (commas separated)

If the module content_translation is enable, you can enable the translation of
object_touristique bundle and therefore the translations will be imported.
