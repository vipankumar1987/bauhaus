Bauhaus - The missing Laravel 4 Admin Generator
---

[![Latest Stable Version](https://poser.pugx.org/krafthaus/bauhaus/v/stable.png)](https://packagist.org/packages/krafthaus/bauhaus)
[![Latest Unstable Version](https://poser.pugx.org/krafthaus/bauhaus/v/unstable.png)](https://packagist.org/packages/krafthaus/bauhaus)
[![Total Downloads](https://poser.pugx.org/krafthaus/bauhaus/downloads.png)](https://packagist.org/packages/krafthaus/bauhaus)
[![License](https://poser.pugx.org/krafthaus/bauhaus/license.png)](https://packagist.org/packages/krafthaus/bauhaus)

Bauhaus is an admin generator / builder / interface for [Laravel](http://laravel.com).
With Bauhaus you can easily create visual stunning lists, forms and filters for your models.

[Documentation is located here.](https://github.com/krafthaus/bauhaus/wiki)

![Bauhaus List](https://raw.githubusercontent.com/krafthaus/bauhaus/gh-pages/screenshots/list.png)
![Bauhaus Form](https://raw.githubusercontent.com/krafthaus/bauhaus/gh-pages/screenshots/form.png)

Installation
---
Add bauhaus to your composer.json file:
```
"require": {
	"krafthaus/bauhaus": "dev-master"
}
```

Use composer to install this package.
```
$ composer update
```

### Registering the package
```php
'providers' => array(
	// ...
	'KraftHaus\Bauhaus\BauhausServiceProvider',
	'Intervention\Image\ImageServiceProvider',
)
```

Add the `admin` folder to the `app/` directory and put the following line in your composer.json file:
```
"autoload": {
	"classmap": [
		"app/admin"
	]
},
```

Then publish the config file with `php artisan config:publish krafthaus/bauhaus`.
This will add the main bauhaus config file in your application config directory.

And last but not least you need to publish to package's assets with the `php artisan asset:publish krafthaus/bauhaus` command.

Creating your first Bauhaus model
---
To build your first (and most exciting) admin controller you'll have to follow the following easy steps:

Run `$ php artisan bauhaus:scaffold --model=name` where `name` is the name of the model you want to use.

This will create 3 files:
- A new (empty) model in `app/models/YourModelName`.
- A new migration in the `app/database/migrations` directory.
- And ofcourse a Baushaus model file in `app/admin`.

Support
---
Have a bug? Please create an issue here on GitHub that conforms with [necolas's guidelines](https://github.com/necolas/issue-guidelines).

License
---
This package is available under the [MIT license](LICENSE).
