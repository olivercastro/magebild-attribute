<?php
/**
 * @author MageBild Team
 * @copyright Copyright (c) 2019 Magebild
 * @package Magebild_DefaultText
 */
namespace Magebild\DefaultText\Model\Source\Attribute;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;

class Campaign extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const DEFAULT_TEXT_ATTRIBUTE_CODE = 'campaign_default_text';

    private $blockRepository;

    private $searchBuilder;

    private $filterBuilder;

    /**
     * Campaign constructor.
     * @param BlockRepositoryInterface $blockRepository
     * @param SearchCriteriaBuilder $searchBuilder
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        BlockRepositoryInterface $blockRepository,
        SearchCriteriaBuilder $searchBuilder,
        FilterBuilder $filterBuilder
    ) {
        $this->blockRepository = $blockRepository;
        $this->searchBuilder = $searchBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * Retrieve All options
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAllOptions()
    {
        $blockFilter = $this->filterBuilder->setField('identifier')
            ->setValue('block_product_default%')
            ->setConditionType('like')
            ->create();
        $search = $this->searchBuilder->addFilter($blockFilter)
            ->create();

        $cmsBlocks = $this->blockRepository->getList($search)->getItems();

        $blocks = [];

        foreach ($cmsBlocks as $block) {
            $blocks[] = [
                'label' => $block->getTitle(),
                'value' => $block->getId(),
            ];
        }
        // TODO: Implement getAllOptions() method.
        return $blocks;
    }
}
