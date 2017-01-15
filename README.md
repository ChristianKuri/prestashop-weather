# Prestashop Weather (Prestashop 1.6 module)

[![Software License][ico-license]](LICENSE.md)

**Allows your Prestashop store to show the weather at your store or office.**


## Index

- [Installation](#installation)
- [Configuration](#configuration)
	- [Api key](#api-key)
	- [Zip code](#zip-code)
	- [Time](#time)
- [Contributions](#contributions)
	- [Pull Requests](#pull-requests)

- [Security](#security)
- [Credits](#credits)
- [License](#license)


## Installation

1) Download this repository

2) Extract the weather folder into the `prestashop\modules` folder

3) Access your prestashop admin panel and go to `Modules and Services`

4) Search "Weather" and you will find a module by Christian Kuri, press `install`.

5) A confirmation window will popup, press `proceed with the installation`.

6) You will be sent to a configure page, if you have any problem check the [Configuration](#configuration) seccion.

7) go to `positions` which is inside `Modules and Services` menu and move the Weather hook to the desired location in the footer.


## Configuration

The configuration panel have 3 inputs:

### Api key

Here is where you have to introduce your api key, to get it you have to visit [Openweathermap][link-openweathermap] sign up and in the Api keys seccion you will find it.

### Zip code

Here is where you have to introduce your zip code, if you dont know yours, you can visit [Zip codes][link-unitedstateszipcodes] and find your zip code.

### Time

Here is where you have to introduce the time you would like the application to hold the weather data without making another api call to the server, the time is in minuts, if you want to make an api request each time the website is visited it might be `0`, but this is not recommended. The recommended is `30`, which means 30 minuts.


## Contributions

Contributions are **welcome** and will be fully **credited**.
We accept contributions via Pull Requests on [Github](https://github.com/ChristianKuri/laravel-favorite).

### Pull Requests

- **[PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)** - Check the code style with ``$ composer check-style`` and fix it with ``$ composer fix-style``.

- **Add tests!** - Your patch won't be accepted if it doesn't have tests.

- **Document any change in behaviour** - Make sure the `README.md` and any other relevant documentation are kept up-to-date.

- **Consider our release cycle** - We try to follow [SemVer v2.0.0](http://semver.org/). Randomly breaking public APIs is not an option.

- **Create feature branches** - Don't ask us to pull from your master branch.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please [squash them](http://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.


## Security

Please report any issue you find in the issues page.  
Pull requests are welcome.


## Credits

- [Christian Kuri][link-author]
- [All Contributors][link-contributors]


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square

[link-author]: https://github.com/ChristianKuri
[link-contributors]: ../../contributors

[link-openweathermap]: https://openweathermap.org 
[link-unitedstateszipcodes]: http://www.unitedstateszipcodes.org