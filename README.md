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

## Prerequisites

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

You can load this repository as a submodule and symlink your desired yml configuration files from your root folder.

Configuration
-------------

In order to use the ScrutinizerCloverListener you have to add a listener to your phpunit.xml file. 

``` xml
    <listeners>
        <listener class="\Uniplaces\Phpunit\ScrutinizerCloverListener" file="ci-configs/src/Uniplaces/Phpunit/ScrutinizerCloverListener.php"/>
    </listeners>
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
