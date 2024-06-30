---
id: FileCollection
title: File handling with FileCollection
sidebar_position: 2
---

## Rationale

Many modules allow (or even require) users to upload files to the installation.
This is one of the most dangerous ways how we get user input, thus we need to ensure that we get what we actually want from the user and local operations happen in the correct place.
The FileCollection class was created to provide the tools for that by restricting filesystem operations to defined paths and files

## How to use

By default a new file collection enforces the following rules for any file operation
* path must be below the root path to the lansuite installation (e.g. `/var/www/lansuite`)
* The filename MUST NOT match the regular expressions defined in the class constant `FileCollection::SECURITY_BLACKLIST`

It is highly recommended to apply finer grained control through the following means

### Setting a relative path

Normally a module stores files in a subfolder in `ext_inc`, thus it makes sense to configure the `FileCollection` instance to ensure that all file operations are done in this subpath.
This can be done by setting an additional relative path on top on the root path automatically set
e.g. to ensure that file operations happen under `/var/www/lansuite/ext_inc/user_pics` the relative path `ext_inc/user_pics/` has to be set.
```
$fc = new FileCollection();
$fc->setRelativePath('ext_inc/user_pics/');
```
Now all file paths will be restricted to files in that directory or subdirectories

### Setting a file whitelist

Often there is a certain group of file endings that should be accepted.
This can be enforced by setting a whitelist with one or more entries to validate against.
A file will only be considered if any of the regular expressions validates true
For example, if you want to only allow upload files that have an image extension you can use the class constant `IMAGE_WHITELIST`
```
$fc->new FileCollection();
$fc->setWhitelist(FileCollection::IMAGE_WHITELIST);
```
And now only files with the extensions jpg, jpeg, webp, gif, png, bmp or ico will be accepted

### Setting a file blacklist

Note that setting a new blacklist overwrites the existing (default) list.
You may want to read out the current value (via `getBlacklist()`) and array-merge it with what you want to add.
the class constant `IMAGE_WHITELIST` is used by default, which permits any files named `.htaccess`, `user.ini` or have a php file extension

