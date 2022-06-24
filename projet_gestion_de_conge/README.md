# Symfony Boilerplate
- PHP 8.1
- Symfony 5.4
- MariaDB 10.5.12

Download the latest version:  
https://gitlab.axiocode.net:50463/axiolab/symfony/boilerplate/-/releases

___

[[_TOC_]]

___



## Requirement

#### Linux / MacOS

* Docker

* docker-compose

#### Windows

* WSL 2
* Docker
* docker-compose



## Configuration

### 1. SSL Key

With generalization of use of AxioCode Bundles like Auth-Bundle, API-Bundle, Mailer-Bundle, ... hosted in our private Gitlab instance, we need to provide private ssl key which matches with public key registered on our Gitlab Profile.

⚠ So, first at all, copy only your `private ssl key` in `docker/php-fpm/ssh` directory.
Name this file as `id_rsa`.

```shell
cp ${HOME}/.ssh/id_rsa /path/to/project/docker/php-fpm/ssh/id_rsa

chmod 600 /path/to/project/docker/php-fpm/ssh/id_rsa
```



### 2. Environment file `.env`

A `.env` files is provided and used Docker.

This file MUST stay at same depth that tools and `docker-compose.yaml`.

##### Variables of `.env` file

You can modify the value of the variables as you want in order to match to your project.
Below we give us the aim of each variables :

| Variable                   | Aim                                                          | Default Value                   |
| :------------------------- | :----------------------------------------------------------- | :------------------------------ |
| `PROJECT`                  | Name of project (i.e. MyClientProject, ...)                  | `boilerplate`                   |
| `ENVIRONMENT`              | Current runing environment, it's only an information and is not linked with your symfony running environment | `develop`                       |
| `DOMAIN_NAME`              | Domain name of Nginx instance                                | `boilerplate.local`             |
| `WORKING_DIR`              | Directory where symfony project was stored in containers     | `/var/www/application`          |
| `HOST_NGINX_PORT`          | Used by `docker-compose.yml` and `run-server.sh`, it allows to set listen port of nginx server in docker host side | `8000`                          |
| `NGINX_IMAGE_FROM_IMAGE`   | Base image used to build Nginx custom image                  | `nginx`                         |
| `NGINX_IMAGE_FROM_TAG`     | Base image tag used to build Nginx custom image              | `alpine`                        |
| `MARIADB_IMAGE_FROM_IMAGE` | Base image used to build MariaDB custom image                | `mariadb`                       |
| `MARIADB_IMAGE_FROM_TAG`   | Base image tag used to build MariaDB custom image            | `10.5.12`                       |
| `MARIADB_ROOT_PASSWORD`    | MariaDB root password                                        | `root` : MUST be changed        |
| `MARIADB_DATABASE`         | MariaDB database name                                        | `boilerplate` : MUST be changed |
| `MARIADB_USER`             | MariaDB database owner user name                             | `boilerplate` : MUST be changed |
| `MARIADB_PASSWORD`         | MariaDB database owner user password                         | `boilerplate` : MUST be changed |



### 3. Setup the project

In order to build the containers and install the vendors, execute:

```bash
$ make install
```



### 4. [Optional] Add a host to your hostfile

⚠ Using a host name and Docker's internal network only seem to work on Linux right now (and not on WSL2)

Add `<your_domain>.local` to your `/etc/hosts` file:

```
<ip_domain>.1 <your_domain>.local # For example: 172.42.10.1 managician.api.local
```

You will be able to use http://<your_domain>.local/ instead of http://localhost:8000/ 



## Development

### Building, running and stopping Docker containers

There are 4 Docker containers: nginx, php-fpm, mariadb and maildev (to catch emails).

You probably will need to rebuild the containers from time to time. To do that, execute:

```
$ make build
```

To start the containers, execute:

```
$ make run
```

To stop the containers, execute:

```
$ make stop
```



## MariaDB

### Data persistence

Data of MariaDB container were persist in directory `/project/path/docker/mariadb/data`.

⚠ This directory is not versioned in the Git repository. You can check `.gitignore` for more details about ignored files and directory.



### Migrations

- Create a migration file: `make console make migration`
- Execute migration: `make console doctrine:migrations:migrate`



### DataFixtures

- Loading of fixtures : `make console doctrine:fixtures:load`
- Execute all commands to purge and reset db with fixtures : `make db-reset`



## MailDev

Email web-client access : http://<ip_domain>.4



## Linting

