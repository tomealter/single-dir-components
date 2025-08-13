# Content Security Policy

Drupal sites that require a content security policy should first consider the ramifications of that.
See [John's presentation on CSP](https://johnbburg.github.io/csp-presentation/web/pres) for more information on Content
Security Policy, and some tips on whether or not it is appropriate to set up on your site.

Due to the varied nature of websites, it's challenging to create a one-size-fits-all CSP. As of October 2024, best
practices recommend defining CSPs programmatically rather than solely relying on configuration files. To gain a better
understanding of how to use the CSP module, review the[documentation](https://www.drupal.org/docs/extending-drupal/contributed-modules/contributed-module-documentation/content-security-policy),
especially the section "[Altering a site's policy](https://www.drupal.org/docs/extending-drupal/contributed-modules/contributed-module-documentation/content-security-policy/altering-a-sites-policy)"

The Drupal CSP module provides you with a UI to add domains to various CSP directives. However, adding
sources this way can make it difficult to understand the reasoning behind each inclusion as there is no inline method
of adding documentation within the CSP module's UI. So, in order provide documentation as to why a CSP source is
included, in the same context as it's definition, we recommend using the CSP module's API to create an EventSubscriber,
and alter the CSP in code. This will allow you to provide code comments adjacent to the included sources to describe
their reason for being included.

To aid in getting started with this, an example "[CSP Starter](https://github.com/forumone/csp_starter)" module is
available in the F1 GitHub repository here. The provided "`CspStarterSubscriber`" in that module gives you good starting
place as well as examples for including some common sources for your CSP. Feel free to install that module as a custom
module in your own project, and modify from there.


## Getting Started with the CSP Starter Module

Download the latest release of the [CSP Starter](https://github.com/forumone/csp_starter/tags) module from GitHub into your
modules/custom directory, and enable as normal.

Review the contents of the csp_starter/src/EventSubscriber/CspStarterSubscriber.php file, in the onCspPolicyAlter
method, and uncomment any sections that seem like they may be relevant to your project. Also, if needed, add any new
sources to the appropriate directive section. Note that it places everything in the `default-src` directive initially,
but feel free to arrange these to a configuration that works for your project. You may wish to specifically define
directives such as `script-src` within your project instead of lumping everything into `default-src`. If you do make
any additions, be sure to include any detailed information in a code comment explaining the reasoning for its inclusion,
maybe even include a link to a corresponding Jira ticket if applicable. 


