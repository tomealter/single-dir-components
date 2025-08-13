# Workspaces Recipe

This recipe is a combination of the core Workspaces module and the Workspace Extra & Workspace Plus contrib modules.

> A workspace is a copy of the live site, that exists in parallel to the live site. It provides the ability to have multiple environments to work on a Drupal site, where you can stage changes and then deploy them to the production site. In essence, a workspace is a set of configurations and content, separated from the main site, which can be edited and tested without affecting the live site.

![image](https://www.drupal.org/files/issues/workspace-concept.png)

This recipe contains:

* An administrative user interface for managing workspaces. A "Stage" workspace is created by default. Site editors can create additional workspaces as needed.
* Mild customizations of the Workspaces & Workspace Extras modules

## Requirements

- Drupal 10.3.6 or later
- PHP 8.2 or later
- Drush 13.x
- ddev & composer based project
- Knowledge of applying composer patches

## Instructions

### Adding this recipe to your existing Drupal project

If you would like to add this recipe to your existing project, copy this recipe to your project's `[docroot]/recipes/` directory.

Modify your project's composer.json file and add this in the `repositories` section:

```
{
  "type": "vcs",
  "url": "https://github.com/WembassyCo/workspace_plus"
}
```

Require these composer packages:

`ddev composer require drupal/wse:^2.0@alpha wembassyco/workspace_plus`

Run this recipe

`ddev drush recipe [path-to-recipes]/workspaces/`

## Resources

Learn more about the power of Workspaces here

[Hack and Play: Workspaces in Drupal core](https://www.youtube.com/watch?v=zaQaK_3UcRg)

[Drupal 8 workspaces preview](https://www.youtube.com/watch?v=3JwkLA--Ciw)

[Drupal Workspaces: A Game-changer for Site Wide Content Staging - Tag1 Team Talks](https://www.youtube.com/watch?v=Q8hfzqZo-uE)

