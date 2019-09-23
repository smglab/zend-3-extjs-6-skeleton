# Требования к окружению

* PHP >= 7.2
* PostgreSQL 9.5
* Java 7
* Sencha Cmd >= 6

# Установка

* Выкачать проект

* Установить Sencha Cmd

```
Ссылка для скачивания: http://cdn.sencha.com/cmd/6.5.2.15/jre/SenchaCmd-6.5.2.15-windows-64bit.zip
```

Прописать sencha в переменную PATH. (C:\Users\username\bin\Sencha\Cmd\6.5.2.15).

* Скачать composer, если его нету (https://getcomposer.org/)
* Выполнить команду composer install (php composer.phar install)

```bash
composer install
```

* Для разработчиков необходимо включить режим разработки 

```bash
composer development-enable
```

* Создать БД, скопировать файл liquibase/liquibase.properties.dist и переименовать его в liquibase.properties. Заполнить в новом файле данные для подключения к БД.

* Накатить миграции БД.

```bash
composer db-migrate
```

* Собрать ExtJS приложение.

```bash
cd public
sencha build development
```  

# Полезные ссылки:

* Документация Liquibase: https://www.liquibase.org/documentation/index.html


* Документация ExtJS 6: https://docs.sencha.com/extjs/6.0.0/classic/Ext.html

* Документация Sencha Cmd: https://docs.sencha.com/cmd/6.5.0/guides/advanced_cmd/cmd_reference.html