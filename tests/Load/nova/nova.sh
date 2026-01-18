#!/bin/bash

# Переходим в директорию скрипта
cd "$(dirname "$0")"

# Проверяем, установлен ли Python 3
if ! command -v python3 &> /dev/null
then
    echo "Python 3 не установлен. Пожалуйста, установите Python 3 и попробуйте снова."
    exit
fi

# Создаем виртуальное окружение
if [ ! -d "venv" ]; then
    echo "Создание виртуального окружения..."
    python3 -m venv venv
else
    echo "Виртуальное окружение уже существует."
fi

# Активируем виртуальное окружение
echo "Активация виртуального окружения..."
source venv/bin/activate

# Устанавливаем зависимости
echo "Установка зависимостей..."
pip install --upgrade pip
pip install locust selenium selenium-wire blinker==1.4 pyyaml

# Экспорт переменных окружения (если необходимо)
export CONFIG_FILE='config.yaml'

# Запуск Locust
echo "Запуск Locust..."
locust -f nova.py
