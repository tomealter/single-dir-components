# Drupal Project Template

![Actively Maintained Badge](https://img.shields.io/badge/Forum_One-Actively_Maintained-E9162B?logo=data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAMAAABEpIrGAAABO1BMVEUAAAD////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////+9fb5v8T++Pj++PnzipLuW2f+9vfzjZXoHy/uVWH+9/jpIjLoGSnuVmLoHCzoGiroHS3nGCjvZnH//Pz99/fzjJXoITHoGyvrNkT4vsL5+fq+t8mPhKKLgJ+Kf56Rh6Tbx9H4rbL2o6r2pKv2qa/71dj//v6yqr9DMWM5Jls6Jls5JVo+K1+bkaz49/n6+vt0Zow9Kl48KV1CL2Kfla9tX4Y6J1xsXoU/LF/39/lyZYqckq35+PrJxNL39vie4JhbAAAAKXRSTlMAACSF1PfWiCYBTdH9/lEC6OtTzymN09r2+9XckCfS1ytS7VfYKNv83drg2w4AAAD8SURBVDjLhdPBSsNAFEbh/9yZSlMRU0qLUgQXXdid7/8WLoUiCLG40BpbtQTFuEgsaZOrd5XJd7KYMIMQwEyHswAKldAHLtQ12XbAGpKjc3nzyIuRuI5N1yOj8DzGGBOYjFwH3ldmf3wfJbOh6yFISs33iuwf7w4a3hk0vSsITe8IQq/p7eDAW8Gef7WDPbckkZg3fQxsd6sByyqYApIE1E+ql7dVcFW9BrhpH4q5rin6lS8f2n/FpLJ34vtuF57/Bq7Xge+yhaSN77mNJTt1XWbP2XEIrgubDEMIrn9EuMzP3KtXcGdapa9ZN2efPAmhWT7cpC3+5i3eq/wBbgQ5Z2KL1QoAAAAASUVORK5CYII=)
![Drupal 10.3](https://img.shields.io/badge/Drupal-10.3.x-00598E?logo=drupal)

This repository holds the scaffolding for Drupal projects built by Forum One.

## Requirements / Dependencies
To setup a new project you will need the following:
* Download Docker
  * [Docker for Mac](https://store.docker.com/editions/community/docker-ce-desktop-mac)
  * [Docker for Windows](https://store.docker.com/editions/community/docker-ce-desktop-windows)
* [Node.js](https://nodejs.org/en/)
* Please install: `npm install -g tiged`
* [DDev Installed](https://forumone.atlassian.net/wiki/spaces/TECH/pages/2859270145/Installing+DDev)

## Setting up a new project.
If you're starting a new build project follow these instructions below. 
- If you are wanting to **contribute to drupal-starter** follow the setup instructions [here](./CONTRIBUTORS.md).
- If you are creating a **headless project** skip the below and follow the instructions [here](./headless/README.md).

1. Please clone the repo: `degit -m=git git@github.com:forumone/drupal-project.git [project_name]`
1. Run `ddev config`
    * Project name (`[project-name]`): `[project-name]<ENTER>`
    * Docroot Location (...): `web<ENTER>`
    * Project Type [...]: `drupal10<ENTER>`
   * If `drupal10` was not an option above or the PHP version isn't already set to PHP 8.3 you'll likely need to update ddev to `>=1.24.0`. To manually set the php version to 8.3 run `ddev config --php-version 8.3`
1. Add the theme to the theme directory (required even if you don't use it), [Setup theme](services/theme.md)
1. Start DDev: `ddev start`.
1. Install package dependencies `ddev composer install`
1. Install drupal `ddev drush si minimal -y`

## Standard build
* Run `ddev composer require forumone/canvas` to require the recipe.
* Run `ddev applyRecipe canvas` to apply our standard recipe.
  * _Note: This recipe features a few prompts to set the initial site name and site email address._  
* Run `ddev composer unpack forumone/canvas && ddev composer unpack forumone/drupal_base` to unpack all the recipe's composer dependencies into your project's root composer.json file.
* Run `ddev launch $(ddev drush uli)` to launch a new browser window logged in as user 1:

### Recipes
During setup you can also enable any recipes that you would like to make use of. We ship with a number of Paragraph type recipes and a Landing Page content type recipe.
Feel free to review those in `web/recipes`

To apply a recipe run `ddev applyRecipe [recipe]`

A kitchen sink demo of all available recipes with is available.

1. Apply all recipes `ddev applyRecipe all`
2. To include default content `ddev applyRecipe default_content`

For more information about available recipes like hosting recipes or technical guides see `docs/recipes`.

## Hosting providers
Please follow the relevant guide depending on your hosting situation.
* [Pantheon Hosting](hosting/pantheon.md)
* [Forumone Hosting](hosting/f1.md)
* [Acquia Hosting](hosting/acquia.md)
* [Generic README.md](../README.project.md)
