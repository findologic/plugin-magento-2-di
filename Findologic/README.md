# FINDOLOGIC Magento 2 DI Plugin - libflexport only

## Installation

  FINDOLOGIC MAGENTO 2 plug-in installation procedure is basically the same as for any other MAGENTO 2 plug-in. It can be summed up in a few simple steps:
  * Plug-in content needs to copied into “app/code” folder
  * Run `composer require findologic/libflexport` in your project directory
  * After this, open terminal in “/bin” directory
  * Type `php magento module:status` to get status of all available modules
  * `Findologic_Search` module should be listed in the bottom of the list as disabled module
  * In order to enable module type `php magento module:enable Findologic_Search`

  **Note**: Maybe you will need to do this with root privileges

  * After this, module should be enabled, and if you type `php magento module:status`, `Findologic_Search` should be listed as enabled module
  * Log-in into Admin panel
  * Click on “Stores” and Under “Settings” menu click on “Configuration”
  * After this, on the left side menu “FINDOLOGIC” should be listed
  * Click on “FINDOLOGIC”, choose desired store view, and enter shop key provided by FINDOLOGIC and click “Save Config” Note: Shop key must be entered in valid format or error will be shown
  * Finally, shop's cache must be cleared

## Running export

  * Call https://shop.url/search/Export/ExportController?shopkey=ABCD&count=20&start=0.

  Three query parameters that are necessary for successfully running the export are:
  * shopkey → SHOPKEY is provided by FINDOLOGIC
  * start → number that should not be lower than zero
  * count → number that should not lower than zero and “start” number
  
  The export needs to be written by the customer. The export of the products is **not included**.
  By default you can find two demo products, when running the export.

## Deployment & Release

1. Go to directory `Findologic` and run `composer install --no-dev`.
1. Create a zip file named `FindologicSearch-x.x.x.zip` that includes all contents of the `Findologic` folder.
1. Go to https://developer.magento.com/extensions/ and select the plugin for the platform "M2".
1. Click on *Submit a New Version* and type the version constraint.
1. In the next step click on *Attach Package* and upload the created zip file.
1. For compatibility choose 2.0, 2.1, 2.2 and 2.3 (other versions are not tested yet).
 You may include a License, Release Notes, etc.
1. Click on *Submit* to submit the plugin. You may need to require a review.
