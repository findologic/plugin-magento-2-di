# Findologic Magento 2 DI Plugin

For the functionality of the plugin, it's essential to export the product data from the shop to Findologic. For this purpose, the Findologic export library [libflexport](https://github.com/findologic/libflexport) is included. By default the export contains only demo product data.

## Installation

  * Plug-in content needs to copied into “app/code” folder
  * Run `composer require findologic/libflexport` in your project directory
  * Run `bin/magento module:status` to get status of all available modules
  * `Findologic_Search` module should be listed in the bottom of the list as disabled module
  * In order to enable module type `bin/magento module:enable Findologic_Search`

  **Note**: Depending on permission setting may need root privileges

  * The plugin should be enabled you can verify it with `bin/magento module:status`
    * `Findologic_Search` should be listed as enabled module
  * Log-in into Admin panel
  * Click on *Stores* and in *Settings* choose *Configuration*
  * Click on *FINDOLOGIC* on the left side menu, choose desired store view, and enter shop key provided by Findologic and click *Save Config* 
    * Note: Shop key must be entered in valid format or error will be shown
  * Clear the Magento shop cache

## Product Export

  * Call `https://<shop-domain>/search/Export/ExportController?shopkey=ABCD&count=20&start=0`

  Required parameters:
  * `shopkey` → Provided by Findologic
  * `start` → number > 0 
  * `count` → number > 0
  

## Release

1. Go to directory `Findologic` and run `composer install --no-dev`.
1. Create a zip file named `FindologicSearch-x.x.x.zip` that includes all contents of the `Findologic/Search` folder.
  * Be aware to neither include directory `Findologic` nor `Search` in the zip file.
1. Go to https://developer.magento.com/extensions/ and upload new version
