# MadHorse

A PHP MVC framework for beginner to geek to super rapid application deploy

## Description

MadHorse is a MVC based PHP framework that allow you to create your web site, web application, and web api in the fastest time with bear minimum effort while maintaining the best software engineering practices. This is a lightweight framework and do not ask for any specific type of server or module. Development with this framework is also do not need any advance knowledge of PHP.  If anyone have knowledge about creating class in PHP, should be good enough to start developing website with Madhorse. For experts, Madhorse will provide full support to ensure the OOP based web development with MVC, Layers, Inversion of Control, and Unit and Integraton Testable work flow.

## Getting Started
MadHorse can be install in all environments (Linux/Windos/Mac). The prerequisite is defined in the next section. Currently the installation is supported only from commandline.

### Prerequisites

The prerequisites are given in the below:

```
PHP 7.0 or higher
GIT
```

### Installing

Download the codebase to your webroot folder. In your commandline traverse to your webroot and clone the repo.
```
git clone https://github.com/mmuhasan/madHorse.git 
```

Then install the dependency with composer. The composer.phar is found in the root of cloned repository, however, if you have already installed a composer into your system you can use that one as well. 

```
php composer.phar install 
```

If composer is already install in the system use the following command to install the dependency

```
composer install 
```

## Running the tests

To run the test type the followin command

```
vendor/bin/phpunit
```

To see the test coverage run the following command

```
vendor/bin/phpunit --coverage-html coverage
```
This will create a folder called coverage in your webroot and put the code coverage in it.

## Contributing

We welcome your contribution. Any useful code addition or improvement for the codebase or in the test suite will be welcomed. If you are willing to contribute to MadHorse please make fork and development in that fork. Then submit your changes using pull request. 

If you are not willing to contribite code, but still want to improve MadHorse by giving your thoughts or previous experience with working in PHP, will add the same value like adding code to MadHorse

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/your/project/tags). 

## Authors

* **Md Monjur Ul Hasan** - *Freelancer Webdeveloper* - [mmuhasan](https://github.com/mmuhasan)

See also the list of [contributors](https://github.com/mmuhasan/madHorse/graphs/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
