---
id: api-docs
title: Generating API docs
---

Former versions of LANSuite bundled an API documentation in the `docs/` folder.
These are currently not included in the development versions, but will be provided for each release.
To self-generate the API documentation in the meantime you can use [phpDocumentor](https://www.phpdoc.org/) with the following commands:

```
$ composer install
$ bin/phpdoc run --progressbar -t ./docs/
```

This will generate the documentation in an HTML-format below the `docs`-Folder.
