---
id: api-docs
title: Generating API docs
---

Former versions of LANSuite bundled an API documentation in the `docs/` folder.
To generate an API documentation in a HTML-Version you can use [phpDocumentor](https://www.phpdoc.org/):

```
$ composer install
$ bin/phpdoc run --progressbar -t ./docs/
```