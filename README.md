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
            "url": "git@github.com:uniplaces/ci-config.git"
        }
    ]
```

You can load this repository as a submodule and symlink your desired yml and configuration files from your root folder.


Configuration
-------------

### Scrutinizer

In order to get use out of scrutinizer, set a symlink from your applications root folder

```
    ln -s ci-configs/src/Uniplaces/Scrutinizer/.scrutinizer.yml
```

Our scrutinizer configuration is set to load and invoke all other components like PHP_codesniffer and Php mess detector

### Phpunit

In order to use the ScrutinizerCloverListener you have to add a listener to your phpunit.xml file. 

``` xml
    <listeners>
        <listener class="\Uniplaces\Phpunit\ScrutinizerCloverListener" file="ci-configs/src/Uniplaces/Phpunit/ScrutinizerCloverListener.php"/>
    </listeners>
```

### PHP_codesniffer

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
    /path/to/your/repository/.git_template/hooks
```

Set a symlink to your hooks directory

```
    cd /path/to/your/repostitory/.git_template/hooks && ln -s /path/to/your/repository/ci-configs/src/Uniplaces/Phpcs/GitHooks/PreCommit/pre-commit
```


Register new template directory with git

```
    git config --global init.templatedir '/path/to/your/repository/.git_template'
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
