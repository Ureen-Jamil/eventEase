-- =============================================
-- EVENTEASE DATABASE
-- Run this in phpMyAdmin first
-- =============================================

DROP DATABASE IF EXISTS eventease_db;
CREATE DATABASE eventease_db;
USE eventease_db;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    icon VARCHAR(50) DEFAULT 'fa-calendar'
);

-- Events table
CREATE TABLE events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    venue VARCHAR(255) NOT NULL,
    capacity INT NOT NULL,
    category_id INT,
    image VARCHAR(255) DEFAULT 'default.jpg',
    status ENUM('upcoming', 'ongoing', 'completed') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Registrations table
CREATE TABLE registrations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    ticket_type VARCHAR(50) DEFAULT 'Standard',
    qr_code TEXT,
    attendance ENUM('registered', 'checked_in', 'cancelled') DEFAULT 'registered',
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    check_in_time TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    UNIQUE KEY unique_reg (user_id, event_id)
);

-- Insert Categories
INSERT INTO categories (name, icon) VALUES
('Conference', 'fa-users'),
('Workshop', 'fa-laptop'),
('Seminar', 'fa-microphone'),
('Networking', 'fa-handshake'),
('Concert', 'fa-music'),
('Sports', 'fa-futbol'),
('Webinar', 'fa-video'),
('Hackathon', 'fa-code');

-- Insert Admin (password = admin123)
INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@eventease.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert Sample Users (password = admin123 for all)
INSERT INTO users (name, email, password, role) VALUES
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Jane Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Alice Johnson', 'alice@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Bob Williams', 'bob@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Insert Events
INSERT INTO events (title, description, event_date, event_time, venue, capacity, category_id, status) VALUES
('Tech Conference 2024', 'Join industry leaders from Google, Microsoft, and Amazon. Topics include AI, Cloud Computing, Cybersecurity, and Web3. Network with 500+ professionals.', '2024-12-15', '09:00:00', 'Islamabad Convention Center', 500, 1, 'upcoming'),
('Web Development Bootcamp', 'Hands-on 2-day workshop covering React, Node.js, PHP 8, and MySQL. Bring your laptop! Certificate provided.', '2024-11-20', '09:00:00', 'PAF-IAST Campus, Lab 3', 50, 2, 'upcoming'),
('AI Future Seminar', 'Exploring AI impact on society. Guest speakers from leading research labs. Panel discussion and Q&A.', '2024-11-25', '11:00:00', 'Main Auditorium', 200, 3, 'upcoming'),
('Startup Networking Night', 'Connect with entrepreneurs, investors, and innovators. Pitch your ideas and find collaborators.', '2024-11-18', '18:30:00', 'Business Hub, Floor 2', 150, 4, 'upcoming'),
('Annual Music Festival', 'Live performances by top artists. Food stalls, games, and cultural performances.', '2024-12-20', '17:00:00', 'Open Air Amphitheater', 1000, 5, 'upcoming'),
('Cricket Championship', 'Inter-university cricket tournament. Register your team of 11 players. Cash prizes for winners.', '2024-11-30', '08:00:00', 'University Sports Complex', 200, 6, 'upcoming'),
('Data Science Bootcamp', 'Intensive 3-day bootcamp covering Python, Pandas, Machine Learning, and real-world projects.', '2024-12-05', '10:00:00', 'Tech Hub, Room 101', 40, 2, 'upcoming'),
('Women in Tech Symposium', 'Celebrating women leaders in technology. Panel discussions and mentorship sessions.', '2024-11-22', '10:00:00', 'Main Auditorium', 300, 1, 'upcoming'),
('Cybersecurity Workshop', 'Learn ethical hacking, penetration testing, and security best practices. Hands-on exercises.', '2024-12-10', '14:00:00', 'Computer Science Lab', 60, 2, 'upcoming'),
('Digital Marketing Seminar', 'SEO, social media marketing, content strategy, and analytics. Industry experts speaking.', '2024-11-28', '15:00:00', 'Business Hall', 180, 3, 'upcoming');

-- Insert Sample Registrations
INSERT INTO registrations (user_id, event_id, ticket_type, attendance) VALUES
(2, 1, 'VIP', 'registered'),
(2, 2, 'Standard', 'registered'),
(3, 1, 'Standard', 'checked_in'),
(3, 3, 'Standard', 'registered'),
(4, 2, 'Standard', 'registered'),
(5, 1, 'VIP', 'registered');