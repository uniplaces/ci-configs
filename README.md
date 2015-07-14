Ci-config
=========

Provides a bundle of configuration files used for Uniplaces' continuous integration

About Ci-config
---------------

Ci-config is meant to provide a complete set of configuration files needed in a ci scenario. We do currently support:

* [Scrutinizer](https://scrutinizer-ci.com) 

including 

* [PHP_CodeSniffer](http://pear.php.net/package/PHP_CodeSniffer)
* [PHP depend](http://pdepend.org/)
* [PHP mess detector](http://phpmd.org/)
* [PHPUnit](https://phpunit.de)

Installation
------------------

### Prerequisites

This repository is dependent on symfony bundles, it requires you to first run [composer](http://getcomposer.org) by adding the following in the `require` and `repository` section of your `composer.json` file 

``` json
    "require": {
        ...
        "uniplaces/ci-config": "dev-master"
    },
    "repositories": [
        ...
        {
            "type": "vcs",
            "url": "git@github.com:uniplaces/ci-configs.git"
        }
    ]
```

Alternatively you can load this repository as a git submodule and symlink your desired yml and configuration files from your root folder.


Configuration
-------------

### Scrutinizer

In order to get use our scrutinizer config, set a symlink from your applications root folder

```
ln -s ci-configs/src/Uniplaces/Scrutinizer/.scrutinizer.yml
```

Our scrutinizer configuration is set to load and invoke all other components like PHP_codesniffer and Php mess detector

### Phpunit

You can configure your Phpunit to send your coverage reports that need to be in clover format to be sent to scrutinizer


In order to use the ScrutinizerCloverListener you have to add a listener to your phpunit.xml file. 

``` xml
    <listeners>
        <listener class="\Uniplaces\Phpunit\ScrutinizerCloverListener" file="ci-configs/src/Uniplaces/Phpunit/ScrutinizerCloverListener.php"/>
            <arguments>
                <string>../../doc/clover.xml</string>
                <string>my api token</string>
            </arguments>
    </listeners>
```

If you want to enable scrutinizer to use external coverage reports you have to be aligned with this:

https://scrutinizer-ci.com/docs/tools/external-code-coverage/

### PHP_codesniffer

You can use this codesniffer standard independently by following the following configuration commands


Make sure that your codesniffer is installed and available in 

```
/usr/bin/phpcs
```

If this is not the case and you would like to use the pre-commit hook, please edit your PHP_codesniffer config file 

```
ci-configs/src/Uniplaces/Phpcs/GitHooks/PreCommit/Phpcs/config
```

In order to use PHP_codesniffer with a git pre-commit hook you have to register it within your git repository. 
You can change your templatedir configuration to point to another directory than the predefined one. By doing this,
you can easily provide a hook directory within your repository. Create your hook directory and register the templatedir

Create your hooks directory

```
mkdir -p /path/to/your/repository/.git_template/hooks
```

Set a symlink to your hooks directory

```
ln -s /path/to/ci-configs/src/Uniplaces/GitHooks/PreCommit/pre-commit .git_template/hooks/
```


If you like you can register your templates globally or locally

```
git config [--global] init.templatedir '/path/to/your/repository/.git_template'
```

CircleCi
---------

In order to add an additional check for circle ci related aspects you can use our git prepare-commit-msg hook.
We do currently support a check for [ci skip] command only.

Create your hooks directory

```
mkdir -p /path/to/your/repository/.git_template/hooks
```

Set a symlink to your hooks directory

```
ln -s /path/to/ci-configs/src/Uniplaces/GitHooks/PrepareCommitMsg/prepare-commit-msg .git_template/hooks/
```

If you like you can register your templates globally or locally

```
git config [--global] init.templatedir '/path/to/your/repository/.git_template'
```



Troubleshooting
---------------

If something goes wrong, errors & exceptions Talk with us via [HipChat](https://www.hipchat.com/g5fiCwbCI)

Contributing
------------

[Contributing](CONTRIBUTING.md)

MIT License
-----------

License can be found [here](https://github.com/Uniplaces/ci-configs/blob/master/LICENSE).

Authors
-------

ci-configs is created by [Uniplaces Ltd.](https://www.uniplaces.com)
