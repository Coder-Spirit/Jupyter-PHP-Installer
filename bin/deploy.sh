#!/usr/bin/env bash

echo "Starting build";

# We only generate packages for tagged versions
git describe --tags --exact-match HEAD || {
    echo "Skipped build";
    exit 0;
}

openssl aes-256-cbc -K $encrypted_f21abcc37842_key -iv $encrypted_f21abcc37842_iv -in .travis/secrets.tar.enc -out .travis/secrets.tar -d

# Unpack secrets
tar xvf .travis/secrets.tar -C .travis

# Setup SSH agent:
eval "$(ssh-agent -s)" #start the ssh agent
chmod 600 .travis/deploy_private.key
ssh-add .travis/deploy_private.key

# Setup git defaults:
git config --global user.email "castarco@gmail.com"
git config --global user.name "Andreu Correa Casablanca"

# Add SSH-based remote to GitHub repo:
git remote add deploy git@github.com:Litipk/Jupyter-PHP-Installer.git
git fetch deploy

# Get Box and build
wget https://github.com/box-project/box2/releases/download/2.7.0/box-2.7.0.phar -O ./bin/box.phar
chmod a+x ./bin/box.phar

# Unsigned build
./bin/box.phar build -vv
mv jupyter-php-installer.phar jupyter-php-installer.phar.tmp

# Signed build
./bin/box.phar build -c box.signed.json -vv
mv jupyter-php-installer.phar jupyter-php-installer.signed.phar.tmp
mv jupyter-php-installer.phar.pubkey jupyter-php-installer.signed.phar.pubkey.tmp

# Checkout gh-pages and add PHAR file and version:
git checkout -b gh-pages deploy/gh-pages

# Moving unsigned build
mv jupyter-php-installer.phar.tmp dist/jupyter-php-installer.phar
cd dist && md5sum jupyter-php-installer.phar > jupyter-php-installer.phar.md5 && cd ..
cd dist && sha1sum jupyter-php-installer.phar > jupyter-php-installer.phar.sha1 && cd ..
cd dist && sha256sum jupyter-php-installer.phar > jupyter-php-installer.phar.sha256 && cd ..
cd dist && sha512sum jupyter-php-installer.phar > jupyter-php-installer.phar.sha512 && cd ..

# Moving signed build
mv jupyter-php-installer.signed.phar.tmp dist/jupyter-php-installer.signed.phar
mv jupyter-php-installer.signed.phar.pubkey.tmp dist/jupyter-php-installer.signed.phar.pubkey
cd dist && md5sum jupyter-php-installer.signed.phar > jupyter-php-installer.signed.phar.md5 && cd ..
cd dist && sha1sum jupyter-php-installer.signed.phar > jupyter-php-installer.signed.phar.sha1 && cd ..
cd dist && sha256sum jupyter-php-installer.signed.phar > jupyter-php-installer.signed.phar.sha256 && cd ..
cd dist && sha512sum jupyter-php-installer.signed.phar > jupyter-php-installer.signed.phar.sha512 && cd ..
cd dist && md5sum jupyter-php-installer.signed.phar.pubkey > jupyter-php-installer.signed.phar.pubkey.md5 && cd ..
cd dist && sha1sum jupyter-php-installer.signed.phar.pubkey > jupyter-php-installer.signed.phar.pubkey.sha1 && cd ..
cd dist && sha256sum jupyter-php-installer.signed.phar.pubkey > jupyter-php-installer.signed.phar.pubkey.sha256 && cd ..
cd dist && sha512sum jupyter-php-installer.signed.phar.pubkey > jupyter-php-installer.signed.phar.pubkey.sha512 && cd ..

# Adding phar files
git add dist/jupyter-php-installer.phar dist/jupyter-php-installer.signed.phar

# Adding public keys
git add dist/jupyter-php-installer.signed.phar.pubkey

# Adding "unsigned" checksums
git add dist/jupyter-php-installer.phar.md5 dist/jupyter-php-installer.phar.sha1 dist/jupyter-php-installer.phar.sha256 dist/jupyter-php-installer.phar.sha512

# Adding "signed" checksums
git add dist/jupyter-php-installer.signed.phar.md5 dist/jupyter-php-installer.signed.phar.sha1 dist/jupyter-php-installer.signed.phar.sha256 dist/jupyter-php-installer.signed.phar.sha512

# Adding public key checksums
git add dist/jupyter-php-installer.signed.phar.pubkey.md5 dist/jupyter-php-installer.signed.phar.pubkey.sha1 dist/jupyter-php-installer.signed.phar.pubkey.sha256 dist/jupyter-php-installer.signed.phar.pubkey.sha512


# Commit and push:
git commit -m 'Rebuilt phar'
git push deploy gh-pages:gh-pages