#!/usr/bin/env bash

# Unpack secrets
tar xvf .travis/secrets.tar -C .travis

# Setup SSH agent:
eval "$(ssh-agent -s)" #start the ssh agent
chmod 600 .travis/phar_private.key
ssh-add .travis/phar_private.key

# Setup git defaults:
git config --global user.email "castarco@gmail.com"
git config --global user.name "Andreu Correa Casablanca"

# Add SSH-based remote to GitHub repo:
git remote add deploy git@github.com:Litipk/Jupyter-PHP-Installer.git
git fetch deploy

# Get Box and build
wget https://github.com/box-project/box2/releases/download/2.7.0/box-2.7.0.phar -O ./bin/box.phar
cp .travis/phar_public.key ./public.key
./bin/box.phar build -vv

# To allow checkout gh-pages branch
mv jupyter-php-installer.phar jupyter-php-installer.phar.tmp
mv jupyter-php-installer.phar.pubkey jupyter-php-installer.phar.pubkey.tmp

# Checkout gh-pages and add PHAR file and version:
git checkout -b gh-pages deploy/gh-pages

mv jupyter-php-installer.phar.tmp dist/jupyter-php-installer.phar
mv jupyter-php-installer.phar.pubkey.tmp dist/jupyter-php-installer.phar.pubkey

sha1sum dist/jupyter-php-installer.phar > dist/jupyter-php-installer.phar.version
git add dist/jupyter-php-installer.phar dist/jupyter-php-installer.phar.version dist/jupyter-php-installer.phar.pubkey

# Commit and push:
git commit -m 'Rebuilt phar'
git push deploy gh-pages:gh-pages