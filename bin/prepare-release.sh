#!/usr/bin/env bash

#
# Prepares a new public LanSuite release.
# The end result are two files:
#   - The complete package: LanSuite-*.tar.gz
#   - A checksum file: LanSuite-*_checksums.txt
#
# Usage:
#   # Running a snapshot release
#   bin/prepare-release.sh
#
#   # Running a release based on a git tag
#   bin/prepare-release.sh v1.2.3
#

LANSUITE_GIT_URL="https://github.com/lansuite/lansuite.git"
OUTPUT_DIR="/builds/"

# Our own sha256sum function call.
# That takes into account machines that don't have sha256sum but do have sha2 (i.e. macOS)
sha256sum_() {
    hash -r
    if type sha256sum >& /dev/null; then
        echo "$(sha256sum $@)"
    elif type shasum >& /dev/null; then
        echo "$(shasum -a 256 $@)"
    else
        echo "$(sha2 -q -256 $@)"
    fi
}

# log function
#
#   Parameter #1: Logging state. INFO, WARNING or ERROR
#   Parameter #2: Message to log
#
# Usage:
#   log "INFO" "this is the message to log."
log () {
    case "$1" in
        INFO)
            echo "[$1]  [$(date)] $2"
            ;;

        *)
            echo "[$1] [$(date)] $2"
    esac
}

log "INFO" "Prepare release ... Started"

#
# Check dependencies
#
# Check if git is available
if ! type "git" > /dev/null; then
  log "ERROR" "git can't be found."
  log "ERROR" "git is necessary to clone the latest version of the source code."
  log "ERROR" "Please install it."
  exit 1;
fi

# Check if composer is available
if ! type "composer" > /dev/null; then
  log "ERROR" "composer can't be found."
  log "ERROR" "composer is necessary to install external dependencies for LanSuite."
  log "ERROR" "Please install it. More information can be found at https://getcomposer.org/."
  exit 1;
fi

# Check if tar is available
if ! type "tar" > /dev/null; then
  log "ERROR" "tar can't be found."
  log "ERROR" "tar is necessary to compress LanSuite to a single archive."
  log "ERROR" "Please install it."
  exit 1;
fi

log "INFO" "All dependencies fulfilled. We continue."

if [ -z "$LANSUITE_VERSION" ]
then
      log "INFO" "No LanSuite tag given set. We package a release of LanSuite with latest main."
else
      log "INFO" "LanSuite tag set. We package a release of LanSuite $LANSUITE_VERSION"
fi

log "INFO" "Checking out the git repository $LANSUITE_GIT_URL ..."
git clone $LANSUITE_GIT_URL
if [ $? -eq 0 ]; then
    log "INFO" "Checking out the git repository $LANSUITE_GIT_URL ... Done."
else
    log "ERROR" "Checking out the git repository $LANSUITE_GIT_URL ... Failed"
    exit 1;
fi

# Switch to working directory
cd ./lansuite/

log "INFO" "Fetching remote tags from $LANSUITE_GIT_URL ..."
git fetch --all --tags
if [ $? -eq 0 ]; then
    log "INFO" "Fetching remote tags from $LANSUITE_GIT_URL ... Done."
else
    log "ERROR" "Fetching remote tags from $LANSUITE_GIT_URL ... Failed"
    exit 1;
fi

# Determine version to release
if [ -z "$LANSUITE_VERSION" ]; then
    COMMIT_SHA=$(git rev-parse --verify HEAD)
    log "INFO" "No LanSuite version supplied."
    log "INFO" "Running in snapshot mode with commit ${COMMIT_SHA}."
    RELEASE_VERSION="snapshot-$COMMIT_SHA"
else
    log "INFO" "LanSuite version $LANSUITE_VERSION given. Checking if tag exists ..."
    git show-ref --verify refs/tags/$LANSUITE_VERSION
    if [ $? -eq 0 ]; then
        log "INFO" "LanSuite version $LANSUITE_VERSION given. Checking if tag exists ... Done."
        log "INFO" "Running in release mode with tag $LANSUITE_VERSION."

        # Switching to the local tag
        log "INFO" "Switching to $LANSUITE_VERSION ..."
        git checkout tags/$LANSUITE_VERSION
        if [ $? -eq 0 ]; then
            log "INFO" "Switching to $LANSUITE_VERSION ...Done."
        else
            log "ERROR" "Switching to $LANSUITE_VERSION ...Failed"exit 1;
        fi

        RELEASE_VERSION=$LANSUITE_VERSION
    else
        log "ERROR" "LanSuite version $LANSUITE_VERSION given. Checking if tag exists ... Failed"
        log "ERROR" "Git tag $LANSUITE_VERSION doesn\'t exists."
        log "ERROR" "If you want to release a new LanSuite version based on a release, please create a new git tag for it."
        exit 1;
    fi
fi

log "INFO" "Release version $RELEASE_VERSION determined. We continue."

LANSUITE_FILENAME="LanSuite-$RELEASE_VERSION"

# If vendor dir exists, delete it
if [ -d "./vendor/" ]; then
  log "INFO" "Removing vendor dir ..."
  rm -rf ./vendor/
  log "INFO" "Removing vendor dir ... Done."
fi

# Install PHP dependencies
log "INFO" "Installing dependencies via composer ..."
composer install --no-dev --optimize-autoloader
log "INFO" "Installing dependencies via composer ... Done."

# TODO Generating PHP class / function documentation

# Packaging archive
log "INFO" "Packaging release archive ..."
tar --exclude .git \
    --exclude .docker \
    --exclude .github \
    --exclude .phan \
    --exclude builds \
    --exclude docker \
    --exclude website \
    --exclude Dockerfile \
    --exclude Dockerfile-Production-Release \
    --exclude docker-compose.yml \
    --exclude docker-compose.dump.yml \
    -cvzf ${OUTPUT_DIR}$LANSUITE_FILENAME.tar.gz .
log "INFO" "Packaging release archive ... Done."

# Building checksums
log "INFO" "Generating checksums ..."
CHECKSUM_FILENAME="${OUTPUT_DIR}${LANSUITE_FILENAME}_checksums.txt"
if [ -f CHECKSUM_FILENAME ]; then
    log "ERROR" "Checksum file $CHECKSUM_FILENAME already exists."
    exit 1;
fi

echo $(sha256sum_ ${OUTPUT_DIR}$LANSUITE_FILENAME.tar.gz) >> $CHECKSUM_FILENAME
log "INFO" "Generating checksums ... Done."
log "INFO" ""

# We are done here.
# Saying goodbye.
log "INFO" "Prepare release ... Done."
log "INFO" "Your next steps:"
log "INFO" " - Write the release notes"
log "INFO" " - Create a new release on https://github.com/lansuite/lansuite/releases (incl. the archives)"
log "INFO" " - Update the CHANGELOG.md (add the new version and add a new unreleased section)"
log "INFO" " - Communicate it"
log "INFO" " - Celebrate"
log "INFO" "Thank you and have a nice day!"