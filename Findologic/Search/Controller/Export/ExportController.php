<?php

namespace Findologic\Search\Controller\Export;

use FINDOLOGIC\Export\Exporter;
use Findologic\Export\Helper\ExportHelper;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Module\Manager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

class ExportController extends Action
{
    /**
     * @var string
     */
    private $shopKey;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var int
     */
    private $count;

    /**
     * @var int
     */
    private $start;

    /**
     * @var Collection
     */
    private $productsCollection;

    /**
     * @var string
     */
    private $validationMessage;

    /**
     * @var Raw
     */
    private $rawResponse;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var ExportHelper|null
     */
    private $exportHelper;

    /**
     * @param Context $context
     * @param Collection $productsCollection
     * @param Raw $rawResponse
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param Manager $moduleManager
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Context $context,
        Collection $productsCollection,
        Raw $rawResponse,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        Manager $moduleManager,
        ObjectManagerInterface $objectManager
    ) {
        parent::__construct($context);

        $this->productsCollection = $productsCollection;
        $this->rawResponse = $rawResponse;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->moduleManager = $moduleManager;

        // Use ObjectManager to manually get the helper, to avoid an exception, in case the export plugin
        // is not yet installed.
        if (class_exists(ExportHelper::class)) {
            $this->exportHelper = $objectManager->get('Findologic\Export\Helper\ExportHelper');
        }
    }

    /**
     * Exports data using shop key.
     */
    public function execute()
    {
        $request = $this->getRequest();
        $this->shopKey = $request->getParam('shopkey', false);
        $this->start = $request->getParam('start', false);
        $this->count = $request->getParam('count', false);
        $this->storeId = $this->getStoreId($this->shopKey);

        if (!$this->validateInput()) {
            return $this->rawResponse
                ->setHeader('Content-type', 'text/plain')
                ->setContents($this->validationMessage);
        }

        if (!class_exists(Exporter::class)) {
            return $this->rawResponse
                ->setHeader('Content-type', 'text/plain')
                ->setContents('Run "composer require findologic/libflexport" in your project directory');
        }

        if (!$this->moduleManager->isEnabled('Findologic_Export')) {
            return $this->rawResponse
                ->setHeader('Content-type', 'text/plain')
                ->setContents('The Findologic export plugin is not installed! You can download it here: ' .
                    'https://docs.findologic.com/lib/exe/fetch.php' .
                    '?media=integration_documentation:plugins:magento_2_export_plugin.zip'
                );
        }

        $export = $this->exportHelper->buildExportInstance();
        $xml = $export->startExport($this->shopKey, $this->start, $this->count);

        return $this->rawResponse
            ->setHeader('Content-type', 'application/xml')
            ->setContents($xml);
    }

    /**
     * Validates weather all input parameters are supplied.
     *
     * @return bool
     */
    private function validateInput()
    {
        $this->validationMessage = '';

        if (!$this->shopKey) {
            $this->validationMessage = 'Parameter "shopkey" is missing! ';
        }

        if (!$this->storeId) {
            $this->validationMessage .= 'Parameter "shopkey" is not configured for any store! ';
        }

        if (!is_numeric($this->start)) {
            $this->validationMessage .= 'Parameter "start" must be numeric! ';
        }

        if ($this->start === false || $this->start < 0) {
            $this->validationMessage .= 'Parameter "start" is missing or less than 0! ';
        }

        if (!is_numeric($this->count)) {
            $this->validationMessage .= 'Parameter "count" must be numeric! ';
        }

        if (!$this->count || $this->count < 1) {
            $this->validationMessage .= 'Parameter "count" is missing or less than 1! ';
        }

        return (empty($this->validationMessage)) ? true : false;
    }

    /**
     * Get store id for specified shop key.
     *
     * @param string $shopKey
     * @return boolean|integer store id if found; otherwise, false.
     */
    private function getStoreId($shopKey)
    {
        $stores = $this->storeManager->getStores();

        foreach ($stores as $store) {
            $storeId = $store->getId();

            $keyValue = $this->scopeConfig->getValue(
                'findologic/findologic_group/shopkey',
                ScopeInterface::SCOPE_STORE,
                $storeId
            );

            if ($keyValue === $shopKey) {
                return $storeId;
            }
        }

        return false;
    }
}
