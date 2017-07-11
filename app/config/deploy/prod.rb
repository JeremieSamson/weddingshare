set :deploy_to, "/var/www/soju.ovh"
set :domain, "ns378858.ip-5-196-69.eu"
set :user, "jerem"
set :symfony_env_prod, "prod"

role :web,        domain
role :app,        domain, :primary => true
role :db,         domain, :primary => true

# conserver le app_dev.php
set :controllers_to_clear, []