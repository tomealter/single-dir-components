#!/bin/bash

site="$1"
target_env="$2"
source_branch="$3"
deployed_tag="$4"
repo_url="$5"
repo_type="$6"

if [ "$target_env" != 'prod' ]; then
  if [ "$target_env" != "ra" ]; then
    echo "$site.$target_env: The $source_branch branch has been updated on $target_env. Running drush deploy."
    # Run updates, import config, clear cache
    drush @"$site.$target_env" deploy -y -vv

    # Check if search_api module is installed and enabled
    if drush @"$site.$target_env" pm-list --type=module --status=enabled --no-core | grep -q "search_api"; then
      echo "search_api module is enabled, running reindexing commands."
      # Mark content for reindexing
      drush @"$site.$target_env" sapi-r
      # Reindex
      drush @"$site.$target_env" sapi-i
      # Clear cache
      drush @"$site.$target_env" cr
    else
      echo "search_api module is not enabled; skipping reindexing commands."
    fi

  fi
else
    echo "$site.$target_env: The $source_branch branch has been updated on $target_env."
fi
