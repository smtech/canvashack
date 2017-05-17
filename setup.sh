#!/usr/bin/env bash

printf "\n* * * * * \n\nStarting setup. Your sudo password will be required.\n\n* * * * *\n\n"

# REQUIRED
printf "\nPreparing directory for install...\n"
sudo chmod -R 777 .

# REQUIRED
printf "\nInstalling global Composer dependencies...\n"
composer global require "fxp/composer-asset-plugin:^1.1"
printf "\nInstalling this package's Composer dependencies...\n"
composer install -o --prefer-dist

# REQUIRED
if [ ! -f config.xml ]; then
    printf "\nPreparing configuration file `config.xml`...\n"
    cp config.example.xml config.xml
fi

# OPTIONAL (but a good idea)
APACHE_USER=${1:-"www-data"}
APACHE_GROUP=${2:-$APACHE_USER}
printf "\nTransfering ownership to $APACHE_USER:$APACHE_GROUP as the Apache user:group.\n"
sudo chown -R $APACHE_USER:$APACHE_GROUP .

# OPTIONAL (but a good idea)
printf "\nSetting secure file permissions...\n"
sudo find . -type d -exec chmod 550 {} +
sudo find . -type f -exec chmod 440 {} +
sudo chmod 750 setup
sudo find .git -type d -exec chmod 750 {} +
sudo find .git -type f -exec chmod 640 {} +

# REQUIRED
printf "Setting file permissions to allow Smarty caching...\n"
sudo chmod -R 750 ./vendor/battis/bootstrapsmarty/templates_c
sudo chmod -R 750 ./vendor/battis/bootstrapsmarty/cache
# Honor SELinux, if present
if type sestatus &>/dev/null ; then
    SELINUX_ENABLED=$(sestatus | grep -oP "(?<=^Current mode:).*")
    if [ $SELINUX_ENABLED == "enabled" ]; then
        printf "Updating SELinux context for Smarty cache directories\n"
        sudo chcon -R -t httpd_sys_rw_content_t vendor/battis/bootstrapsmarty/templates_c
        sudo chcon -R -t httpd_sys_rw_content_t vendor/battis/bootstrapsmarty/cache
    fi
fi

printf "\nDirectory configured.\n"
printf "*** You need to load schema.sql into your MySQL database and then point your web browser at this directory to complete installation.\n\n"
