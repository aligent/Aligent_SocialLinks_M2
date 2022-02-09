# Aligent SocialLinks Magento 2 Module

This module will allow you to specify links to the social accounts for a particular website.

## Installing

Ensure the repository `https://github.com/aligent/Aligent_SocialLinks_M2.git` is listed under the repositories section in your `composer.json` file, e.g.

```
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/aligent/Aligent_SocialLinks_M2.git"
    }
]
```

Then run

`composer require aligent/sociallinks`

If the latest release isn't included, you may need to edit the `composer.json` file manually, changing the entry to `"aligent/sociallinks": "dev-master"`, and then running `composer update aligent/sociallinks`

## Enable the module

`bin/magento module:enable Aligent_SocialLinks`

An instance of the Widget will be automatically created and assigned to the currently active theme.

## Create Instance of Widget through Upgrade script

The following file contents shows the minimum requirements of the upgrade script to create an instance of the SocialLinks widget and save it to the database

```php
<?php
namespace Aligent\CMS\Setup;

use Magento\Widget\Model\Widget\InstanceFactory as WidgetFactory;

class CmsSetup {
    /**
     * @var WidgetFactory
     */
    protected $widgetFactory;
    
    /**
     * Init
     *
     * @param WidgetFactory $widgetFactory
     */
    public function __construct(
        WidgetFactory $widgetFactory
    ) {
        $this->widgetFactory = $widgetFactory;
    }
    
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        if (version_compare($context->getVersion(), '1.0.0', '<')) {
            $widgetData = [
                'instance_type' => 'Aligent\\SocialLinks\\Block\\SocialLinks',
                'title' => 'Aligent Social Links'
            ];

            // Update the usernames for each of the required social networks
            // If a network isn't required, it can be removed from the array
            $widgetParams = [
                'display_text' => 'network_name',
                'twitter' => 'aligent',
                'facebook' => 'aligent',
                'instagram' => 'aligent',
                'youtube' => 'aligent',
                'snapchat' => 'aligent',
                'pinterest' => 'aligent',
                'tiktok'    => 'aligent',
                'linkedin'  => 'aligent'
            ];

            $widget = $this->widgetFactory->create();
            $widget->addData($widgetData);
            $widget->setWidgetParameters($widgetParams);
            $widget->setPageGroups(null);
            $widget->save();
        }
    }
}
```

## Adding module to page

The module will then need to be added to a page/s using XML. The following line, place inside a `referenceContainer` will
achieve this

`<block class="Aligent\SocialLinks\Block\SocialLinks" name="aligent.social.links" as="aligentSocialLinks"/>`

## Styling the links

The links aren't styled at all, and will just display the name of the social network inside an <a> tag. This makes it
very straightforward for you to use any styling you would like, along with any icons.

The links are contained in the following HTML structure

```
<div class="aligent-social-links-container">
    <div class="aligent-social-links-inner-container">
        <ul class="aligent-social-links">
            <li>
                <a class="aligent-social__link social__link--facebook" href="http://www.facebook.com/username">
                    <span class="aligent-social__text">Facebook</span>
                </a>
            </li>
        </ul>
    </div>
</div>
```

## Using your own custom template

If you would like to overwrite the default template, and display the links in another way, you can do that very easily.

Create a new `social-links.phtml` file in your theme's folder inside `design`, E.g.
`app/design/frontend/Magento/{your_theme_name}/Aligent_SocialLinks/templates/social-links.phtml`

Inside the template `$block` will be an instance of `Aligent\SocialLinks\Block\SocialLinks`, and you simply call
`$block->getUrls()` to get the URLs set in the database
