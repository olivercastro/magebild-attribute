<?php
/**
 * @author MageBild Team
 * @copyright Copyright (c) 2019 Magebild
 * @package Magebild_DefaultText
 */
namespace Magebild\DefaultText\Plugin\Magento\Helper;

use Magebild\DefaultText\Model\Source\Attribute\Campaign;
use Magebild\DefaultText\Plugin\Magento\Catalog\Model\Product;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Eav\Model\AttributeRepository;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Escaper;

class Output
{
    const CATALOG_PRODUCT_ENTITY_INT_TABLE = 'catalog_product_entity_int';

    const CATALOG_PRODUCT_ENTITY = 'catalog_product';

    const ALLOWED_TAGS = ['div','p','hr','br'];

    private $resource;

    private $attributeRepository;

    private $escaper;

    private $blockRepository;

    /**
     * Product constructor.
     * @param Escaper $escaper
     * @param BlockRepositoryInterface $blockRepository
     * @param AttributeRepository $attributeRepository
     * @param ResourceConnection $resource
     */
    public function __construct(
        Escaper $escaper,
        BlockRepositoryInterface $blockRepository,
        AttributeRepository $attributeRepository,
        ResourceConnection $resource
    ) {
        $this->escaper = $escaper;
        $this->blockRepository = $blockRepository;
        $this->attributeRepository = $attributeRepository;
        $this->resource = $resource;
    }

    /**
     * @param \Magento\Catalog\Helper\Output $subject
     * @param $result
     * @param $attributeHtml
     * @param \Magento\Catalog\Model\Product $product
     * @param $attributeName
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterProductAttribute(
        \Magento\Catalog\Helper\Output $subject,
        $result,
        $product,
        $attributeHtml,
        $attributeName
    ) {
        $modifiedResult = $result;
        if ($attributeName === Product::ATTRIBUTE_TARGET) {
            $attributeId = $this->getAttributeId();
            $cmsBlockId = $this->getSelectedOptionId($product, $attributeId);

            if ($cmsBlockId !== null) {
                $block = $this->blockRepository->getById($cmsBlockId);
                $matches = [];
                $regexp = '<div\s[^>]*class=\"content-home block-img-content\"[^>]*>(.*)<\/div>';
                preg_match_all("/$regexp/siU", $modifiedResult, $matches, PREG_SET_ORDER);
                if (count($matches) == 0) {
                    $modifiedResult .= $this->escaper->escapeHtml($block->getContent(), self::ALLOWED_TAGS);
                }
            }
        }
        return $modifiedResult;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getAttributeId()
    {
        $attribute = $this->attributeRepository
            ->get(self::CATALOG_PRODUCT_ENTITY, Campaign::DEFAULT_TEXT_ATTRIBUTE_CODE);
        return $attribute->getId();
    }

    /**
     * @param $product
     * @param $attributeId
     * @return mixed|null
     */
    private function getSelectedOptionId($product, $attributeId)
    {
        $value = null;
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $connection */
        $connection = $this->resource->getConnection('core_read');
        $table = $this->resource->getTableName(self::CATALOG_PRODUCT_ENTITY_INT_TABLE);
        $select = $connection->select()
            ->from($table)
            ->where('attribute_id = ?', $attributeId)
            ->where('entity_id = ?', $product->getId())
            ->where('store_id = ?', 0)
            ->limit(1);

        $record = $connection->fetchRow($select);

        if (is_array($record) && array_key_exists('value', $record)) {
            $value = $record['value'];
        }

        return $value;
    }
}
