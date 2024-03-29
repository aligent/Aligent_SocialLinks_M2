<?php

declare(strict_types=1);

namespace Aligent\SocialLinks\Block;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Widget\Model\ResourceModel\Widget\Instance as WidgetResource;
use Magento\Widget\Model\ResourceModel\Widget\Instance\CollectionFactory as WidgetCollectionFactory;
use Magento\Widget\Model\Widget\Instance as WidgetInstance;

class SocialLinks extends Template implements BlockInterface
{

    protected $_template = "Aligent_SocialLinks::social-links.phtml";

    /**
     * @var WidgetInstance
     */
    protected $widgetInstance;

    /**
     * @var WidgetCollectionFactory
     */
    protected $widgetCollectionFactory;

    /**
     * @var WidgetResource
     */
    protected $widgetResource;

    private $urls = [
        "facebook"  => "//www.facebook.com/",
        "twitter"   => "//www.twitter.com/",
        "pinterest" => "//www.pinterest.com/",
        "instagram" => "//www.instagram.com/",
        "youtube"   => "//www.youtube.com/",
        "snapchat"  => "//www.snapchat.com/add/",
        "tiktok"    => "//www.tiktok.com/@",
        "linkedin"  => "//www.linkedin.com/company/"
    ];

    /**
     * SocialLinks constructor
     *
     * @param Context                 $context
     * @param PageFactory             $pageFactory
     * @param WidgetCollectionFactory $widgetCollectionFactory
     * @param WidgetResource          $widgetResource
     * @param array                   $data
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        WidgetCollectionFactory $widgetCollectionFactory,
        WidgetResource $widgetResource,
        array $data = []
    ) {
        $this->widgetCollectionFactory = $widgetCollectionFactory;
        $this->widgetResource = $widgetResource;
        parent::__construct($context, $data);
    }

    /**
     * Find instance of widget to display
     *
     * @param bool $forceLoad
     *
     * @return WidgetInstance
     */
    public function getWidgetInstance(bool $forceLoad = false): WidgetInstance
    {
        if ($this->widgetInstance === null || $forceLoad) {
            $this->widgetInstance = $this->widgetCollectionFactory->create()
                ->addFilter('instance_type', $this->getType())
                ->addStoreFilter([$this->_storeManager->getStore()->getId()])
                ->getLastItem();

            //2.1.7 fix, if loaded from collection, widget does not unserialize fields automatically
            $this->widgetResource->unserializeFields($this->widgetInstance);
        }

        return $this->widgetInstance;
    }

    /**
     * Get parameters for the widget
     *
     * @return array
     */
    public function getWidgetParameters(): array
    {
        $widgetInstance = $this->getWidgetInstance();
        return $widgetInstance->getWidgetParameters();
    }

    /**
     * Get a parameter value for a widget instance
     *
     * @param string $parameter The parameter to get the value of for this widget instance
     *
     * @return string|null
     */
    public function getWidgetParameter(string $parameter): ?string
    {
        $widgetParameters = $this->getWidgetParameters();
        if (!array_key_exists($parameter, $widgetParameters)) {
            $this->_logger->info(
                "Undefined index $parameter. Valid indexes are; " .
                implode(", ", array_keys($widgetParameters))
            );
            return null;
        } else {
            return $widgetParameters[$parameter];
        }
    }

    /**
     * Find an instance of the widget saved in the database, and return an array of usernames saved for each network.
     *
     * @return array
     */
    public function getUsernames(): array
    {
        $parameters = $this->getWidgetParameters();
        unset($parameters['display_text']);
        // ensure that only populated values are returned
        foreach ($parameters as $network => $username) {
            if (!$username) {
                unset($parameters[$network]);
            }
        }
        return $parameters ?? [] ;
    }

    /**
     * Get the link to the social network
     *
     * @param string $network  The social network to be linked to
     * @param string $username The username on that social network
     *
     * @return string
     */
    public function getSocialLink(string $network, string $username): string
    {
        return $this->urls[$network].$username;
    }

    /**
     * Determine if the social network name should be displayed
     *
     * @return bool
     */
    public function displaySocialNetworkName(): bool
    {
        return in_array($this->getWidgetParameter('display_text'), ['network_name', 'both']);
    }

    /**
     * Determine if the username for the social network should be displayed
     *
     * @return bool
     */
    public function displaySocialNetworkUsername(): bool
    {
        return in_array($this->getWidgetParameter('display_text'), ['username', 'both']);
    }
}
