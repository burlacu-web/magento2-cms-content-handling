# CMS Content Handling

The aim of this module is to facilitate the CMS entities creation from the codebase.

The main highlight here is that it provides the ability to read the CMS entity content from the given  file path (`.html` usually) instead of storing the content inside the PHP Class (which is not handy, especially when using PageBuilder).

## Installation

```bash
composer require burlacu-web/cms-content-handling
bin/magento module:enable BurlacuWeb_CmsContentHandling
bin/magento setup:upgrade
```

## Uninstall

```bash
composer remove burlacu-web/cms-content-handling
bin/magento setup:upgrade
```

The module doesn't add any database tables.

## How to

##### Create CMS Block

```php
<?php

namespace BurlacuWeb\ProductRegistration\Setup\Patch\Data;

use BurlacuWeb\CmsContentHandling\Service\CreateCmsBlockService;
use BurlacuWeb\CmsContentHandling\Service\GetContentFromFileService;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class CreateRegistrationContentCmsBlockDataPatch
 */
class CreateRegistrationContentCmsBlockDataPatch implements DataPatchInterface
{
    /** @var string Path to the file with contents for CMS Block entity */
    private const CONTENT_FILE_PATH = __DIR__ . '/data/cms-block/product-registration-content.html';

    /**
     * @var CreateCmsBlockService
     */
    private CreateCmsBlockService $createCmsBlockService;

    /**
     * @var GetContentFromFileService
     */
    private GetContentFromFileService $getContentFromFileService;

    /**
     * @param CreateCmsBlockService $createCmsBlockService
     * @param GetContentFromFileService $getContentFromFileService
     */
    public function __construct(
        CreateCmsBlockService $createCmsBlockService,
        GetContentFromFileService $getContentFromFileService
    ) {
        $this->createCmsBlockService = $createCmsBlockService;
        $this->getContentFromFileService = $getContentFromFileService;
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function apply()
    {
        $blockData = [
            BlockInterface::IDENTIFIER => 'product-registration-content',
            BlockInterface::TITLE => 'Product Registration - Page Content',
            BlockInterface::CONTENT => $this->getContentFromFileService->execute(self::CONTENT_FILE_PATH)
        ];

        $this->createCmsBlockService->execute($blockData);
    }
}
```

##### Create CMS Page

```php
<?php

namespace Vendor\Module\Setup\Patch\Data;

use BurlacuWeb\CmsContentHandling\Service\CreateCmsPageService;
use BurlacuWeb\CmsContentHandling\Service\GetContentFromFileService;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class CreateRegistrationThankYouCmsPageDataPatch
 */
class CreateRegistrationThankYouCmsPageDataPatch implements DataPatchInterface
{
    /** @var string Path to the file with contents for CMS Block entity */
    private const CONTENT_FILE_PATH = __DIR__ . '/data/cms-page/registration-page.html';

    /**
     * @var GetContentFromFileService
     */
    private GetContentFromFileService $getContentFromFileService;

    /**
     * @var CreateCmsPageService
     */
    private CreateCmsPageService $createCmsPageService;

    /**
     * @param CreateCmsPageService $createCmsPageService
     * @param GetContentFromFileService $getContentFromFileService
     */
    public function __construct(
        CreateCmsPageService $createCmsPageService,
        GetContentFromFileService $getContentFromFileService
    ) {
        $this->getContentFromFileService = $getContentFromFileService;
        $this->createCmsPageService = $createCmsPageService;
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function apply()
    {
        $blockData = [
            PageInterface::IDENTIFIER => 'registration-page',
            PageInterface::TITLE => 'Product Registration',
            PageInterface::PAGE_LAYOUT => 'cms-full-width',
            PageInterface::CONTENT => $this->getContentFromFileService->execute(self::CONTENT_FILE_PATH)
        ];

        $this->createCmsPageService->execute($blockData);
    }
}

```

### Notes

#### CMS Entity creation

When calling the service to create a CMS Block or CMS Page, first of all it checks whenever an entity with given
`identifier` exists.
If so, it loads that entity and applies passed data on top of existing.

#### CMS entity content in codebase

The CMS entities content is recommended to be stored at `Vendor/Module/Setup/Data/Patch/data` folder.
This module has dummy files created to exemplify the desired folder structure.
Use it in your own modules that is using this module as a dependency.

There, separate CMS Block from CMS Page by two folders: `cms-block` and `cms-page`.

Inside each of the aforementioned folders store the `.html` files with the content of the CMS entity.
The filename is highly recommended being the CMS entity's `identifier` for better file management and
easier finding of required file.
