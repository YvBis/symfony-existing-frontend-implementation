![Static check status](https://github.com/YvBis/symfony-existing-frontend-implementation/actions/workflows/static_checkers.yml/badge.svg)
![Tests status](https://github.com/YvBis/symfony-existing-frontend-implementation/actions/workflows/tests.yml/badge.svg)

### Introduction

This is a pet project that i did for trying to swap symfony for other backend in application.
The frontend is taken from [Centrifugo grand chat tutorial](https://github.com/centrifugal/centrifugo).

### Installation

#### Prerequisites
- [Docker](https://www.docker.com/)
- Make utility

#### Usage
- Clone this repository
- Run `make init`
- Run `make php`
- Add users with `bin/console app:make:user`
- Add rooms with `bin/console app:make:room`
- Open [http://localhost](http://localhost)

#### Tests
```shell
  make start
  make php
  bin/phpunit
```

