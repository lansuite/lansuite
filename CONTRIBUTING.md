# <a id="contributing"></a> Contributing

LANSuite is an open source project and lives from your ideas and contributions.

There are many ways to contribute, from improving the documentation, submitting
bug reports and features requests or writing code to add enhancements or fix bugs.

#### Table of Contents

1. [Introduction](#contributing-intro)
2. [Fork the Project](#contributing-fork)
3. [Branches](#contributing-branches)
4. [Commits](#contributing-commits)
5. [Pull Requests](#contributing-pull-requests)

## <a id="contributing-intro"></a> Introduction

Please consider our [open issues](https://github.com/lansuite/lansuite/issues) when you start contributing to the project.

Before starting your work on LANSuite, you should [fork the project](https://help.github.com/articles/fork-a-repo/) to your GitHub account. This allows you to freely experiment with your changes.
When your changes are complete, submit a [pull request](https://help.github.com/articles/using-pull-requests/).
All pull requests will be reviewed and merged if they suit some general guidelines:

* Changes are located in a topic branch
* For new functionality, proper tests are written
* Changes should follow the existing coding style and standards

Please continue reading in the following sections for a step by step guide.

## <a id="contributing-fork"></a> Fork the Project

[Fork the project](https://help.github.com/articles/fork-a-repo/) to your GitHub account and clone the repository:

```
git clone https://github.com/andygrunwald/lansuite.git
```

Add a new remote `upstream` with this repository as value.

```
git remote add upstream https://github.com/lansuite/lansuite.git
```

You can pull updates to your fork's master branch:

```
git fetch --all
git pull upstream HEAD
```

Please continue to learn about [branches](CONTRIBUTING.md#contributing-branches).

## <a id="contributing-branches"></a> Branches

Choosing a proper name for a branch helps us identify its purpose and possibly
find an associated bug or feature.
Generally a branch name should include a topic such as `fix` or `feature` followed by a description and an issue number if applicable. Branches should have only changes relevant to a specific issue.

```
git checkout -b fix/service-template-typo-1234
git checkout -b feature/config-handling-1235
```

Continue to apply your changes.

## <a id="contributing-commits"></a> Commits

Once you've finished your work in a branch, please ensure to commit
your changes. A good commit message includes a short topic, additional body
and a reference to the issue you wish to solve (if existing).

Fixes:

```
Fix problem with notifications in Chrome browser

Chrome has implemented a new JavaScript API for push notifications

refs #4567
```

Features:

```
Add a new projector plugin

On LAN-Parties it is useful to show important information on bigger screens.
The projector module was built for this.

refs #1234
```

You can add multiple commits during your journey to finish your patch.

## <a id="contributing-pull-requests"></a> Pull Requests

Once you've commited your changes, please update your local master
branch and rebase your fix/feature branch against it before submitting a PR.

```
git checkout master
git pull upstream HEAD

git checkout fix/notifications
git rebase master
```

Once you've resolved any conflicts, push the branch to your remote repository.
It might be necessary to force push after rebasing - use with care!

New branch:
```
git push --set-upstream origin fix/notifications
```

Existing branch:
```
git push -f origin fix/notifications
```

You can now either use the [hub](https://hub.github.com) CLI tool to create a PR, or nagivate to our [GitHub repository](https://github.com/lansuite/lansuite) and create a PR there.

The pull request should again contain a telling subject and a reference
with `fixes` to an existing issue id if any. That allows developers
to automatically resolve the issues once your PR gets merged.

If you chose [hub](https://hub.github.com) type:

```
hub pull-request

<a telling subject>

fixes #1234
```

Thanks a lot for your contribution!

### <a id="contributing-rebase"></a> Rebase a Branch

If you accidentally sent in a PR which was not rebased against the upstream master, developers might ask you to rebase your PR.

First off, fetch and pull `upstream` master.

```
git checkout master
git fetch --all
git pull upstream HEAD
```

Then change to your working branch and start rebasing it against master:

```
git checkout fix/notifications
git rebase master
```

If you are running into a conflict, rebase will stop and ask you to fix the problems.

```
git status

  both modified: path/to/conflict.cpp
```

Edit the file and search for `>>>`. Fix, build, test and save as needed.

Add the modified file(s) and continue rebasing.

```
git add path/to/conflict.cpp
git rebase --continue
```

Once succeeded ensure to push your changed history remotely.

```
git push -f origin fix/notifications
```

If you fear to break things, do the rebase in a backup branch first and later replace your current branch.

```
git checkout fix/notifications
git checkout -b fix/notifications-rebase

git rebase master

git branch -D fix/notifications
git checkout -b fix/notifications

git push -f origin fix/notifications
```