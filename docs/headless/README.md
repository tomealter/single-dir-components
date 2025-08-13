# Headless Drupal

The approach for headless will allow both a Next.js front-end and a Drupal back-end to exist in the same project structure.

## Step 1: Configure Drupal
At the end of this step you should have a working Drupal site that lives in a subfolder within the project

1. Please clone the repo: `degit -m=git git@github.com:forumone/drupal-project.git [project_name]`
2. In the root of the directory create a new "cms" directory: `cd [project_name] && mkdir cms`
3. Now we need to move all the Drupal files out of the root and move them into the new `cms` directory.
   1. Move non-directory files to the `cms` directory: `find . -maxdepth 1 -type f -exec mv {} cms/ \;`
   2. Move all the necessary drupal specific directories from the root to the new `cms` directory -- `mv web hooks config composer-dependencies drush scripts cms/`
6. Remove the gesso and storybook docker-compose files: `rm .ddev/docker-compose.gesso.yml .ddev/docker-compose.storybook.yaml`
7. Remove the default ddev config.hooks.yaml: `rm .ddev/config.hooks.yaml`
8. Ensure the .env is registered: `cp cms/.env.local cms/.env`
9. Run `ddev config`
   1. When prompted for the "Docroot Location (current directory)" type `cms/web`
   2. When prompted for the "Project type"  type `drupal10` 
11. Set directory for where composer should run: `ddev config --composer-root cms`
12. Set the correct PHP version: `ddev config --php-version 8.3`
    1. Not needed if on ddev version >= `1.24.0` as this is the default for a `drupal10` project
13. Set the working directory for web: `ddev config --web-working-dir /var/www/html/cms`
14. Set environment variables for the Drupal Consumer (used in later steps): `ddev config --web-environment DRUPAL_CLIENT_SECRET=drupal_client_secret_not_secure_used_only_locally,DRUPAL_CLIENT_ID=drupal-client-id`
14. Ensure your settings.local files are in place: `cp cms/web/sites/default/example.settings.local.php cms/web/sites/default/settings.local.php && cp cms/web/sites/default/example.services.local.yml cms/web/sites/default/services.local.yml`
15. `ddev start`
16. Install composer, install Drupal and launch as admin: `ddev composer install && ddev drush si -y && ddev launch $(ddev drush uli)`
17. Generate oauth keys (used in later steps): `ddev generate-oauth-keys`
17. Enable all recipes: `ddev applyRecipe all` (This is a temporary requirement until the headless starter supports recipes)
18. Clear Drupal cache: `ddev drush cr`

## Step 2: Configure Front-end
At the end of this step we should have the Next.js app inside the project under its own 'frontend' directory
1. From the project root: `mkdir frontend && cd frontend`
2. Install the Next.js project inside the "frontend" directory: `degit --mode=git git@github.com:forumone/nextjs-project.git`
3. Move the front-end's `config.hooks.yaml` file to the root `.ddev` folder: `mv .ddev/config.hooks.yaml ../.ddev`
4. Find and replace `web` with `frontend` in the `config.hooks.yaml` file. The command needed varies slightly based on your OS.
   1. Windows/Linux: `sed -i 's/web/frontend/g' ../.ddev/config.hooks.yaml`
   2. Mac: `sed -i '' 's/web/frontend/g' ../.ddev/config.hooks.yaml`    
5. Move the front-end's Drupal docker-compose file to the root `.ddev` folder: `mv starterkits/drupal/docker-compose.frontend.yaml ../.ddev/`
6. Create a new directory for frontend commands: `cd ../.ddev && mkdir commands/frontend`
7. Copy the commands from the `/frontend/.ddev/commands/web` to the root frontend command directory: `cp -R ../frontend/.ddev/commands/web/* commands/frontend`
8. Copy the `frontend/.ddev/commands/host/setup-drupal` command root ddev commands: `cp -R ../frontend/.ddev/commands/host/* commands/host`
9. Now remove the `.ddev` folder inside the Next.js project `cd ../ && rm -Rf frontend/.ddev`
10. Cleanup and remove additional next directories `rm -Rf frontend/.buildkite frontend/.github`
11. Create a new directory inside the root `.ddev` directory: `mkdir .ddev/frontend`
12. Add a `Dockerfile` inside the newly-created `frontend` directory: `echo -e "FROM node:20-buster\n\nRUN npm install -g npm@10.9.1 pm2" > .ddev/frontend/Dockerfile`
13. `ddev restart`. You may get errors from pm2 about scripts not found. That is expected and will be fixed in the next step.
14. Now run the `ddev setup-drupal` command.
15. You should now be able to visit `[projectname].ddev.site:3000` to access the Next.js site and `[projectname].ddev.site:6006` to access Storybook..

