---
id: production-release
title: Creating a Release
sidebar_position: 5
---

## Introduction

The production release is not the same as the LanSuite development version (aka the GitHub repository).
The production release only contains the required files to run LanSuite as a website.
It does not contain any functionality to develop the platform.

## Usage

To build a production release, we are using Docker.
This way, we ensure that every contributor can release the same production release with the same version constraints.

### Building the image

First step: Building the docker image to create a release:

```
docker build --file ./Dockerfile-production-release --tag lansuite/lansuite:prod-release .
```

### Building a release from the latest version

If you aim to build a production release from the latest git HEAD:

```
docker run  --rm --volume=./builds:/builds:rw lansuite/lansuite:prod-release
```

### Building a release from a tag

If you want to build a production release from a git tag:

```
docker run  --rm --volume=./builds:/builds:rw -e "LANSUITE_VERSION=v4.2-beta" lansuite/lansuite:prod-release
```

Please replace `v4.2-beta` with your git tag in the command.

### Archives

In your `./builds/` folder, you now have two files:

* 1 x tar.gz, which is the compressed LanSuite production release
* 1 x file with a checksum of the `.tar.gz` file