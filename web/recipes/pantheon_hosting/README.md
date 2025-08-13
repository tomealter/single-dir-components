# Pantheon Hosting Recipe

This recipe is really straightforward, but you do need to add the following to your root composer.json in the "extra" section for this to work properly.

See https://github.com/pantheon-systems/drupal-integrations?tab=readme-ov-file#enabling-this-project 

    "extra": {
        "drupal-scaffold": {
            "allowed-packages": [
                "pantheon-systems/drupal-integrations"
            ]
        }
    }

## Post-apply
After applying the recipe be sure to follow our docs for additional steps to configure your site and buildkite settings properly [here](../../../docs/hosting/pantheon.md)