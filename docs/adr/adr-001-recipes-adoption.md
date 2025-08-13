# ADR 001: Recipes adoption

**Status**: Accepted ✅

---
## Decision
Forum One will adopt the use of Drupal's [Recipe API](https://www.drupal.org/docs/extending-drupal/drupal-recipes) to enable reusable, common features included on typical Drupal sites.

## Context
Prior to Drupal 10.3's release of recipes we had taken the approach of building _"starter-kits"_ which were essentially modules that only provided default configuration for content types / components we often use on most projects.
Since then Drupal has released a new feature called _Recipes_ that allows for this same functionality in a more lightweight and extendable way.

Recipes is the preferred way of creating reusable configuration for the following reasons:
1. Recipes are not like modules in that once they are applied they can be removed from the codebase and you will not be locked in to the specifics of a recipe once applied unlike a distribution.
2. Recipes can allow you to import specific configuration items and allows you to create / alter other configuration with the use of [Config Actions](https://git.drupalcode.org/project/distributions_recipes/-/blob/1.0.x/docs/config_action_list.md).
3. Recipes are extendable and can built upon one another. 

## Consequences
This is a new feature that's still in development at the time of writing (12/20/2024) and there are still lots of unknowns to figure out with recipes. However, the benefits from this are greatly impactful and speed up our ability to get a baseline solution solved for common problems which will allow us to spend our time on more valuable problem solving.
To make the most of recipes we will need to extract our common features out into small, composable units that can enable developers to pick and choose what they want to have enabled on the sites they build.

In addition to this adoption, we will also need to provide documentation and trainings for folks to get them up to speed on how to use recipes. We will need to teach developers to think about looking for a recipe solutions first, similar to how we look to see if a Drupal contrib module exists to solve a particular issue before building a custom module that does what we want.