---
id: documentation
title: Documentation
---

The documentation is built with [Docusaurus](https://docusaurus.io/).
Please check out their [*Getting Started* guide for installation](https://docusaurus.io/docs/en/installation).

This page assumes that you have [yarn](https://github.com/yarnpkg/yarn) installed.
Docusaurus is also able to operate with [npm](https://docs.npmjs.com/getting-started/what-is-npm).
For the related npm commands, please check out the documentation of Docusaurus.

## Writing a new documentation article

1. Add your documentation to the `/docs` folder as `.md` files, ensuring you have the proper [header](https://docusaurus.io/docs/en/doc-markdown#documents) in each file.
The most straightforward header would be the following, where `id` is the link name (e.g., `docs/intro.html`) and the `title`, is, of course, the title of the browser page.

    ```yaml
    ---
    id: intro
    title: Getting Started
    ---

    My new content here..
    ```

2. If your new documentation article should appear in the sidebar add it to the `website/sidebars.json` file so that your documentation is rendered in a sidebar.

  > If you do not add your documentation to the `sidebars.json` file, the docs will be rendered, but they can only be linked to from other documentation and visited with the known URL.

3. Place assets, such as images, in the `website/static/` folder.
4. Run the site to see the results of your changes.

  ```bash
  cd website/
  yarn install
  yarn start
  # Navigate to http://localhost:3000
  ```

## Writing a new blog post

To publish a new post on the blog, create a file within the blog folder with a formatted name of `YYYY-MM-DD-My-Blog-Post-Title.md`.
The post date is extracted from the file name.

For example, at `website/blog/2017-08-18-Introducing-Docusaurus.md`:

```yml
---
author: Frank Li
authorURL: https://twitter.com/foobarbaz
title: Introducing Docusaurus
---

Lorem Ipsum...
```

## Building the documentation site

The website is located in the `website/` folder and depends on various dependencies.
For this we go into the `website/` folder, install the dependencies and start the local web server:

```bash
cd website/
yarn install
yarn start
```

After the `start` command, your browser should open the address [http://localhost:3000/](http://localhost:3000/) and show you the website.

## Publishing the documentation site

If you are ready, we should publish your new work.
While publishing we generate static HTML and push it to the [LANSuite repositories `gh-pages` branch](https://github.com/lansuite/lansuite/tree/gh-pages).
This branch is responsible for the content behind [https://lansuite.github.io/lansuite/](https://lansuite.github.io/lansuite/).
Now let's publish:

```bash
cd website/
GIT_USER=<GIT_USER> \
  CURRENT_BRANCH=master \
  USE_SSH=true \
  yarn run publish-gh-pages
```

> The specified `GIT_USER` must have push access to the repository of LANSuite.
