yii2-apidoc-dash-docset
=======================

Generate a Dash compatible docset for Yii2

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist jom/yii2-apidoc-dash-docset "*"
```

or add

```json
"jom/yii2-apidoc-dash-docset": "*"
```

to the require section of your composer.json.

Usage
-----

To generate API documentation, run the `apidoc` command.

```
vendor/bin/apidoc --template=docset source/directory ./output
```

See Also
--------

- [Dash Documentation](http://kapeli.com/docsets)
- [yii2-apidoc](https://github.com/yiisoft/yii2-apidoc)
