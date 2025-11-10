USE gestor_tareas;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
INSERT INTO users (
        email,
        password_hash,
        first_name,
        last_name,
        role
    )
VALUES (
        'demo@demo.com',
        '$2y$10$UiCB/SzpgB6Nn/L4DXJf5eFtDiJz0D7jST0gZF0QC06LTxsYEqTXS',
        'Admin',
        'Demo',
        'admin'
    );
-- Generar un nuevo HASH con password_hash('admin123', PASSWORD_BCRYPT). Valor por defecto "admin123"