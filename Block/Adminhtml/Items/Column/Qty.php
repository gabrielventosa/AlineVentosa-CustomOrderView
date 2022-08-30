<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace AlineVentosa\CustomOrderView\Block\Adminhtml\Items\Column;

use Magento\Sales\Model\Order\Creditmemo\Item as CreditmemoItem;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\Item;
use Magento\Quote\Model\Quote\Item\AbstractItem as QuoteItem;
use Magento\InventoryApi\Api\StockRepositoryInterface;
use Magento\InventorySalesApi\Model\GetStockItemDataInterface;


/**
 * Sales Order items qty column renderer
 *
 * @api
 * @author     Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */

class Qty extends \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn
{
    /**
     * Option factory
     *
     * @var \Magento\Catalog\Model\Product\OptionFactory
     */
    protected $_optionFactory;

    /**
     *  StockRepositoryInterface
     *
     * @var Magento\InventoryApi\Api\StockRepositoryInterface
     */
    protected $stockRepositoryInterface;

    /**
     * stockRepositoryInterface
     *
     * @var Magento\InventorySalesApi\Model\GetStockItemDataInterface
     */
    protected $stockItemDataInterface;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Frameword\Registry $registry
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param StockRepositoryInterface $stockRepositoryInterface
     * @param GetStockItemDataInterface $stocItemDataInterface
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        StockRepositoryInterface $stockRepositoryInterface,
        GetStockItemDataInterface $stockItemDataInterface,
        array $data = []
    ) {
        $this->_optionFactory = $optionFactory;
        $this->stockRepositoryInterface = $stockRepositoryInterface;
        $this->stockItemDataInterface = $stockItemDataInterface;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
    }

    public function getStockRepository()
    {
        $stockData = array();
        $stock_rpsi = $this->stockRepositoryInterface->getList();
        foreach ($stock_rpsi->getItems() as $stk) {
            $stockId = $stk->getStockId();
            $stockName = $stk->getName();
            $sku = $this->getSku();
            $stockData[] = array($stockId, $stockName, $sku);
        }
        return $stockData;
    }

    public function getStockQty()
    {
        $stockData = $this->getStockRepository();
        $options = $this->getItem()->getProductOptions();
        $sku = $options['simple_sku'];
        //$sku = $this->getSku();
        $stocks = array();
        foreach ($stockData as $stk) {
            $stockId = $stk[0];
            $stockName = $stk[1];
            $stockItemData = $this->stockItemDataInterface->execute($sku, $stockId);
            if (is_null($stockItemData)) {
                continue;
            }
            $stock = round($stockItemData["quantity"]);
            $stocks[] = array($stockName, $stock);
        }
        return $stocks;
    }
        /*
        $productDetails = $this->product->create()->load($productId);
        $proType = $productDetails->getTypeId();
        $result = array();
        if ($proType == 'configurable') {
            $product = $this->getCurrentProduct();
            $productTypeInstance = $product->getTypeInstance();
            $usedProducts = $productTypeInstance->getUsedProducts($product);
            foreach ($usedProducts as $p) {
                $p_id = $p->getId();
                $p_details = $this->product->create()->load($p_id);
                $p_sku = $p_details->getSku();
                $stockData = array();
                foreach ($stock_rpsi->getItems() as $stk) {
                    $stockId = $stk->getStockId();
                    $stockName = $stk->getName();
                    $stockItemData = $this->stockItemDataInterface->execute($p_sku, $stockId);
                    if (is_null($stockItemData)) {
                        continue;
                    }
                    $stock = round($stockItemData["quantity"]);
                    $stockData[] = array($stockName, $stock);
                }
                $result[$p_id] = $stockData;
            }
        }
        return $result;
    }
 */
}
