CREATE DATABASE IF NOT EXISTS complaint_db CHARACTER SET utf8mb4;
USE complaint_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS complaints (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(200) NOT NULL,
  category VARCHAR(50) NOT NULL,
  description TEXT NOT NULL,
  status ENUM('Pending','In Progress','Resolved','Rejected') NOT NULL DEFAULT 'Pending',
  admin_remark TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Default admin: admin@example.com / admin123
INSERT INTO users (name,email,password,role)
VALUES ('Administrator','admin@example.com','$6$tW/2k5leCPgJqBKl$ZKWE.PiDXmqpjR3Zr0X5YQzRjfxBxRWwHg4MWCIGnS.6KN3S6wAfhAzz82kgKGunlyJwxJU6hnyfB.ucJ7V34/','admin');
