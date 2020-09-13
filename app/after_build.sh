#!/bin/sh

rm -rf ./../web/css
rm -rf ./../web/img
rm -rf ./../web/js

mkdir -p ./../web/css
mkdir -p ./../web/img
mkdir -p ./../web/js

cp -r ./../web/app/css/* ./../web/css || true
cp -r ./../web/app/img/* ./../web/img || true
cp -r ./../web/app/js/* ./../web/js || true

cp ./../web/app/favicon.ico ./../web/favicon.ico || true
cp ./../web/app/manifest.json ./../web/manifest.json || true
cp ./../web/app/robots.txt ./../web/robots.txt || true
cp ./../web/app/service-worker.js ./../web/service-worker.js || true

rm ./../web/precache-manifest.* || true
cp ./../web/app/precache-manifest.* ./../web/ || true
cp ./../web/app/index.html ./../web/app.html || true
rm -rf ./../web/app || true
