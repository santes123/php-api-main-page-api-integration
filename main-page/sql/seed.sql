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