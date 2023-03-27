<?php

declare(strict_types=1);

namespace BurlacuWeb\CmsContentHandling\Service;

use Magento\Cms\Api\GetPageByIdentifierInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\Data\PageInterfaceFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\Store;

/**
 * Creates a new CMS Page with given data.
 *
 * Class CreateCmsPageService
 */
class CreateCmsPageService
{
    private PageRepositoryInterface $pageRepository;
    private PageInterfaceFactory $pageInterfaceFactory;
    private GetPageByIdentifierInterface $getPageByIdentifier;

    /**
     * @param PageRepositoryInterface $pageRepository
     * @param PageInterfaceFactory $pageInterfaceFactory
     * @param GetPageByIdentifierInterface $getPageByIdentifier
     */
    public function __construct(
        PageRepositoryInterface $pageRepository,
        PageInterfaceFactory $pageInterfaceFactory,
        GetPageByIdentifierInterface $getPageByIdentifier
    ) {
        $this->pageRepository = $pageRepository;
        $this->pageInterfaceFactory = $pageInterfaceFactory;
        $this->getPageByIdentifier = $getPageByIdentifier;
    }

    /**
     * @param array $entityData
     *
     * @return void
     * @throws LocalizedException
     */
    public function execute(array $entityData)
    {
        try {
            $identifier = $entityData[PageInterface::IDENTIFIER];
            $storeId = $entityData['store_id'] ?? Store::DEFAULT_STORE_ID;

            /** @var PageInterface|AbstractModel $page */
            $page = $this->getPageByIdentifier->execute($identifier, $storeId);
        } catch (NoSuchEntityException $e) {
            /** @var PageInterface|AbstractModel $page */
            $page = $this->pageInterfaceFactory->create();
        }

        $page->addData($entityData);

        $this->pageRepository->save($page);
    }
}
