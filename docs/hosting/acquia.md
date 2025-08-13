# Drupal Project template for Acquia

This is a build guide to help walk you through setting the project up for Acquia hosting.

## Getting started...
* Please stop the project: `ddev stop`
* Please rename the following files:
  * `.buildkite/pipeline.acquia.yml` > `.buildkite/pipeline.deploy.yml`
* Need to modify the following variables:
  * `.buildkite/pipeline.deploy.yml` > `[ACQUIA GIT REPO]`
  * `.buildkite/pipeline.deploy.yml` > `[ACQUIA HOST]`
  * `.ddev/providers/acquia.yaml` > `[PROJECT ID]`
  * `.ddev/providers/acquia.yaml` > `[DATABASE NAME]`
* `web/sites/default/settings.php` > `[ACCOUNT NAME]`
* Please look through, [ref](https://github.com/forumone/VA-DMV-VITA-dev-2022/blob/main/docroot/sites/default/settings.php#L755-L806) and place it somewhere in the `web/sites/default/settings.php`
* Rename `web` to `docroot`
* Please change `web` to `docroot` in:
  * `phpstan.neon.dist`
  * `phpstan.baseline.neon`
  * `Dockerfile`
  * `.ddev/docker-compose.gesso.yaml`
  * `.ddev/docker-compose.storybook`
* Rename `README.project.md` > `README.md`
* Please continue down the readme...

Example for variable names:
`accountName@svn-45120.prod.hosting.acquia.com:accountName.git`
* `[ACQUIA GIT REPO]` > `accountName@svn-45120.prod.hosting.acquia.com:accountName.git`
* `[ACQUIA HOST]` > `svn-45120.prod.hosting.acquia.com`
* `[PROJECT ID]` > `accountName.test`
* `[DATABASE NAME]` `accountName` (default: uses account name)
* `[ACCOUNT NAME]` `accountName`
*
This is a build guide to help walk you through setting the project up for Acquia.

### Need to modify Acquia environments for the branches below

Keep in mind that the target branches will need to be configured within `Acquia cloud`.
```yaml
- match: integration
  target: integration
- match: main
  target: main
- match: live
  target: live
```

## Additional services

Please go through the following if you need them, please go through the (required) section.

* (optional) [Setup memcache](../services/memcache.md)
* (optional) [Setup solr](../services/solr.md)

**NOTE** Additional resources can be found [here](../services/optional.md)


## Cleaning up

Please delete the following:
* `docs`
* `.buildkite/pipeline.f1.yml`
* `capistrano`
* `.ddev/web/terminus`
* `.ddev/commands/web/setupProject`

## Updating Readme.

Please update the `README.md` to be relevant to the new project.

## Do you need to configure config_split?

If yes, the basic Acquia config split that can be modified is in the `./web/sites/default/settings.php`


## Special notes about deploying to Acquia

If you are using a project that requires `fed complient`, then you have to look at `hooks`. More information can be found [here].

[here]:https://docs.acquia.com/cloud-platform/develop/api/cloud-hooks/

In this project a basic one is setup to automatically run `drush deploy -y` upon every commit.
