<?php

namespace Klevu\Search\Helper;

class Compat extends \Magento\Framework\App\Helper\AbstractHelper {
    /**
     * @var \Magento\Catalog\Model\Factory
     */
    protected $_catalogModelFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_frameworkModelResource;

    public function __construct(\Magento\Catalog\Model\Factory $catalogModelFactory, 
        \Magento\Framework\App\ResourceConnection $frameworkModelResource)
    {
        $this->_catalogModelFactory = $catalogModelFactory;
        $this->_frameworkModelResource = $frameworkModelResource;

    }


    /**
     * Return a Select statement for retrieving URL rewrites for the given list of products.
     *
     * Uses the Product URL Rewrite Helper if available, falling back to building the query manually.
     *
     * @param array $product_ids
     * @param       $category_id
     * @param       $store_id
     *
     * @return \Magento\Framework\Db\Select
     */
    public function getProductUrlRewriteSelect(array $product_ids, $category_id, $store_id) {
        $resource = $this->_frameworkModelResource;
        return $resource->getConnection("core_write")->select()
            ->from($resource->getTableName("url_rewrite"), array("entity_id", "request_path"))
            ->where("store_id = ?", (int) $store_id)
            ->where("is_autogenerated = ?", 1)
            ->where("entity_type = ?", "product")
            ->where("entity_id IN (?)", $product_ids)
            ->order("entity_id " . \Magento\Framework\Data\Collection::SORT_ORDER_DESC);
    }

    /**
     * Return the current date in internal format.
     *
     * @param bool $withoutTime day only flag
     *
     * @return string
     */
    public function now($withoutTime = false) {
        if (method_exists("Magento\Framework\Date", "now")) {
            return \Magento\Framework\Date::now($withoutTime);
        } else {
            $format = ($withoutTime) ? "Y-m-d" : "Y-m-d H:i:s";
            return date($format);
        }
    }
}
