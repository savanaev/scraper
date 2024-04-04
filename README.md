# Скрапер


## Установка

1. Склонируйте репозиторий на свой локальный компьютер:

    ```bash
    git clone <URL репозитория>
    ```

2. Перейдите в директорию проекта:

    ```bash
    cd <название директории>
    ```

3. Запустите команду `make init`, чтобы инициализировать проект:

    ```bash
    make init
    ```

## Использование

1. Для запуска контейнеров Docker используйте команду:

    ```bash
    make up
    ```

2. Чтобы остановить контейнеры, выполните:

    ```bash
    make down
    ```

3. Используйте команду `make cli` для запуска PHP-CLI контейнера.

4. Для других доступных команд и дополнительной информации, обратитесь к файлу Makefile или запустите:

    ```bash
    make help
    ```

