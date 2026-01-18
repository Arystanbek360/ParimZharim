# Структура модулей

- `/modules` - модули, которые разработаны компанией. На первом уровне - папки клиентов + папка `Shared`, общая для всех клиентов
- `/modules/ClientName/*` - модули, которые разработаны для конкретного клиента
- `/modules/Shared/*` - общие модули, которые используются у разных клиентов
- `/modules/Shared/Core` - ядро архитектуры, общее для всех модулей

# Composer

`composer outdated --direct` - показывает устаревшие пакеты

# Как установить и запустить платформу DevCraft
#### Инструкция по установке платформы и всего сопутствующего ПО для Ubuntu 22.04.4 LTS (Jammy Jellyfish)
>Предполагается, что используется только установленная, свежая система. 

## Что требуется для работы
* Клонированный репозиторий DevCraft
* Git [[Установка Git](docs/how-to/start_working/clone_repository.md)]
* PHP 8.3 [[Установка PHP 8.3](docs/how-to/start_working/install_php_8_3.md)]
* Docker [[Установка Docker](docs/how-to/start_working/install_docker.md)]
* Composer
* DBeaver [[Установка DBeaver](docs/how-to/start_working/install_dbeaver.md)]
* IDE (в инструкции использовалась PhpStorm. Инструкция по получению ключа https://306.antroot.ru/jetbrains-activation)

# Установка платформы

Предварительно надо установить composer командой
```
sudo apt install composer
```

Так же требуется установить проложение `make`:
```
sudo apt-get update
sudo apt-get install build-essential
```

Установить PHP 8.3, docker и composer

В корневом каталоге `/web` открыть файл `Makefile`
В файле `Makefile` запустить блок `composer-install`

Если возникает ошибка с установкой расширений, то их надо установить следующими командами:

```
sudo apt-get update
sudo apt-get install php8.3-zip php8.3-gd
```

Так же, для корректной работы требуется раскоментить эти расширения в файле `/etc/php/8.3/cli/php.ini`
```
extension=curl
extension=gd
extension=zip
```

Далее система запросит ввести данные для аутентификации для Laravel Nova. 

Следующим шагом надо проверить статус Docker:

```
sudo systemctl status docker
```
Docker должен иметь статус "active"

Добавить пользователя в группу Docker

```
sudo usermod -aG docker $USER

```

Выполните команду `newgrp docker` чтобы изменения вступили в силу.

Следующим шагом выполните команды:

```
vendor/bin/sail up
```
>Может занять длительное время, в зависимости от мощности системы

После завершения процесса, надо нажать на ctrl+c и ввести следующие команды:
```
vendor/bin/sail artisan octane:install --server=frankenphp
vendor/bin/sail down
```
В новом окне терминала в IDE ввести следующие команды:
```
vendor/bin/sail up
vendor/bin/sail artisan storage:link
```

Следующим шагом необходимо запустить ранее установленный DBeaver. В окне программы надо создать новую базу данных PostgreSQL с данными из файла `.env` в каталоге `devcraft/platform/web/`

После создания базы надо создать схему, которая указана в файле `.env` в DB_SCHEME

Далее выполнить команды:
```
vendor/bin/sail artisan migrate
vendor/bin/sail artisan db:seed
```

Если миграции при выполнении вызвали ошибку, попробуйте установить расширение и повторить запуск миграций и сидеров 
```
make create-pg-trgm-extension
```

Далее необходимо установить npm:
```
sudo apt install npm
npm i
npm run dev
```

Возможен вариант, что установка/запуск npm не сработают, тогда нужно установить свежую версию Node:
```
npm install -g n
n lts
n latest
n prune
npm i
npm run dev
```

Для тестирования проекта нужно создать тестовую базу данных:
```
make create-test-database
```

Далее можно перейти в браузере на localhost/nova и насладится установленным проектом :)
Пользователь: super@admin.com, пароль: password

