-- Crear tabla de usuarios de ejemplo
CREATE TABLE IF NOT EXISTS usuarios (
                                        id INT AUTO_INCREMENT PRIMARY KEY,
                                        nombre VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

-- Insertar datos de ejemplo
INSERT INTO usuarios (nombre, email) VALUES
                                         ('Juan Pérez', 'juan@ejemplo.com'),
                                         ('María García', 'maria@ejemplo.com'),
                                         ('Carlos López', 'carlos@ejemplo.com'),
                                         ('Ana Martínez', 'ana@ejemplo.com'),
                                         ('Luis Rodríguez', 'luis@ejemplo.com');