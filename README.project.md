# <Project Name> [![Build status](<buildkite-badge-url>)](https://buildkite.com/forum-one/<project-uri>)
![Actively Maintained Badge](https://img.shields.io/badge/Forum_One-In_Development-E9162B?logo=data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAMAAABEpIrGAAABO1BMVEUAAAD////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////+9fb5v8T++Pj++PnzipLuW2f+9vfzjZXoHy/uVWH+9/jpIjLoGSnuVmLoHCzoGiroHS3nGCjvZnH//Pz99/fzjJXoITHoGyvrNkT4vsL5+fq+t8mPhKKLgJ+Kf56Rh6Tbx9H4rbL2o6r2pKv2qa/71dj//v6yqr9DMWM5Jls6Jls5JVo+K1+bkaz49/n6+vt0Zow9Kl48KV1CL2Kfla9tX4Y6J1xsXoU/LF/39/lyZYqckq35+PrJxNL39vie4JhbAAAAKXRSTlMAACSF1PfWiCYBTdH9/lEC6OtTzymN09r2+9XckCfS1ytS7VfYKNv83drg2w4AAAD8SURBVDjLhdPBSsNAFEbh/9yZSlMRU0qLUgQXXdid7/8WLoUiCLG40BpbtQTFuEgsaZOrd5XJd7KYMIMQwEyHswAKldAHLtQ12XbAGpKjc3nzyIuRuI5N1yOj8DzGGBOYjFwH3ldmf3wfJbOh6yFISs33iuwf7w4a3hk0vSsITe8IQq/p7eDAW8Gef7WDPbckkZg3fQxsd6sByyqYApIE1E+ql7dVcFW9BrhpH4q5rin6lS8f2n/FpLJ34vtuF57/Bq7Xge+yhaSN77mNJTt1XWbP2XEIrgubDEMIrn9EuMzP3KtXcGdapa9ZN2efPAmhWT7cpC3+5i3eq/wBbgQ5Z2KL1QoAAAAASUVORK5CYII=)
![Drupal 10.3](https://img.shields.io/badge/Drupal-10.3.x-00598E?logo=drupal)

## Development Branch Naming & Workflow

- Always create Feature and Bugfix branches from the `main` branch.
- When needed, create Hotfix branches from `live`.
- Merge, or submit a Pull Request, from Feature/Bugfix/Hotfix branches to `integration` for Development/Integration environment deployments.
- Submit a Pull Request from Feature/Bugfix branches to `main` for QA/Staging environment deployments.
- Submit a Pull Request from Hotfix branches to `live` for Production environment deployments.
- Submit a Pull Request from `main` to `live` for all regular Production releases.

## Project Setup Instructions

To set up the local environment, do the following:

1. Install [`docker`](https://docs.docker.com/install/) and [`docker-compose`](https://docs.docker.com/compose/install/), if not already installed.

2. Install [`DDEV`](https://ddev.readthedocs.io/en/stable/#installation)

**NOTE:** If you are setting up Pantheon for the first time, please follow these instructions to be able to pull the database & files from a [Pantheon environment](https://ddev.readthedocs.io/en/stable/users/providers/pantheon/#pantheon-quickstart), Please only follow `1 & 2`.

3. Clone this repository.
```
git clone [INSERT git repo URL]

cd [INSERT repo directory]
```

4. Run:
```
ddev start && ddev gesso build
```
**NOTE:** Older projects may also need a `ddev gesso install` step in the middle. Verify whether this is the case, update the above command if needed, then remove this note.

5. Install Drupal core and plugins.
```
ddev composer install
```


**Please select which variant to get the database.**

6a. Installing the database from Pantheon. 
```
ddev pull pantheon --skip-files
```
**NOTE:** If you receive an error that there are no backups available, then login to the Pantheon site and create a backup in the corresponding environment.

6b. Installing the database from Acquia
```
ddev pull acquia --skip-files
```

6c. Installing the database from Forum One hosting (make sure you have smallstep setup)

  1. Need to authenticate to smallstep if the project is setup.
  ```
  ddev step-auth
  ```
  2. Verify that you have access via smallstep
  ```
  ddev step ssh hosts
  ```
  3. Pull database
  ```
  ddev drush sql-drop -y; ddev drush sql-sync @alias -y
  ```

7. Flush the cache.
```
ddev drush deploy
```

## Building/Watching Theme File Changes
1. Build the theme assets.
```
ddev gesso build
```
2. Watch for changes
```
ddev gesso watch
```

## Environments

 * Dev:
 * Stage:
 * Prod:


## Site port allocation:

|Service|Port|
|--|-------|
|Web|80|
|Gesso|  x  |
|Storybook|6006 & 6007|
|Mailpit|8026|

**NOTE:** To see all ports and URLS you can run `ddev status`

Newer versions of ddev no longer bundle PHPMyAdmin. To add it, get [the ddev plugin](https://github.com/ddev/ddev-phpmyadmin).

## Additional Resources

#### Xdebug

* To enable `ddev xdebug on`
* To disable xdebug `ddev xdebug off`

#### Linting

Additional documentation for linting, including setting up your IDE to reporting on issues as your making changes, can be found in Confluence in the [Tech Team Linting section](https://forumone.atlassian.net/wiki/spaces/TECH/pages/32964621/Linting).

Following linting tools are available for local development with their current commands:
* `phpstan` > `ddev phpstan` > Uses config file `phpstan.neon` and excluding specific project folders in the composer file. If any errors are found then a `phpmd-report.json` is generated at the project root.
* `phpcs` > `ddev phpcs` > Runs standard command outlined in the `phpcs.xml.dist`
* `eslint` > `ddev gesso eslint` > Only scans the theme folder. (Default: web/themes/gesso)
* `stylelint` > `ddev gesso stylelint` > Only scans the theme folder. (Default: web/themes/gesso)

These commands can be found in the following directory: `.ddev/commands/web` & `.ddev/commands/gesso`

#### Custom commands
* `ddev phpstan` > Runs `phpstan` in the Web service
* `ddev phpcs` > Runs `PHP_Codesniffer` in the Web service
* `ddev gesso install` > Runs `npm ci` for `gesso`  in the Gesso service
* `ddev gesso build` Runs `npm run build` in the Gesso service
* `ddev gesso deploy` > Runs `npm run build-storybook` in the Gesso service
* `ddev gesso eslint` > Runs `npx eslint source/**/!\(*.stories\).js` in the Gesso service
* `ddev gesso stylelint` > Runs `npx stylelint "source/**/*.scss"` in the Gesso service
* `ddev gesso watch` Runs `npm run watch`  in the Gesso service
* `ddev storybook restart` > Runs `pm2 restart storybook` in the Storybook service
* `ddev storybook monit` Runs `pm2 monit` in the Storybook service

#### For default services

#### Performance for local development

With `DDev 1.19` there was a service that was included called `Mutagen`. Mutagen allows for a `2 way sync` for the code base into a `docker container`.
Some of the things to think about when using `Mutagen`
* Uses a lot more disk space
* Can get out of sync if you run `npm install` for the first time, `composer install` installing a lot of new packages, and huge `git` operations
  * To re-sync `ddev mutagen sync`
  * To monitor mutagen `ddev mutagen monitor`

Reference: [Mutagen documentation]
<br />
<br />
To enable mutagen for a specific project please create a file `.ddev/config.mutagen.yaml` **DO NOT COMMIT THIS FILE.**
 ```yaml
 mutagen_enabled: true
 ```
**NOTE:** Note mutagen is only really intended for Mac OS, but it may also be used on Windows.

#### Pantheon account

* Add your SSH key that was generated into Pantheon
  * Login to Pantheon
  * Go to `Account` tab
  * Go to `SSH` tab and input your *.pub key
* Generating `Machine token` for Terminus
  * Log into Pantheon
  * Go to the `Account` tab
  * Click on: `Machine Tokens` on the left hand side of the menu
  * Click on: `Create token`
  * Put a name to identify the token (example: local Terminus), re-authenticate as soon as you login you will be given a auth:key. Will need this key in a later step

#### Add Github OAuth token to project

1. Copy auth.json.example to auth.json
2. Visit your Github profile [Settings > Developer Settings > Personal access tokens > Generate new token](https://github.com/settings/tokens/new). If you have an access token generated prior to March 31, 2021, you will need to regenerate your token. Please visit [Authentication token format updates are generally available](https://github.blog/changelog/2021-03-31-authentication-token-format-updates-are-generally-available/)
3. Give your token a note, like "Composer"
4. Select "repo" for the scope
5. Click "generate token"
6. Copy the token and update auth.json replacing `your token goes here` with your actual token
7. Save auth.json

#### Setting up SmallStep

1. [Setting up smallstep](https://forumone.atlassian.net/wiki/spaces/TECH/pages/2820145219/Small+Step+SSH+Client+Install)
2. Copy your `~/.ssh` folder to `~/.ddev/homeadditions/.ssh`
3. Restart the project

#### Acquia account to setup for local development (Please remove if not setting up Acquia hosting)

* Add your SSH key that was generated into Acquia
    * Login to Acquia
    * Go to `https://cloud.acquia.com/a/profile/ssh-keys`, and click `Add SSH Key`
    * Add your `ssh public key` input your `*.pub key`
* Generating `API Token` for `ddev`
    * Log into Acquia
    * Go to: `https://cloud.acquia.com/a/profile/tokens`
    * Click on: `Create token` on the right side of the page
    * Edit: `~/.ddev/global_config.yaml`
      ```yaml
      ...
      web_environment:
        - ACQUIA_API_KEY=XXXXXXX
        - ACQUIA_API_SECRET=XXXXXXX
      ...
      ```
