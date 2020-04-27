<?php
namespace Findologic\Search\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\Session;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\Group;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class TrackingScripts extends AbstractHelper
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var UsersGroup
     */
    private $usersGroup;

    /**
     * @var StoreManagerInterface
     */
    private $storeManagerInterface;

    /**
     * Used for caching categories retrieved from database.
     *
     * @var array
     */
    private static $categories = [];

    public function __construct(
        Context $context,
        Session $session,
        CustomerSession $customerSession,
        Group $group,
        StoreManagerInterface $storeManagerInterface
    ) {
        parent::__construct($context);

        $this->session = $session;
        $this->customerSession = $customerSession;
        $this->usersGroup = $group;
        $this->storeManagerInterface = $storeManagerInterface;
    }

    /**
     * Renders needed scripts for current page.
     *
     * @param Http $request
     * @return string
     */
    public function renderScripts(Http $request)
    {
        $findologic = $request->get('findologic');
        $result = '';

        if ($findologic != 'off' && $this->getShopKey()) {
            // Inserting js files for every page
            $result .= $this->headJs();
        }

        return $result;
    }

    /**
     * Get configuration data to start tracking
     */
    private function getConfData()
    {
        /** @var $session CustomerSession*/
        $customer = $this->customerSession->getCustomer();

        $userGroup = $this->usersGroup
            ->load((int)$customer->getGroupId())
            ->getCustomerGroupCode();
        $shopKey = $this->getShopKey();
        $userGroupHash = $this->userGroupToHash($shopKey, $userGroup);
        
        $this->data['USERGROUP_HASH'] = $this->customerSession->isLoggedIn() ? $userGroupHash : '';
        $this->data['HASHED_SHOPKEY'] = strtoupper(md5($shopKey));
    }

    /**
     * Returns tracking js script for head (all pages).
     *
     * @return string
     */
    private function headJs()
    {
        $navSelector = '.fl-navigation-result';
        $searchSelector = '.fl-result';

        $this->getConfData();

        return sprintf(
            '<script type="text/javascript">(function (f,i,n,d,o,l,O,g,I,c){' .
            'var V=[];' .
            'var m=f.createElement("style");' .
            'if(d){V.push(d)}' .
            'if(c&&I.location.hash.indexOf("#search:")===0){V.push(c)}' .
            'if(V.length>0){var Z=V.join(",");' .
            'm.textContent=Z+"{opacity: 0;transition: opacity "+O+" ease-in-out;}."+o+" {opacity: 1 !important;}";' .
            'I.flRevealContainers=function(){' .
            'var a=f.querySelectorAll(Z);' .
            'for(var T=0;T<a.length;T++){a[T].classList.add(o)}' .
            '};' .
            'setTimeout(I.flRevealContainers,l)}' .
            'var W=g+"/static/"+i+"/main.js?usergrouphash=%s"+n;' .
            'var p=f.createElement("script");' .
            'p.type="text/javascript";' .
            'p.async=true;' .
            'p.src=g+"/static/loader.min.js";' .
            'var q=f.getElementsByTagName("script")[0];' .
            'p.setAttribute("data-fl-main",W);' .
            'q.parentNode.insertBefore(p,q);' .
            'q.parentNode.insertBefore(m,p)})' .
            '(document,\'%s\',\'\',\'%s\',\'fl-reveal\',3000,\'.3s\',\'//cdn.findologic.com\',window,\'%s\');' .
            '</script>',
            $this->data['USERGROUP_HASH'],
            $this->data['HASHED_SHOPKEY'],
            $navSelector,
            $searchSelector
        );
    }

    /**
     * @return string Reads plugin configuration and returns shop key for current shop.
     */
    private function getShopKey()
    {
        $storeId = $this->storeManagerInterface->getStore()->getId();

        return $this->scopeConfig->getValue(
            'findologic/findologic_group/shopkey',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Gets category path as string separated by provided separator.
     *
     * @param string $categoryPathIds Identifiers separated by slash. Example: 154/6584/653/8
     * @param string $separator Separator for generated path between category names
     * @param bool $wrap Indicates whether to wrap each category name with quotes
     * @param bool $addSlashes
     * @return string
     */
    public function getCategoryPath($categoryPathIds, $separator = ', ', $wrap = true, $addSlashes = true)
    {
        $holder = [];
        $categoryIds = explode('/', $categoryPathIds);
        $n = 0;
        foreach ($categoryIds as $catId) {
            // Skip 'root' and 'default' category and add up to 5 categories.
            if ($n < 5 && $catId != 1 && $catId != 2) {
                $category = $this->getCategory($catId);
                $categoryName = $addSlashes ? $this->customaddslashes($category->getName()) : $category->getName();
                $holder[] = $wrap ? '"' . $categoryName . '"' : $categoryName;

                $n++;
            }
        }

        return implode($separator, $holder);
    }

    /**
     * Gets category from local cache.
     *
     * @param int $id Category identifier
     * @return mixed
     */
    public function getCategory($id)
    {
        if (!array_key_exists($id, self::$categories)) {
            $il = ObjectManager::getInstance();
            self::$categories[$id] = $il->create(Collection::class)
                            ->addAttributeToSelect(['is_active', 'name'])
                            ->addFieldToFilter('entity_id', $id)
                            ->getFirstItem();
        }

        return self::$categories[$id];
    }

    /**
     * Returns hash key merged from shop key and user group.
     *
     * @param string $shopKey
     * @param string $userGroup
     * @return string
     */
    public function userGroupToHash($shopKey, $userGroup)
    {
        return base64_encode($shopKey ^ $userGroup);
    }

    private function customaddslashes($value)
    {
        $value = str_replace('"', '\"', $value);
        $value = str_replace("'", "\'", $value);
        return $value;
    }
}
