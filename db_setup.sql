-- Profile Database Setup Script
-- Run this in your MySQL client or phpMyAdmin

CREATE DATABASE IF NOT EXISTS misc DEFAULT CHARACTER SET utf8;

GRANT ALL ON misc.* TO 'fred'@'localhost' IDENTIFIED BY 'zap';
GRANT ALL ON misc.* TO 'fred'@'127.0.0.1' IDENTIFIED BY 'zap';

USE misc;

-- Users table
CREATE TABLE IF NOT EXISTS users (
   user_id INTEGER NOT NULL AUTO_INCREMENT,
   name VARCHAR(128),
   email VARCHAR(128),
   password VARCHAR(128),
   PRIMARY KEY(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE users ADD INDEX(email);
ALTER TABLE users ADD INDEX(password);

-- Seed users (password is salted MD5 of 'php123' with salt 'XyZzy12*_')
INSERT INTO users (name, email, password)
    VALUES ('Chuck', 'csev@umich.edu', '1a52e17fa899cf40fb04cfc42e6352f1');

INSERT INTO users (name, email, password)
    VALUES ('UMSI', 'umsi@umich.edu', '1a52e17fa899cf40fb04cfc42e6352f1');

-- Profile table with foreign key to users
CREATE TABLE IF NOT EXISTS Profile (
  profile_id INTEGER NOT NULL AUTO_INCREMENT,
  user_id INTEGER NOT NULL,
  first_name TEXT,
  last_name TEXT,
  email TEXT,
  headline TEXT,
  summary TEXT,

  PRIMARY KEY(profile_id),

  CONSTRAINT profile_ibfk_2
        FOREIGN KEY (user_id)
        REFERENCES users (user_id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
