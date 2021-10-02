#!/usr/bin/env bash

echo "Starting build";

# We only generate packages for tagged versions
git describe --tags --exact-match HEAD || {
    echo "Skipped build";
    exit 0;
}

if ! command -v openssl &> /dev/null
then
    echo "openssl could not be found, please install it"
    exit
fi

openssl genrsa -out .travis/phar_private.key 1024

#check for wget
if ! command -v wget &> /dev/null
then
    echo "wget could not be found, please install it"
    exit
fi

if ! command -v ./bin/box.phar &> /dev/null
then
  # Get Box and build
  echo "getting the box builder"
  wget https://github.com/box-project/box2/releases/download/2.7.0/box-2.7.0.phar -O ./bin/box.phar
  chmod a+x ./bin/box.phar
fi

# Unsigned build
php -d phar.readonly=0 ./bin/box.phar build -vv
mv jupyter-php-installer.phar jupyter-php-installer.phar.tmp

# Signed build
php -d phar.readonly=0 ./bin/box.phar build -c box.signed.json -vv
mv jupyter-php-installer.phar jupyter-php-installer.signed.phar.tmp
mv jupyter-php-installer.phar.pubkey jupyter-php-installer.signed.phar.pubkey.tmp

mkdir -p dist

if ! command -v md5sum &> /dev/null
then
    echo "md5sum could not be found, please install it (md5sha1sum)"
    exit
fi

if ! command -v md5sum &> /dev/null
then
    echo "sha1sum could not be found, please install it (sha2)"
    exit
fi

# Moving unsigned build
mv jupyter-php-installer.phar.tmp dist/jupyter-php-installer.phar
cd dist && md5sum jupyter-php-installer.phar > jupyter-php-installer.phar.md5 && cd ..
cd dist && sha1sum jupyter-php-installer.phar > jupyter-php-installer.phar.sha1 && cd ..

# Moving signed build
mv jupyter-php-installer.signed.phar.tmp dist/jupyter-php-installer.signed.phar
mv jupyter-php-installer.signed.phar.pubkey.tmp dist/jupyter-php-installer.signed.phar.pubkey
cd dist && md5sum jupyter-php-installer.signed.phar > jupyter-php-installer.signed.phar.md5 && cd ..
cd dist && sha1sum jupyter-php-installer.signed.phar > jupyter-php-installer.signed.phar.sha1 && cd ..
cd dist && md5sum jupyter-php-installer.signed.phar.pubkey > jupyter-php-installer.signed.phar.pubkey.md5 && cd ..
cd dist && sha1sum jupyter-php-installer.signed.phar.pubkey > jupyter-php-installer.signed.phar.pubkey.sha1 && cd ..