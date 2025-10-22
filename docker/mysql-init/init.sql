-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS quizjeeto;

-- Create Laravel user for any host
CREATE USER IF NOT EXISTS 'laraveluser'@'%' IDENTIFIED BY 'secret';
CREATE USER IF NOT EXISTS 'laraveluser'@'localhost' IDENTIFIED BY 'secret';

-- Grant all privileges
GRANT ALL PRIVILEGES ON quizjeeto.* TO 'laraveluser'@'%';
GRANT ALL PRIVILEGES ON quizjeeto.* TO 'laraveluser'@'localhost';

FLUSH PRIVILEGES;