Before committing your to GitLab, you should lint your code so that its formatting matches Symfony standards:

`$ make lint`



## Testing

### Install test database

Execute the following:

- `make db-reset SF_ENV=test`

### Running unit tests

Run `make test` to execute the unit tests (and coverage) with PHPUnit.

Tests are located in `symfony/tests`.



## Makefile

A makefile is available to manage the container and the application more easily. To see all available make commands,
simply execute `make`.



## Publishing a release

### What is it about?
Everytime we want to deploy the application we should make a release for it.

A release is tagged with a version number MAJOR.MINOR.PATCH (ie: 1.4.21). In order to simplify
this tagging and do it automatically, this project uses `standard-version` which is installed on the docker
container. `standard-version` will managed the version number automatically however it requires to keep
clean commit messages.

You can find information about the formatting of git commit messages here:
[https://www.conventionalcommits.org/en/v1.0.0/](https://www.conventionalcommits.org/en/v1.0.0/). In a gist,
the messages must be prefixed with the type of modification it brings. For example, if it's a bufix you will
have something like `fix: Select fields properly display their options`. Theses commit messages will be put
in the CHANGELOG.md file.



### How to make a release?

A make command has been added to make things easier.

For a dry-run of the making of the release, simply run: `make release-dry` . This will do nothing permanent
but will print what it would do for the real run.

Once you've checked the dry-run, you can use `make release-run` . This command will actually fill the
CHANGELOG.md, update versions in json files, commit the release, make a git tag for it and push
everything on GitLab. So you have to be sure!

If you want to control the version number, make a pre-release (alpha, beta) or anything else a bit more
fancy, check the documentation at
[https://github.com/conventional-changelog/standard-version](https://github.com/conventional-changelog/standard-version).



### Commit message prefixes available and when to use them

The prefixes you can use are:

- `feat` When a feature is added. Result in a MINOR change.
- `fix` When a bugfix is made. Result in a PATCH change.
- `update` When a general update is done. Result in a PATCH change.
- `refactor` When a refactoring happened. Will not be displayed in the CHANGELOG. Result in a PATCH change.
- `dev` A purely development related change. Will not be displayed in the CHANGELOG. Result in a PATCH change.
- `docs` When documentation is added. Will not be displayed in the CHANGELOG. Result in a PATCH change.
- `config` When configuration is added or changed. Will not be displayed in the CHANGELOG. Result in a PATCH change.
- `style` When CSS changed. Will not be displayed in the CHANGELOG. Result in a PATCH change.
- `test` When tests are added. Will not be displayed in the CHANGELOG. Result in a PATCH change.
- `chore` **This should not be used** except by the tool itself.



## Quality tools configuration

### EditorConfig

#### VSCode

Install the following plugin
https://marketplace.visualstudio.com/items?itemName=EditorConfig.EditorConfig

#### xStorm

Install the following plugin
https://plugins.jetbrains.com/plugin/7294-editorconfig

### Windows Tips

If you use `pushd` and `popd` rather than `cd`, you bypass this UNC Error.

`pushd <UNC path>` will create a temporary virtual drive and enter it.
`popd` it will delete the temporary drive and bring you back to the path you had when you entered `pushd`.

For example:

```shell
C:\Users\developer\sources> pushd \\wsl$\Debian\home\developer\sources\axiolab\symfony\boilerplate
Z:\home\developer\sources\axiolab\symfony\boilerplate> REM a temporary Z: virtual drive has been created
Z:\home\developer\sources\axiolab\symfony\boilerplate> popd
C:\Users\developer\sources> REM the Z: drive has been deleted
C:\Users\developer\sources>
```



### Git hooks

#### PRE-COMMIT

Create a file `pre-commit` in root project `.git/hooks/` with the following script

```bash
#!/bin/bash
exec ./pre-commit.sh
```

### GitLabCI

`.gitlab-ci.yml` is already configured for tests, you can use only specific branch for test with `only` keyword.
Add deploy stage if you want to create deployment jobs



## How to configure

PHPUnit: `phpunit.xml`
PHPStan: `phpstan.neon`
PHP Code sniffer: `phpcs.xml`
PHP Mess detector: `phpmd.xml`



## Monitoring tools

### Sentry

To enable sentry you must fill SENTRY_DSN in .env
You can get SENTRY_DSN when creating a project in Sentry
You should not enable Sentry on develop

There's docs on google drive about these steps
https://drive.google.com/drive/folders/1ifI81VBkPcie5jRbtHLRe1TDJdqEnXxI

