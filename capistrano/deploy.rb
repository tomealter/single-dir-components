# The name for the application, should only be things that can be in a directory name
set :application, 'APP_NAME'

# The repo URL
set :repo_url, 'HTTPS_REPO_URL'

# Or use the line below to deploy via rsync
set :scm, :rsync

# Use a remote cache for git
set :deploy_via, :remote_cache

# Which logging formatter to use
set :format, :pretty

# Which logging level to use
set :log_level, :debug

# Whether to use sudo for commands
set :user_sudo, false

# Whether to use a pseudo-TTY
set :pty, true

# Number of release directories to keep
set :keep_releases, 3

# Add custom SSH config
set :ssh_options, {
  config: 'config/ssh_config'
}

# Platform
set :platform, "drupal9"

# rsync settings
set :rsync_options, %w[--links --recursive --chmod=Dug=rwx,Do=rx --perms --delete --delete-excluded --exclude=.git* --exclude=node_modules]
set :rsync_copy, "rsync --archive --acls --xattrs"
set :rsync_cache, "shared/deploy"

set :rsync_stage, 'artifacts'

set :app_webroot, 'web'
set :webroot, 'web'

set :linked_dirs, ["web/sites/default/files", "private"]
set :linked_files, [".env"]

set :drupal_features, false
