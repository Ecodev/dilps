#!/usr/bin/env bash

# Make a dump of the database
echo "Dumping database..."
php ./dump-data.php $(date +%Y%m%d%H%M%S).db.backup.sql.gz

# Update project from master branch
echo "Updating project files..."
git pull origin master

# Rebuild project
sh ./build.sh
