## Setting up local development

1. Clone the repository: `git clone git@github.com:forumone/drupal-project.git`
1. Go to the project directory: `cd drupal-project`
1. Go to the .git/info directory: `cd .git/info`
1. Edit the exclude file:
```
web/themes/gesso
.ddev/config.yaml
.ddev/.global_commands/*
.ddev/traefik/*
composer.lock
composer-manifest.yaml
```
1. Follow steps 2-5 from [setting up a new project](./README.md)
1. Return to project root: `cd ../..`
1. Install composer dependencies: `ddev composer install`
1. Run drupal site install: `drush si -y --existing-config`
1. Log in to drupal: `ddev drush uli`

You're now ready to start contributing to Drupal-Project. See instructions for testing & deploying new work on the demo site [here](https://github.com/forumone/drupal-project-demo/blob/main/docs/CONTRIBUTORS.md)

## Adding a recipe

### Basic approaches
If a feature has been previously built that seems like a good fit as a recipe in Drupal starter, one basic approach is to delete the feature and use the removed configuration as a basis for a new recipes configuration. For example, if you have an event content type, trying deleting the content type to list all of the associated config that would be removed.

We use the naming convention of `[feature_name]_[entity]` for creating recipes.

If configuration is shared between multiple recipes add to the existing base recipes like `web/recipes/content_base`, `web/recipes/paragraph_base`, or add your own base recipe.

### Ingredients
Ingredients are what we call modules that are necessary for a particular recipe to run. They are listed in an `ingredients.txt` file located in the root of a recipe. For an example see `web/recipes/dynamic_list_paragraph`. If you have multiple composer dependencies add each on a new line. If you require a specific version for a composer dependency you can write the ersion name just as you would if you were using `composer require` e.g. `drupal/facets:^3.0`.

### Default content

The default content module is included with the common composer dependencies. To add default content for a feature, the recommended approach is:

1. `ddev drush en default_content -y`
2. Create your content in Drupal
3. `ddev drush dcer {entity_type} {id} --folder={path/to/recipe/content/folder/from/web}`, i.e ddev drush dcer node 1 --folder=recipes/landing_page_content/content

By virtue of content existing in a `content` directory the recipe installer will handle the rest.

**Note:** You don’t need to have the default_content module in the project for recipe default content to work

### Recipe resources
* [Recipes Cookbook](https://www.drupal.org/docs/extending-drupal/contributed-modules/contributed-module-documentation/distributions-and-recipes-initiative/recipes-cookbook)
* [Recipes Developer Documentation](https://git.drupalcode.org/project/distributions_recipes/-/tree/1.0.x/docs)
* [Kanopi Comprehensive Guide to Drupal Recipes](https://kanopi.com/blog/the-comprehensive-guide-to-drupal-recipes/)
* [Kanopi Guide to Default Content in Drupal](https://kanopi.com/blog/default-content-in-drupal/)
