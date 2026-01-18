import os
import yaml
from locust import HttpUser, TaskSet, task, between, events
from seleniumwire import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.chrome.service import Service as ChromeService
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from datetime import datetime
import time
import logging

logging.basicConfig(level=logging.INFO)

class UserBehavior(TaskSet):
    def on_start(self):
        try:
            # Load configuration
            config_file = os.getenv('CONFIG_FILE', 'config.yaml')
            with open(config_file, 'r') as file:
                self.config = yaml.safe_load(file)

            # Configure Selenium Wire to use Chrome
            chrome_options = Options()
            chrome_options.add_argument("--headless")
            chrome_options.add_argument("--no-sandbox")
            chrome_options.add_argument("--disable-dev-shm-usage")

            chrome_service = ChromeService(executable_path='./chromedriver')
            self.driver = webdriver.Chrome(service=chrome_service, options=chrome_options)
            self.login()
        except Exception as e:
            logging.error(f"Error during setup: {e}")
            self.interrupt()

    def login(self):
        try:
            logging.info("Logging in")

            login_url = self.config['login_url']
            logging.info(f"Login URL: {login_url}")

            username = self.config['username']
            logging.info(f"Username: {username}")

            password = self.config['password']
            logging.info(f"Password: {password[0]}{'*' * (len(password) - 1)}")

            logging.info("Making a request to the login page")
            self.driver.get(login_url)

            logging.info("Waiting for the login page to load")
            WebDriverWait(self.driver, 30).until(
                EC.presence_of_element_located((By.NAME, "email"))
            )

            email_elem = self.driver.find_element(By.NAME, "email")
            password_elem = self.driver.find_element(By.NAME, "password")

            email_elem.send_keys(username)
            password_elem.send_keys(password)
            password_elem.send_keys(Keys.RETURN)

            logging.info("Waiting for the login process to complete")
            WebDriverWait(self.driver, 10).until(
                EC.url_changes(login_url)
            )
        except Exception as e:
            logging.error(f"Error during login: {e}")
            self.interrupt()

    @task
    def load_pages(self):
        try:
            logging.info("Loading pages from config")

            urls = self.config['urls']

            for url in urls:
                logging.info(f"Loading URL: {url}")
                start_time = time.time()

                try:
                    self.driver.get(url)
                    WebDriverWait(self.driver, 10).until(
                        EC.presence_of_element_located((By.TAG_NAME, "body"))
                    )
                    self.capture_network_data(url)
                except Exception as e:
                    response_time = (time.time() - start_time) * 1000  # в миллисекундах
                    logging.error(f"Error loading {url}: {e}")
                    events.request.fire(
                        request_type="GET",
                        name=url,
                        response_time=response_time,
                        response_length=0,
                        exception=e,
                        context={}
                    )
        except Exception as e:
            logging.error(f"Error during load_pages: {e}")
            self.interrupt()

    def capture_network_data(self, url):
        logging.info(f"Capturing network data for url {url}")
        for request in self.driver.requests:
            logging.info(f"Request: {request.url}, method: {request.method}")
            response = request.response
            if response:
                status = response.status_code

                # TODO: IT WORKS continiue to
                start_time = request.date
                end_time = response.date

                logging.info(f"Start Time: {start_time}, End Time: {end_time}")

                response_time = 0
                if start_time and end_time:
                    response_time = (end_time - start_time).total_seconds() * 1000  # Convert to milliseconds
                    logging.info(f"Response: {status}, Response Time: {response_time:.2f} ms")

                if status >= 400:
                    logging.error(f"Request to {request.url} failed with status {status}")
                    events.request.fire(
                        request_type=request.method,
                        name=request.path,
                        response_time=response_time,
                        response_length=len(response.body),
                        exception=Exception(f"HTTP {status}"),
                        context={}
                    )
                else:
                    logging.info(f"Request to {request.url} succeeded with status {status}")
                    events.request.fire(
                        request_type=request.method,
                        name=request.path,
                        response_time=response_time,
                        response_length=len(response.body),
                        exception=None,
                        context={}
                    )

    def on_stop(self):
        try:
            self.driver.quit()
        except Exception as e:
            logging.error(f"Error during on_stop: {e}")

class WebsiteUser(HttpUser):
    tasks = [UserBehavior]
    wait_time = between(1, 5)

# Логирование событий Locust
@events.request.add_listener
def log_request(request_type, name, response_time, response_length, exception, **kwargs):
    if exception:
        logging.error(f"Request Failed: {request_type} {name} {response_time}ms {exception}")
    else:
        logging.info(f"Request Success: {request_type} {name} {response_time}ms {response_length} bytes")
