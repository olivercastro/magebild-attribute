<?php
/**
 * @author MageBild Team
 * @copyright Copyright (c) 2019 Magebild
 * @package MageBild_DefaultText
 */
namespace Magebild\DefaultText\Plugin\Magento\Catalog\Model;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Escaper;

class Product
{
    const ATTRIBUTE_TARGET = 'description';

    private $escaper;

    private $blockRepository;

    /**
     * Product constructor.
     * @param Escaper $escaper
     * @param BlockRepositoryInterface $blockRepository
     */
    public function __construct(
        Escaper $escaper,
        BlockRepositoryInterface $blockRepository
    ) {
        $this->escaper = $escaper;
        $this->blockRepository = $blockRepository;
    }

    /**
     * @param \Magento\Catalog\Model\Product $subject
     * @param $result
     * @param $attributeCode
     * @return string
     */
    public function afterGetAttributeText(
        \Magento\Catalog\Model\Product $subject,
        $result,
        $attributeCode
    ) {
        $modifiedResult = $result;
        if ($attributeCode === self::ATTRIBUTE_TARGET) {
        }
        //Your plugin code
        return $modifiedResult;
    }
}