# Установка платформы DevCraft на MacOS

1. Установить PHP 8.3, docker и composer
2. Клонировать репозиторий DevCraft
3. копируешь .env.example в .env в корне проекта
4. Makefile - запускаешь composer-install
5. sail up
6. sail artisan octane:install --server=frankenphp
7. sail down
8. sail up
9. sail artisan storage:link
10. Подключиться к БД используя SQL клиент и креды из .env файла и создать схему, которая будет написана в параметре DB_SCHEME в .env файла
11. sail artisan migrate 
12. sail artisan db:seed
13. npm i
14. npm run dev 
15. navigate to localhost/nova и заходи под пользователем super@admin.com и паролем password

# Установка платформы DevCraft на Windows
Выполнить установку php, docker

Предварительно надо установить composer командой
```
sudo apt install composer
```

Так же требуется установить проложение `make`:
```
sudo apt-get update
sudo apt-get install build-essential
sudo apt install make
```
В папке `platform/web` находим файл `.env.example`, копируем его и переименовываем в `.env`
Установить PHP 8.3, docker и composer

В корневом каталоге `/web` открыть файл `Makefile`
В файле `Makefile` запустить блок `load-rpoject` и блок `composer-install`

Если возникает ошибка с установкой расширений, то их надо установить следующими командами:
```
sudo apt-get update
sudo apt-get install php8.3-zip php8.3-gd
```

Так же, для корректной работы требуется раскоментить эти расширения в файле `/etc/php/8.3/cli/php.ini`
```
extension=curl
extension=gd
extension=zip
```

Возможна ошибка, что не получается сохранить изменения в этом файле, надо из под wsl дать права на изменение:
`sudo chmod a+w <путь к файлу>`

Далее система запросит ввести данные для аутентификации для Laravel Nova.

Следующим шагом выполните команды:
```
vendor/bin/sail up
```
>Может занять длительное время, в зависимости от мощности системы

После завершения процесса, надо нажать на ctrl+c и ввести следующие команды:
```
vendor/bin/sail artisan octane:install --server=frankenphp
vendor/bin/sail down
```
В новом окне терминала в IDE ввести следующие команды:
```
vendor/bin/sail up
vendor/bin/sail artisan storage:link
```

При ошибке:`
-bash: vendor/bin/sail: No such file or directory`
Если репозиторий скачан из под windows, и примонтирован к wsl, решение:
```
Указывать абсолютный путь до sail, например -
/mnt/c/Users/user/PhpstormProjects/devcraft/platform/web/vendor/bin/sail up
```

Следующим шагом необходимо запустить ранее установленный DBeaver.
В окне программы надо создать новую базу данных PostgreSQL с данными из файла `.env` в каталоге `devcraft/platform/web/`

После создания базы надо создать схему, которая указана в файле `.env` в DB_SCHEME
Например:
```
psql -h localhost -U sail -d laravel
```
Ввести пароль, далее ввести команду:
```
CREATE SCHEMA `имя схемы`;
```

При ошибке:`Error: You must install at least one postgresql-client-<version> package`
Решение: установить клиент psql командой:`sudo apt-get install postgresql-client`

Далее выполнить команды:
```
vendor/bin/sail artisan migrate
vendor/bin/sail artisan db:seed
```

Если миграции при выполнении вызвали ошибку, попробуйте установить расширение и повторить запуск миграций и сидеров:
```
make create-pg-trgm-extension
```

Далее необходимо установить npm:
```
sudo apt install npm
npm i
npm run dev
```

Возможен вариант, что установка/запуск npm не сработают, тогда нужно установить свежую версию Node:
```
npm install -g n
n lts
n latest
n prune
npm i
npm run dev
```

Для тестирования проекта нужно создать тестовую базу данных:
```
make create-test-database
```

Далее можно перейти в браузере на localhost/nova и насладится установленным проектом :)
Пользователь: super@admin.com, пароль: password
