# Is gesso theme going to be used?

## If Yes,

Will need to modify the following files on a project basis

* Please add the latest `gesso theme` to the `web/themes` folder from downloading [github gesso] or `degit --force git@github.com:forumone/gesso.git gesso`.
* Then remove the following directories
    * .buildkite
    * .github

[github gesso]:https://github.com/forumone/gesso/releases

## If No,

**Note:** If you will be using a theme that will still require a build process then you shouldn't delete the gesso-related files but rename the `gesso` references to `theme`. For example rename `.ddev/docker-compose.gesso.yml` to `.ddev/docker-compose.theme.yml`

* Delete the following files
    * `.ddev/docker-compose.gesso.yaml`
    * `.ddev/commands/gesso`
    * `.ddev/config.hooks.yaml`
    * `.ddev/docker-compose.storybook.yaml`
    * `Dockerfile` and delete any references to `gesso`
* Edit the following files **If the new theme requires building**
  * `Dockerfile` > Look at any reference that mentions `gesso`
    **NOTE:** You can probably keep the `FROM forumone/gesso` as the base image
  * `phpstan.neon.dist` > Any references for theme scanning
  * `.phpcs.xml.dist` > Any references to theme scanning
  * If the theme does not support `ESLint` or `Stylelint`
    * Update the `.buildkite/pipeline.HOSTING.yml` to remove those `steps`
Will need to modify the following scripts for the correct `theme location`
* `.ddev/commands/gesso/gesso`
* In the `.ddev/config.yaml` section delete the `hooks:` section.

