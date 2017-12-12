#!/usr/bin/env bash

# This script build all assets for production environment

# If the deploy user exists on the machine, re-run script with that user
DEPLOY_USER="dilpsch"
if id "$DEPLOY_USER" >/dev/null 2>&1; then

    if [ ! "$DEPLOY_USER" == "$USER" ]; then
        echo "Restarting script with user '$DEPLOY_USER'"
        sudo -H -u $DEPLOY_USER "${BASH_SOURCE}"
        exit
    fi

    # Declare a fake HOME, so that cache and various config files can be created
    export HOME="/tmp/dilps-home"
fi

# Exit script on any error
set -e

# Disable progress
if [ "$1" = '--no-progress' ]; then
    NO_PROGRESS='--no-progress'
    PROGRESS_GIT='--quiet'
    export PROGRESS_NG='--progress false'
else
    NO_PROGRESS=
    PROGRESS_GIT=
    export PROGRESS_NG=
fi

echo "Installing git hooks..."
ln -fs ../../bin/pre-commit.sh .git/hooks/pre-commit

echo "Updating Node.js packages..."
yarn install $NO_PROGRESS

echo "Updating all PHP dependencies via composer..."
composer install --classmap-authoritative $NO_PROGRESS

echo "Updating database..."
./vendor/bin/doctrine-migrations migrations:migrate --no-interaction
./vendor/bin/doctrine orm:generate-proxies

echo "Clear cache"
rm -rf ./data/tmp/cache/*

echo "Building Angular application..."
yarn run prod
