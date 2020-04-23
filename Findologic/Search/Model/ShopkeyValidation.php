<?php

namespace Findologic\Search\Model;

use Magento\Framework\App\Config\Value;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ShopkeyValidation extends Value
{
    private $_request;
    private $_store_manager;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->_request = $request;
        $this->_store_manager = $storeManager;
        $this->_scope_config = $scopeConfig;
    }

    /**
     * Process additional data before save config
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $shopKey = trim($this->getValue());
        if ($shopKey) {
            // Get id of currently edited shop.
            $currentId = $this->_request->getParam('store');
            
            // Get all stores from site.
            $stores = $this->_store_manager->getStores();

            // CurrentId is set only if admin editing store's data, otherwise he is editing
            // main website or default configuration.
            // Uncomment this condition if you want to enable main website or default configuration
            // to have same shop key as some store.
            // if (isset($currentId)) {
                // Check if any store has same shopkey and throws exception.
                foreach ($stores as $store) {
                    $storeId = $store->getId();

                    // Skip check for current store, we only need to check for other stores.
                    if (isset($currentId) && $currentId === $storeId) {
                        continue;
                    }

                    $keyValue = $this->_scope_config->getValue(
                            'findologic/findologic_group/shopkey',
                         ScopeInterface::SCOPE_STORE,
                                   $storeId
                    );

                    if ($keyValue === $shopKey) {
                        throw new LocalizedException(
                            __('Shop key already exists! Each store view must have its own shop key.')
                        );
                    }
                }
            //}
            
            // Check if shopkey is in valid format.
            if (!preg_match('/^[A-Z0-9]{32}$/', $shopKey)) {
                throw new LocalizedException(__('Shop key format is not valid!'));
            }
            
            $this->setValue(trim($shopKey));
        }

        return $this;
    }
}
