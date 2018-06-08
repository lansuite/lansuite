---
id: configuration
title: Configuration
---

## Configuration file

An example configuration file looks like:

```ini
[lansuite]
version=Nightly
default_design=simple
chmod_dir=777
chmod_file=666
debugmode=0

[database]
server=mysql
user=root
passwd=
database=lansuite
prefix=ls_
charset=utf8
```

**Warning**: Setting directories to `0777` is not suggested for production. Only your webserver user should be able to write into this directory.