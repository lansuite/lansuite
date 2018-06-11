---
id: configuration
title: Configuration
---

## Configuration file

LANSuite's configuration file is written in the `ini` format and should be placed into `inc/base/config.php`.

An example configuration file looks like:

```ini
[lansuite]
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

**Warning**: Setting directories to `0777` is not suggested for production. Only your web server user should be able to write into this directory.

## Configuration settings

Here you will find a detailed description of each configuration setting:

| Section      | Name               | Type      | Description                                                                                                                                                                                                                                                |
| ------------ | ------------------ | --------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `lansuite`   | `default_design`   | string    | Default design for the LANSuite instance. Users might be able to choose different designs. If no design is chosen, the default design is shown. Valid designs are `simple`, `osX` or `Sunset`                                                              |
| `lansuite`   | `chmod_dir`        | integer   | Newly created directories will be changed to this Unix access pattern. E.g. `644` means read- and write access for the owner, read access for everyone else or `600` means read- and write access for the owner, nothing for everyone else.              |
| `lansuite`   | `chmod_file`       | integer   | Newly uploaded files will be changed to this Unix access pattern. E.g. `644` means read- and write access for the owner, read access for everyone else or `600` means read- and write access for the owner, nothing for everyone else.                   |
| `lansuite`   | `debugmode`        | boolean   | If it is enabled (`1`), errors will be shown with a full stack trace. This is useful for development and debugging purpose. In production, this should be disabled with `0`.                                                                                |
| `database`   | `server`           | string    | The hostname / IP address of the database server. For example, `localhost` or `mysql5.myhoster.com` or `192.168.3.65`                                                                                                                                      |
| `database`   | `user`             | string    | The username of the database user. LANSuite should have an own database user for the database connection.                                                                                                                                                  |
| `database`   | `passwd`           | string    | The password for the database user.                                                                                                                                                                                                                        |
| `database`   | `database`         | string    | The Name of the database which will be used by LANSuite. The user needs to have access to this database.                                                                                                                                                   |
| `database`   | `prefix`           | string    | Prefix that will be used for every database table. Every table created by LANSuite will be prefixed with the value entered here. This is used to avoid table name collisions and you can run multiple LANSuite instances and applications in one database. |
| `database`   | `charset`          | string    | MySQL supported character set to be used for the database connection. `utf8` is the default value and should be fine for new installations. Installations of LANSuite <=4.2 may need to use `latin1` to avoid display issues.                              |