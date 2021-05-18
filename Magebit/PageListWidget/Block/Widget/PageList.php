<?php
/**
 * This file is part of the Magebit PageListWidget package.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magebit PageListWidget
 * to newer versions in the future.
 *
 * @copyright Copyright (c) 2019 Magebit, Ltd. (https://magebit.com/)
 * @license   GNU General Public License ("GPL") v3.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Magebit\PageListWidget\Block\Widget;

use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Template;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Cms\Api\PageRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Block responsible for displaying list of CMS Pages
 */

class PageList extends Template implements BlockInterface
{

    /**
     * Widget options and values
     */
    const TITLE = 'title';
    const DISPLAY_MODE = 'display_mode';
    const SELECTED_PAGES = 'selected_pages';
    const SHOW_ALL = 'all';
    const SHOW_SPECIFIC = 'specific';

    /**
     * @var string
     */
    protected $_template = "Magebit_PageListWidget::page-list.phtml";

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepositoryInterface;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PageList constructor.
     * @param PageRepositoryInterface $pageRepositoryInterface
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        PageRepositoryInterface $pageRepositoryInterface,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->pageRepositoryInterface = $pageRepositoryInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
    }

    /**
     * Returns CMS Pages basing on selected display mode in Page List Widget and Selected pages in Widget Options
     *
     * @return \Magento\Cms\Api\Data\PageInterface[]
     */
    public function getPages(): array
    {
        $pages = [];
        $searchCriteria = $this->searchCriteriaBuilder;
        if ($this->getData(self::DISPLAY_MODE) === self::SHOW_SPECIFIC){
            $searchCriteria->addFilter('identifier', explode(',',$this->getData(self::SELECTED_PAGES)), 'in');
        }
        try {
            $pages = $this->pageRepositoryInterface->getList($searchCriteria->create())->getItems();
        } catch (\Exception $exception) {
            $this->logger->critical($exception);

        }
        return $pages;
    }
}
