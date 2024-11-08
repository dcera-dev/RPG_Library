-- Create or Update Databases for Project
-- Database RPG_Library
DROP DATABASE IF EXISTS rpg_library;
CREATE DATABASE rpg_library DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE rpg_library;

-- Table Structure for Users Table
DROP TABLE IF EXISTS users;
CREATE TABLE users (
	UserNo int(11) AUTO_INCREMENT PRIMARY KEY,
	UserId int(255) NOT NULL,
	Username varchar(50) NOT NULL,
	Password varchar(50) NOT NULL,
	EMail varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table Structure for Characters Table
DROP TABLE IF EXISTS characters;
CREATE TABLE characters (
	CharacterNo int(11) AUTO_INCREMENT PRIMARY KEY,
	CharacterId int(24) NOT NULL,
	FirstName varchar(50) NOT NULL,
	LastName varchar(50),
	CharacterObject varchar(255) NOT NULL, 
	ProfileImage varchar(255),
	UserId int(24) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Test Account
INSERT INTO users (UserId, Username, Password, Email)
	VALUES (1, 'admin', 'p@ssword1', 'admin@noemail.com');

-- Adding Test Records to Admin Account
INSERT INTO characters (CharacterId, FirstName, LastName, CharacterObject, ProfileImage, UserId)
	VALUES (1, 'John', 'Smith', '../db_storage/1/1/info.json', '../db_storage/1/1/profile.png', 1),
			(2, 'Jane', 'Doe', '../db_storage/1/2/info.json', '../db_storage/1/2/profile.png', 1),
			(3, 'Samuel', 'Guy', '../db_storage/1/3/info.json', '../db_storage/1/3/profile.png', 1),
			(4, 'Mark', 'Someguy', '../db_storage/1/4/info.json', '../db_storage/1/4/profile.png', 1),
			(5, 'Emily', 'Gerhart', '../db_storage/1/5/info.json', '../db_storage/1/5/profile.png', 1),
			(6, 'A', 'Dog', '../db_storage/1/6/info.json', '../db_storage/1/6/profile.png', 1),
			(7, 'A', 'Cat', '../db_storage/1/7/info.json', '../db_storage/1/7/profile.png', 1),
			(8, 'A', 'Lizard', '../db_storage/1/8/info.json', '../db_storage/1/8/profile.png', 1),
			(9, 'A', 'Bird', '../db_storage/1/9/info.json', '../db_storage/1/9/profile.png', 1),
			(10, 'Telerius', 'Frostwoven', '../db_storage/1/10/info.json', '../db_storage/1/10/profile.png', 1),
			(11, 'Ulgak', 'Grumash', '../db_storage/1/11/info.json', '../db_storage/1/11/profile.png', 1),
			(12, 'Jeff', 'Allbright', '../db_storage/1/12/info.json', '../db_storage/1/12/profile.png', 1);

-- Add Admin Account
-- User: db_admin
-- Password: 6TusdNdKdUoACkHm
DROP USER IF EXISTS db_admin;
CREATE USER db_admin@'%' IDENTIFIED VIA mysql_native_password USING '*2252BB2E0D370FDE7C672BBC576D019B2FDC9DC1';
GRANT ALL PRIVILEGES ON rpg_library.* TO db_admin@'%' REQUIRE NONE WITH GRANT OPTION MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;