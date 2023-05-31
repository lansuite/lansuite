---
id: documentation
title: Documentation
sidebar_position: 4
---

The documentation is built with [Docusaurus](https://docusaurus.io/).

This page assumes you have [`npm`](https://docs.npmjs.com/about-npm) installed.

## Writing a new documentation article

1. Add your documentation to the `website/docs[/<category>]` folder as a Markdown (`.md`) file:

    ```yaml
    ---
    id: cache
    title: Using the LANSuite cache
    ---

    My documentation content here ...
    ```

2. Place assets, such as images, in the `website/static/` folder.
3. Run the site locally to see the results of your changes:

  ```bash
  cd website/
  make init
  make run
  # Navigate to http://localhost:3000/lansuite/
  ```

There are more options to adjust.
We suggest reading [Create a doc @ Docusaurus documentation](https://docusaurus.io/docs/create-doc) to get an overview.

## Writing a new blog post

1. Add your blog post to the `website/blog` folder as a Markdown (`.md`) file with the filename pattern of `YYYY-MM-DD-My-Blog-Post-Title.md`:

    ```yaml
    ---
    slug: documentation-launch
    title: Launch of the documentation
    authors: andygrunwald
    tags: [documentation, website]
    ---

    Lorem Ipsum ...
    ```

2. Place assets, such as images, in the `website/static/` folder.
3. Run the site locally to see the results of your changes:

  ```bash
  cd website/
  make init
  make run
  # Navigate to http://localhost:3000/lansuite/
  ```

There are more options to adjust.
We suggest reading [Blog @ Docusaurus documentation](https://docusaurus.io/docs/blog) to get an overview.

## Running the documentation site locally

The website is located in the `website/` folder.
Switch into the `website/` folder, install the dependencies, and start the local development server:

```bash
cd website/
make init
make run
```

It should open the address [http://localhost:3000/lansuite/](http://localhost:3000/lansuite/) in a local web browser and show you the website.

## Publishing the documentation site to production

While publishing the documentation, it generates static HTML and pushes it to the [`gh-pages` branch](https://github.com/lansuite/lansuite/tree/gh-pages) of the LANSuite repository.
Via GitHub pages, this branch is served at [https://lansuite.github.io/lansuite/](https://lansuite.github.io/lansuite/).

Now let's publish:

```bash
cd website/
make init
make deploy
```

If you encounter problems during the process, please check if all required environment variables are set: [Environment settings @ Docusaurus documentation](https://docusaurus.io/docs/deployment#environment-settings).
