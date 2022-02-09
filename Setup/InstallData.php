<?php

declare(strict_types=1);

namespace Aligent\SocialLinks\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Widget\Model\Widget\InstanceFactory;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;
use Magento\Theme\Model\Theme;
use Magento\Framework\App\Config\ScopeConfigInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var InstanceFactory
     */
    private $widgetFactory;
    /**
     * @var Registry
     */
    private $coreRegistry;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * InstallData constructor.
     *
     * @param InstanceFactory       $widgetFactory
     * @param Registry              $coreRegistry
     * @param LoggerInterface       $logger
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        InstanceFactory $widgetFactory,
        Registry $coreRegistry,
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->widgetFactory = $widgetFactory;
        $this->coreRegistry = $coreRegistry;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context): void
    {
        // Apply for all stores
        $storeId = 0;
        $widgetCodeName = 'aligent-social-links';

        $widgetInstance = $this->widgetFactory->create();
        $widgetType = $widgetInstance->getWidgetReference('code', $widgetCodeName, 'type');
        $themeId = $this->getThemeId();
        $widgetInstance->setType($widgetType)->setCode($widgetCodeName)->setThemeId($themeId);
        $this->coreRegistry->register('current_widget_instance', $widgetInstance);

        $storeIds = [$storeId];
        $parameters = [];

        $widgetInstance->setTitle("Aligent Social Links")
            ->setStoreIds($storeIds)
            ->setWidgetParameters($parameters);

        try {
            $widgetInstance->save();
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
        }
    }

    /**
     * Get the name of the current theme being used
     *
     * @return int
     */
    public function getThemeId(): int
    {
        return (int)$this->scopeConfig->getValue(
            DesignInterface::XML_PATH_THEME_ID,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }
}
