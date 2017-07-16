# Aligent SocialLinks Magento 2 Module

This module will allow you to specify links to the social accounts for a particular website.

## Installing

*need to figure out how the module will get put into the project, likely from composer, but not sure how that works*

Enable the module

`bin/magento module:enable Aligent_SocialLinks`

An instance of the Widget will be automatically created and assigned to the currently active theme.
 
The module will then need to be added to a page/s using XML. The following line, place inside a `referenceContainer` will
achieve this

`<block class="Aligent\SocialLinks\Block\SocialLinks" name="aligent.social.links" as="aligentSocialLinks"/>`

The links won't be styled at all, and will just display the name of the social network inside the <a> tag. This makes it
very straightforward for you to use any styling you would like, along with any icons.
