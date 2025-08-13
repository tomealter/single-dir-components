ARG PHP_VERSION="8.3"
ARG COMPOSER_VERSION="2.3"
ARG NODE_VERSION="20"

FROM forumone/composer:${COMPOSER_VERSION}-php-${PHP_VERSION} AS base

# In some instances this is required.
WORKDIR /var/www/html

# This will copy everything into the dockerfile other than
# those excluded in the .dockerignore
COPY . .

# Install without dev dependencies
RUN set -ex \
  && composer install --no-dev --optimize-autoloader \
  && composer drupal:scaffold

#Gesso
FROM forumone/gesso:node-v${NODE_VERSION}-php-${PHP_VERSION} AS theme-base

WORKDIR /app

RUN npm install --global npm@10.7.0

COPY ["web/themes/gesso", "./"]

RUN if test -e package-lock.json; then npm ci; else npm i; fi

FROM theme-base AS theme

RUN set -ex \
  && npm run build-storybook \
  && npm run build \
  && rm -rf node_modules

# Linting
FROM base AS php-linting

RUN composer install

# Building artifact
FROM busybox AS artifact

WORKDIR /var/www/html

COPY --from=base ["/var/www/html", "./"]
COPY --from=theme ["/app", "web/themes/gesso"]

RUN if test -e .env.production; then mv .env.production .env; fi
FROM artifact
