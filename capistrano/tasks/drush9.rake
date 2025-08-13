# A set of tasks to trigger operations with Drush 9 on the remote system during deployment
#
# Since Drush 9 will only work with Drupal 8.4+ no back support for Drupal 7 is required.
#
# Tasks:
# - :initialize: Creates a ~/.drush directory and copies aliases from the release
# - :initialize:drushdir: Creates ~/.drush/sites directory if it's missing
# - :initialize:aliases: Copies any aliases from the root checkout to the logged in user's ~/.drush directory
# - :site:install: Triggers drush site-install
# - :rsync: Copies files from a remote Drupal site, assumes ENV['source'] provided which is a drush alias
# - :cacheclear: Clears or rebuilds the Drupal cache
# - :deploy: Runs drush deploy
# - :cr: (Drupal 8) Rebuilds the entire Drupal cache
# - :update: Runs all pending updates, including DB updates, Features and Configuration -- if set to use those
# - :db:update: Runs update hooks
# - :db:revert: Drop the current database and roll back to the last database backup
# - :db:dump: Dumps the database to the current revision's file system
# - :db:sync: Copies a database from a remote Drupal site, assumes ENV['source'] provided which is a drush alias
# - :db:drop: Drops the database and all content
# - :configuration:import: (Drupal 8) Import Configuration into the database from the config management directory
# - :sapi:reindex: Clear Search API indexes and reindex each
#
# Variables:
# - :drupal_db_updates: Whether to run update hooks on deployment -- defaults to TRUE


namespace :load do
  task :defaults do
    set :settings_file_perms, "644"
    set :site_directory_perms, "750"
  end
end

namespace :drush9 do
  namespace :custom do
    desc "Apply Drupal database updates and import configuration."
    task :update do
      on roles(:db) do
        within "#{release_path}" do
          invoke "drush9:updatedb"
          invoke "drush9:cache:rebuild"
          invoke "drush9:config:import"
        end
      end
    end

  desc "(Drupal 9) Runs drush deploy"
  task :deploy do
    on roles(:db) do
      within "#{release_path}/#{fetch(:app_webroot, 'public')}" do
        fetch(:site_url).each do |site|
          execute :drush, "-y -r #{current_path}/#{fetch(:app_webroot, 'public')} -l #{site}", 'deploy'
        end
      end
    end
  end

    desc "Drop the current database and roll back to the last database backup."
    task :revertdb do
      on roles(:db) do
        within "#{release_path}" do
          invoke "drush9:sql:drop"
          execute :drush, "--yes", "sql:connect < #{last_release_path}/db.sql"
        end
      end
    end
  end

  desc "Apply any database updates required (as with running update.php)."
  task :updatedb do
    on roles(:db) do
      within "#{release_path}" do
        fetch(:site_url).each do |site|
          execute :drush, "--yes --uri=#{site}", "updatedb"
        end
      end
    end
  end

  namespace :cache do
    desc "Rebuild a Drupal 8 site cache."
    task :rebuild do |task|
      # Allow this task to be re-invoked (normally, Rake only permits tasks to be run once).
      task.reenable

      on roles(:db) do
        within "#{release_path}" do
          fetch(:site_url).each do |site|
            execute :drush, "--yes --uri=#{site}", "cache:rebuild"
          end
        end
      end
    end
  end

  namespace :config do
    desc "Import config from a config directory."
    task :import do
      on roles(:db) do
        within "#{release_path}" do
          execute :drush, "--yes", "config:import"
        end
      end

      invoke "drush9:cache:rebuild"
    end
  end

  namespace :core do
    desc "Rsync Drupal code or files to/from another server using ssh."
    task :rsync do
      on roles(:app) do
        within "#{release_path}" do
          if ENV["path"]
            path = ENV["path"]
          else
            path = "%files"
          end

          if ENV["source"]
            execute :drush, "--yes", "rsync #{ENV['source']}:#{path} @self:#{path} --mode=rz"
          end
        end
      end
    end
  end

  namespace :site do
    desc "Install Drupal along with modules/themes/configuration/profile."
    task :install do
      on roles(:db) do
        within "#{release_path}" do
          command = ["--yes", "site-install"]

          if ENV['profile']
            command.push(ENV['profile'])
          end

          execute :drush, *command
        end
      end
    end
  end

  namespace :sql do
    desc "Exports the Drupal DB as SQL using mysqldump or equivalent."
    task :dump do
      on roles(:db) do
        unless test "[ -f #{release_path}/db.sql.gz ]"
          within "#{release_path}" do
            # Capture the output from drush status
            status = JSON.parse(capture(:drush, "--yes --format=json", "core:status"))

            # Ensure that we are connected to the database and were able to bootstrap Drupal
            if (status["db-status"] == "Connected" && status["bootstrap"] == "Successful")
              execute :drush, "--yes", "sql:dump --gzip --result-file=#{release_path}/db.sql"
            end
          end
        end
      end
    end

    desc "Copy DB data from a source site to a target site. Transfers data via rsync."
    task :sync do
      on roles(:db) do
        if ENV["source"]
          within "#{release_path}" do
            execute :drush, "--yes", "sql:sync #{ENV['source']} @self"
          end
        end
      end

      Rake::Task["drush9:deploy"].execute
    end
  end
end
