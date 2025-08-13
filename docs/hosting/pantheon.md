# Drupal Project template for Pantheon

This is a build guide to help walk you through setting the project up for Pantheon.

## Getting started...

* Please rename the following files:
    * `.buildkite/pipeline.pantheon.yml` > `.buildkite/pipeline.deploy.yml`
* Need to modify the following variables:
    * `.buildkite/pipeline.deploy.yml` > `[SSH_REPO_FROM_PANTHEON]`
        *  This is the Git SSH Clone URL under Connection Info in the dashboard:
           `ssh://codeserver.dev.[UUID]@codeserver.dev.[UUID].drush.in:2222/~/repository.git`
    * `.buildkite/pipeline.deploy.yml` > `[PANTHEON_HOST]`
        * This will be `codeserver.dev.[UUID].drush.in:2222`
    * `.ddev/providers/pantheon.yaml` > `[PROJECT.KEY]`
        * This breaks down into `[UUID].[ENVIRONMENT]` **Note:** that your site UUID and environment appear in the dashboard URL:
      `https://dashboard.pantheon.io/sites/[UUID]#[ENVIRONMENT]`
* Rename `README.project.md` > `README.md`

## Additional services

Please go through the following if you need them, please go through the (required) section.

* (optional) [Setup memcache](../services/memcache.md)
* (required) [Setup theme](../services/theme.md)
* (optional) [Setup Redis](../services/redis.md)

**NOTE** Additional resources can be found [here](../services/optional.md)

## If you need to set up config_split

For Pantheon, there is specific things needed for config split, in the `./web/sites/default/settings.php`.

```php

/*
 *  This is an example on having specific
 *  environment splits for Pantheon
 *
 */
//if (isset($_ENV['PANTHEON_ENVIRONMENT'])) {
//  switch($_ENV['PANTHEON_ENVIRONMENT']) {
//    // case 'live':
//    case 'f1-stage':
//      $split_env = 'stage';
//      break;
//    case 'f1-dev':
//      $split_env = 'dev';
//      break;
//    case 'dev':
//    case 'test':
//      $split_env = 'stage';
//      break;
//    case 'live':
//      $split_env = 'live';
//      break;
//  }
//}
```
## Cleaning up

Please delete the following:
* `Capfile`
* `Gemfile`
* `docs`
* `.buildkite/pipeline.f1.yml`
* `.buildkite/pipeline.acquia.yml`
* `capistrano`
* `.ddev/providers/acquia.yaml`
* `.ddev/commands/web/setupProject`
* `.env.local`
* `hooks`

## Updating Readme.

Please update the `README.md` to be relevant to the new project.
