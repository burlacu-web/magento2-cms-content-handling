<?php

declare(strict_types=1);

namespace BurlacuWeb\CmsContentHandling\Service;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Api\Data\BlockInterfaceFactory;
use Magento\Cms\Api\GetBlockByIdentifierInterface;
use Magento\Cms\Model\Block;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\Store;

/**
 * Creates a new CMS Block with given data.
 *
 * Class CreateCmsBlockService
 */
class CreateCmsBlockService
{
    private BlockRepositoryInterface $blockRepository;
    private BlockInterfaceFactory $blockInterfaceFactory;
    private GetBlockByIdentifierInterface $getBlockByIdentifier;
    private CollectionFactory $cmsBlockCollectionFactory;

    /**
     * @param BlockRepositoryInterface $blockRepository
     * @param BlockInterfaceFactory $blockInterfaceFactory
     * @param GetBlockByIdentifierInterface $getBlockByIdentifier
     * @param CollectionFactory $cmsBlockCollectionFactory
     */
    public function __construct(
        BlockRepositoryInterface $blockRepository,
        BlockInterfaceFactory $blockInterfaceFactory,
        GetBlockByIdentifierInterface $getBlockByIdentifier,
        CollectionFactory $cmsBlockCollectionFactory
    ) {
        $this->blockRepository = $blockRepository;
        $this->blockInterfaceFactory = $blockInterfaceFactory;
        $this->getBlockByIdentifier = $getBlockByIdentifier;
        $this->cmsBlockCollectionFactory = $cmsBlockCollectionFactory;
    }

    /**
     * @param array $entityData
     *
     * @return void
     * @throws LocalizedException
     */
    public function execute(array $entityData)
    {
        if (!array_key_exists('store_id', $entityData)) {
            $entityData['store_id'] = [Store::DEFAULT_STORE_ID];
        }

        if (!is_array($entityData['store_id'])) {
            $entityData['store_id'] = [$entityData['store_id']];
        }

        $storeId = $entityData['store_id'][0];
        $identifier = $entityData[BlockInterface::IDENTIFIER];

        $collection = $this->cmsBlockCollectionFactory->create();
        $collection->addFieldToFilter(BlockInterface::IDENTIFIER, $identifier);
        $collection->addStoreFilter($storeId);

        /** @var Block|null $block */
        $block = $collection->getFirstItem();
        $foundBlock = $block && $block->getId() && $block->getStoreId() === $storeId;

        if (!$foundBlock) {
            /** @var BlockInterface|Block|AbstractModel $block */
            $block = $this->blockInterfaceFactory->create();
        }

        $block->addData($entityData);
        $block->setData('store_id', $entityData['store_id']);

        $this->blockRepository->save($block);
    }
}
