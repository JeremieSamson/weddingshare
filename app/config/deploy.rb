set :application, "SoJu"
set :use_sudo,    false
set :webserver_user, "www-data"
set :use_set_permissions, true
ssh_options[:forward_agent] = true
default_run_options[:pty] = true
ssh_options[:port] = 22

# Multi Stage
set :default_stage, "prod"
set :stage_dir,   "app/config/deploy"
set :stages,        %w(prod)
require 'capistrano/ext/multistage'
set :parameters_file, false

set :app_path,    "app"
set :web_path,    "web"

# Repo
set :repository,  "git@github.com:JeremieSamson/weddingshare.git"
set :scm,         :git

#Symfony
set :assets_symlinks, true
set :use_composer, true
set :interactive_mode, false
set :assets_install,      true
set :dump_assetic_assets, false
set :use_composer, true
set :model_manager, "doctrine"

# Capistrano system
set :deploy_via,  :remote_cache
set :keep_releases, 3
logger.level = Logger::MAX_LEVEL
def self.directory?(path)
    level = logger.level
    logger.level = Logger::IMPORTANT
    results = []
    run "if [ -d '#{path}' ]; then echo -n 'true'; fi" do |channel, stream, out|
        results << (out == 'true')
    end
    logger.level = level
    results.count > 0
end
set :shared_files,      ["app/config/parameters.yml"]
set :shared_children,     ["var/logs", "var/sessions", "var/cache", "vendor"]

set :composer_options, "--verbose --prefer-dist --optimize-autoloader --no-progress"

# Run deployment
after "deploy", "deploy:cleanup"