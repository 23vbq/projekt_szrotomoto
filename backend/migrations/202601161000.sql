-- Attachments table
CREATE TABLE attachments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    filename VARCHAR(255) NOT NULL UNIQUE,
    mime_type VARCHAR(50) NOT NULL
);
ALTER TABLE offers ADD COLUMN attachments JSON;