## Step 3. Making Drupal and Next.js Talk
By the end of this step we should have a Drupal site that is configured to allow the specified content types exposed via GraphQL so that the Next.js Project can request the data and display it on the front-end site.

### Step 3.1 Separate host for frontend
First we need to make it so that the front-end responds to a different hostname than Drupal. To do that we need to do the following:
1. `ddev config --additional-hostnames=frontend` -- "frontend" can really be anything here, but this is the hostname that we'll use to access the Next.js site.
2. Create an nginx configuration file that will respond to when we request `frontend.ddev.site` to respond with the Next.js site: `cd .ddev/nginx_full` then `touch frontend.conf` and paste the following. _Note: if you called the hostname something other than "frontend" you'll need to change the `server_name` line to match your hostname to match and change the filename to [yourname].config. You should NOT change the other references to `frontend` in the file, because that refers to your Docker service name._
```
server {
    # This is the line you will change if you used a different hostname
    server_name frontend.ddev.site;

    listen 80;
    listen 443 ssl;

    ssl_certificate /etc/ssl/certs/master.crt;
    ssl_certificate_key /etc/ssl/certs/master.key;

    location / {
      proxy_pass http://frontend:3000;
    }

    location /sites/default/files {
      proxy_pass http://web:80;
    }

    location /media/oembed {
      proxy_pass http://web:80;
    }

    location /_next/webpack-hmr {
      proxy_pass http://frontend:3000/_next/webpack-hmr;
      proxy_http_version 1.1;
      proxy_set_header Upgrade $http_upgrade;
      proxy_set_header Connection "upgrade";
    }

    include /etc/nginx/monitoring.conf;
}
```
3. `ddev restart` and you should now be able to access the Next.js site at `[frontend].ddev.site`. Storybook is at `[frontend].ddev.site:6006`.

### Step 3.2 Configuring GraphQL for Drupal
1. Enable the headless recipe: `ddev applyRecipe f1_headless_kit`
   1. This seems to initially fail. You need to `ddev drush cr && ddev applyRecipe f1_headless_kit` to get this to apply correctly
2. Run the drush command to create the consumer: `ddev drush f1_next:setup-users-and-consumers`
3. Clear Drupal cache `ddev drush cr`
3. Now head to the frontend directory and modify the `.env.local` and paste the following. (TODO:Update these to pull from the environment variables set in ddev's config.yaml from Step 1. #11)
```
NEXT_PUBLIC_DRUPAL_BASE_URL=http://web
NEXT_IMAGE_DOMAIN=web

# Authentication
DRUPAL_CLIENT_ID=drupal-client-id
DRUPAL_CLIENT_SECRET=drupal_client_secret_not_secure_used_only_locally
```
5. `ddev frontend restart next` to apply the .env.local changes
6. Finally, create an Article or Basic page (one of the "entity types" we enabled in GraphQL settings earlier) and be sure to give the node a URL alias.
7. You should see after saving when on the "View" tab the Next.js preview of the frontend site in the iFrame below. If you visit the same slug on the frontend site, https://frontend.ddev.site/[your-path] for a basic page and https://frontend.ddev.site/blog/[your-path] for an article, you should see the contents of the page there.
8. Until https://github.com/forumone/nextjs-project/issues/168 is resolved, you may need to delete references in queries to any paragraph types, image styles, etc. that do not exist on your site.
9. For the preview functionality to work and until https://github.com/forumone/nextjs-project/issues/182 is resolved, you may need to update [frontend_directory]/app/api/preview/route.ts where you see
```
  let slug = query.get('slug');
```

to 

```
  let slug = query.get('path');
```
