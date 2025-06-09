DROP DATABASE IF EXISTS universe_db;

CREATE DATABASE IF NOT EXISTS universe_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE universe_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_created_at ON users(created_at);

CREATE TABLE IF NOT EXISTS pizzas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE INDEX idx_pizzas_category ON pizzas(category);
CREATE INDEX idx_pizzas_name ON pizzas(name);
CREATE INDEX idx_pizzas_price ON pizzas(price);

CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    cpf VARCHAR(11) NOT NULL UNIQUE,
    phone VARCHAR(11) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    zipcode VARCHAR(10) NOT NULL,
    neighborhood VARCHAR(100) NOT NULL,
    street VARCHAR(200) NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE INDEX idx_customers_cpf ON customers(cpf);
CREATE INDEX idx_customers_status ON customers(status);
CREATE INDEX idx_customers_city ON customers(city);
CREATE INDEX idx_customers_state ON customers(state);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    order_number VARCHAR(20) NOT NULL UNIQUE,
    status ENUM('pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled') DEFAULT 'pending',
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    delivery_address TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

CREATE INDEX idx_orders_customer_id ON orders(customer_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_order_number ON orders(order_number);
CREATE INDEX idx_orders_created_at ON orders(created_at);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    pizza_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (pizza_id) REFERENCES pizzas(id) ON DELETE CASCADE
);

CREATE INDEX idx_order_items_order_id ON order_items(order_id);
CREATE INDEX idx_order_items_pizza_id ON order_items(pizza_id);

INSERT INTO users (username, email, password) VALUES 
('admin', 'admin@admin.com', MD5('admin123')),
('teste', 'teste@teste.com', MD5('teste123'));

INSERT INTO pizzas (name, description, price, category) VALUES 
('Calabresa', 'Pizza tradicional com calabresa, cebola e azeitonas', 35.90, 'Tradicionais'),
('Margherita', 'Pizza clássica com molho de tomate, mussarela e manjericão fresco', 32.90, 'Tradicionais'),
('Frango com Catupiry', 'Pizza com frango desfiado e catupiry cremoso', 38.90, 'Tradicionais'),
('Portuguesa', 'Pizza com presunto, ovos, cebola, azeitonas e ervilha', 42.90, 'Tradicionais'),
('Quatro Queijos', 'Deliciosa combinação de mussarela, parmesão, provolone e gorgonzola', 45.90, 'Tradicionais'),
('Nasa', 'Pizza especial da casa com ingredientes secretos do espaço', 48.90, 'Tradicionais'),
('Napolitana', 'Pizza tradicional italiana com molho de tomate, mussarela e manjericão', 36.90, 'Tradicionais'),
('Mussarela', 'Pizza simples e deliciosa apenas com mussarela', 29.90, 'Tradicionais'),
('Peito de Peru', 'Pizza com peito de peru fatiado e queijo', 39.90, 'Tradicionais'),
('Presunto', 'Pizza clássica com presunto e mussarela', 34.90, 'Tradicionais'),
('Bacon da Lua', 'Pizza especial com bacon crocante e temperos lunares', 41.90, 'Tradicionais'),
('Palmito', 'Pizza vegetariana com palmito e queijo', 37.90, 'Tradicionais'),
('Milho com Bacon', 'Pizza doce e salgada com milho e bacon', 38.90, 'Tradicionais'),
('Calabresa com Cebola', 'Pizza de calabresa com cebola caramelizada', 36.90, 'Tradicionais'),
('Frango com Milho', 'Pizza com frango desfiado e milho doce', 37.90, 'Tradicionais'),
('Alho e Óleo', 'Pizza simples com alho, óleo e oregano', 31.90, 'Tradicionais'),
('Vegetariana de Vênus', 'Pizza com vegetais frescos de outros planetas', 43.90, 'Tradicionais'),
('Muçarela com Ovo', 'Pizza tradicional com mussarela e ovo', 33.90, 'Tradicionais'),
('Quatro Estações', 'Pizza dividida em quatro sabores diferentes', 47.90, 'Tradicionais'),
('Catubresa', 'Combinação especial de catupiry e calabresa', 39.90, 'Tradicionais'),

('Lombo com Catupiry', 'Pizza especial com lombo suíno e catupiry', 49.90, 'Especiais'),
('Brócolis com Bacon', 'Pizza gourmet com brócolis frescos e bacon', 46.90, 'Especiais'),
('Escarola com Alho', 'Pizza especial com escarola refogada e alho', 44.90, 'Especiais'),
('Tomate Seco com Rúcula', 'Pizza sofisticada com tomate seco e rúcula', 48.90, 'Especiais'),
('Pepperoni Especial', 'Pizza com pepperoni importado e temperos especiais', 52.90, 'Especiais'),
('Baiana Nebulosa', 'Pizza picante com temperos da nebulosa', 45.90, 'Especiais'),
('Moda da Nave', 'Pizza especial da casa com ingredientes extraterrestres', 55.90, 'Especiais'),
('Baiana de Saturno', 'Pizza picante com anéis de cebola como Saturno', 47.90, 'Especiais'),
('Mexicana', 'Pizza com pimenta, carne moída e temperos mexicanos', 49.90, 'Especiais'),
('Cheddar com Bacon', 'Pizza com queijo cheddar derretido e bacon', 44.90, 'Especiais'),
('Interplanetária', 'Pizza com sabores de diferentes planetas', 58.90, 'Especiais'),
('Marguerita Especial', 'Versão gourmet da margherita com ingredientes premium', 46.90, 'Especiais'),
('Linguiça Toscana', 'Pizza com linguiça toscana artesanal', 48.90, 'Especiais'),
('Picanha Estelar', 'Pizza com picanha grelhada de qualidade estelar', 62.90, 'Especiais'),
('Costela ao Barbecue', 'Pizza com costela desfiada ao molho barbecue', 56.90, 'Especiais'),
('Strogonoff de Carne', 'Pizza com strogonoff de carne cremoso', 52.90, 'Especiais'),
('Strogonoff de Frango', 'Pizza com strogonoff de frango', 49.90, 'Especiais'),
('Camarão com Catupiry', 'Pizza premium com camarões frescos e catupiry', 65.90, 'Especiais'),
('Lombo Canadense', 'Pizza com lombo canadense defumado', 54.90, 'Especiais'),
('Brócolis com Requeijão', 'Pizza vegetariana com brócolis e requeijão cremoso', 42.90, 'Especiais'),

('Romeu e Julieta', 'Pizza doce com queijo e goiabada', 34.90, 'Doces'),
('Banana com Canela', 'Pizza doce com banana e canela', 32.90, 'Doces'),
('Bombom de Morango', 'Pizza doce com cobertura de morango', 36.90, 'Doces'),
('Banana Nevada', 'Pizza doce com banana e leite condensado', 35.90, 'Doces'),
('Prestígio', 'Pizza doce sabor prestígio com coco e chocolate', 38.90, 'Doces'),
('Chocolate Branco', 'Pizza doce com cobertura de chocolate branco', 37.90, 'Doces'),
('Velocidade da Luz', 'Pizza doce especial que derrete na boca', 42.90, 'Doces'),
('Dois Amores', 'Pizza doce com chocolate ao leite e branco', 39.90, 'Doces'),
('Marciana', 'Pizza doce com sabores de Marte', 44.90, 'Doces'),
('Brigadeiro', 'Pizza doce com cobertura de brigadeiro', 36.90, 'Doces');

INSERT INTO customers (name, cpf, phone, status, zipcode, neighborhood, street, city, state) VALUES 
('Maria Silva Santos', '30536516022', '41998765432', 'active', '80020-100', 'Centro', 'Praça Tiradentes', 'Curitiba', 'Paraná'),
('João Carlos Oliveira', '40628454074', '41987654321', 'active', '80020-110', 'Centro', 'Rua do Rosário', 'Curitiba', 'Paraná'),
('Ana Paula Ferreira', '75395304053', '41976543210', 'active', '80020-010', 'Centro', 'Praça General Osório', 'Curitiba', 'Paraná');

-- Inserindo pedidos de exemplo para popular o dashboard
INSERT INTO orders (customer_id, order_number, status, total_amount, delivery_address, notes, created_at) VALUES 
-- Pedidos de hoje
(1, 'PU-2025-0001', 'delivered', 71.80, 'Praça Tiradentes, 123 - Centro, Curitiba/PR', 'Entregar na portaria', NOW()),
(2, 'PU-2025-0002', 'preparing', 84.80, 'Rua do Rosário, 456 - Centro, Curitiba/PR', 'Sem cebola na pizza', NOW()),
(3, 'PU-2025-0003', 'confirmed', 32.90, 'Praça General Osório, 789 - Centro, Curitiba/PR', '', NOW()),

-- Pedidos de ontem
(1, 'PU-2025-0004', 'delivered', 105.70, 'Praça Tiradentes, 123 - Centro, Curitiba/PR', 'Troco para R$ 150', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(2, 'PU-2025-0005', 'delivered', 78.80, 'Rua do Rosário, 456 - Centro, Curitiba/PR', 'Apartamento 402', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(3, 'PU-2025-0006', 'cancelled', 0.00, 'Praça General Osório, 789 - Centro, Curitiba/PR', 'Cliente cancelou por telefone', DATE_SUB(NOW(), INTERVAL 1 DAY)),

-- Pedidos da semana passada
(1, 'PU-2025-0007', 'delivered', 142.60, 'Praça Tiradentes, 123 - Centro, Curitiba/PR', 'Festa de aniversário', DATE_SUB(NOW(), INTERVAL 7 DAY)),
(2, 'PU-2025-0008', 'delivered', 89.80, 'Rua do Rosário, 456 - Centro, Curitiba/PR', 'Massa fininha', DATE_SUB(NOW(), INTERVAL 6 DAY)),
(3, 'PU-2025-0009', 'delivered', 65.80, 'Praça General Osório, 789 - Centro, Curitiba/PR', '', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(1, 'PU-2025-0010', 'delivered', 156.70, 'Praça Tiradentes, 123 - Centro, Curitiba/PR', 'Pedido para o escritório', DATE_SUB(NOW(), INTERVAL 4 DAY)),

-- Pedidos do mês passado
(2, 'PU-2025-0011', 'delivered', 98.80, 'Rua do Rosário, 456 - Centro, Curitiba/PR', '', DATE_SUB(NOW(), INTERVAL 15 DAY)),
(3, 'PU-2025-0012', 'delivered', 73.80, 'Praça General Osório, 789 - Centro, Curitiba/PR', 'Sem azeitonas', DATE_SUB(NOW(), INTERVAL 18 DAY)),
(1, 'PU-2025-0013', 'delivered', 87.80, 'Praça Tiradentes, 123 - Centro, Curitiba/PR', '', DATE_SUB(NOW(), INTERVAL 22 DAY)),
(2, 'PU-2025-0014', 'delivered', 124.70, 'Rua do Rosário, 456 - Centro, Curitiba/PR', 'Reunião de trabalho', DATE_SUB(NOW(), INTERVAL 25 DAY)),

-- Pedidos de meses anteriores
(1, 'PU-2025-0015', 'delivered', 67.80, 'Praça Tiradentes, 123 - Centro, Curitiba/PR', '', DATE_SUB(NOW(), INTERVAL 45 DAY)),
(3, 'PU-2025-0016', 'delivered', 95.80, 'Praça General Osório, 789 - Centro, Curitiba/PR', '', DATE_SUB(NOW(), INTERVAL 48 DAY)),
(2, 'PU-2025-0017', 'delivered', 112.70, 'Rua do Rosário, 456 - Centro, Curitiba/PR', 'Almoço de domingo', DATE_SUB(NOW(), INTERVAL 52 DAY)),
(1, 'PU-2025-0018', 'delivered', 89.80, 'Praça Tiradentes, 123 - Centro, Curitiba/PR', '', DATE_SUB(NOW(), INTERVAL 65 DAY)),
(3, 'PU-2025-0019', 'delivered', 76.80, 'Praça General Osório, 789 - Centro, Curitiba/PR', '', DATE_SUB(NOW(), INTERVAL 75 DAY)),
(2, 'PU-2025-0020', 'delivered', 134.70, 'Rua do Rosário, 456 - Centro, Curitiba/PR', 'Jantar romântico', DATE_SUB(NOW(), INTERVAL 85 DAY));

-- Inserindo itens dos pedidos
INSERT INTO order_items (order_id, pizza_id, quantity, unit_price, subtotal, notes) VALUES 
-- Pedido 1 (PU-2025-0001)
(1, 1, 2, 35.90, 71.80, ''), -- 2x Calabresa

-- Pedido 2 (PU-2025-0002)
(2, 3, 1, 38.90, 38.90, 'Sem cebola'), -- 1x Frango com Catupiry
(2, 5, 1, 45.90, 45.90, ''), -- 1x Quatro Queijos

-- Pedido 3 (PU-2025-0003)
(3, 2, 1, 32.90, 32.90, ''), -- 1x Margherita

-- Pedido 4 (PU-2025-0004)
(4, 4, 1, 42.90, 42.90, ''), -- 1x Portuguesa
(4, 6, 1, 48.90, 48.90, ''), -- 1x Nasa
(4, 29, 1, 13.90, 13.90, ''), -- 1x Bombom de Morango (assumindo que o ID da pizza doce é 29)

-- Pedido 5 (PU-2025-0005)
(5, 7, 1, 36.90, 36.90, ''), -- 1x Napolitana
(5, 11, 1, 41.90, 41.90, ''), -- 1x Bacon da Lua

-- Pedido 7 (PU-2025-0007)
(7, 21, 1, 49.90, 49.90, ''), -- 1x Lombo com Catupiry
(7, 25, 1, 52.90, 52.90, ''), -- 1x Pepperoni Especial
(7, 30, 1, 39.90, 39.90, ''), -- 1x Dois Amores

-- Pedido 8 (PU-2025-0008)
(8, 8, 1, 29.90, 29.90, ''), -- 1x Mussarela
(8, 24, 1, 48.90, 48.90, ''), -- 1x Tomate Seco com Rúcula
(8, 12, 1, 37.90, 37.90, ''), -- 1x Palmito

-- Pedido 9 (PU-2025-0009)
(9, 9, 1, 39.90, 39.90, ''), -- 1x Peito de Peru
(9, 27, 1, 25.90, 25.90, ''), -- 1x Romeu e Julieta

-- Pedido 10 (PU-2025-0010)
(10, 32, 1, 62.90, 62.90, ''), -- 1x Picanha Estelar
(10, 5, 1, 45.90, 45.90, ''), -- 1x Quatro Queijos
(10, 31, 1, 47.90, 47.90, ''), -- 1x Interplanetária

-- Pedido 11 (PU-2025-0011)
(11, 26, 1, 49.90, 49.90, ''), -- 1x Mexicana
(11, 28, 1, 48.90, 48.90, ''), -- 1x Banana Nevada

-- Pedido 12 (PU-2025-0012)
(12, 13, 1, 38.90, 38.90, ''), -- 1x Milho com Bacon
(12, 34, 1, 34.90, 34.90, ''), -- 1x Banana com Canela

-- Pedido 13 (PU-2025-0013)
(13, 22, 1, 46.90, 46.90, ''), -- 1x Brócolis com Bacon
(13, 15, 1, 37.90, 37.90, ''), -- 1x Frango com Milho
(13, 36, 1, 3.00, 3.00, ''), -- 1x Refrigerante (se houver)

-- Pedido 14 (PU-2025-0014)
(14, 33, 1, 56.90, 56.90, ''), -- 1x Costela ao Barbecue
(14, 5, 1, 45.90, 45.90, ''), -- 1x Quatro Queijos
(14, 23, 1, 44.90, 44.90, ''), -- 1x Escarola com Alho

-- Pedido 15 (PU-2025-0015)
(15, 14, 1, 36.90, 36.90, ''), -- 1x Calabresa com Cebola
(15, 35, 1, 30.90, 30.90, ''), -- 1x Prestígio

-- Pedido 16 (PU-2025-0016)
(16, 19, 1, 47.90, 47.90, ''), -- 1x Quatro Estações
(16, 3, 1, 38.90, 38.90, ''), -- 1x Frango com Catupiry
(16, 37, 1, 9.00, 9.00, ''), -- 1x Sobremesa

-- Pedido 17 (PU-2025-0017)
(17, 18, 1, 54.90, 54.90, ''), -- 1x Lombo Canadense
(17, 25, 1, 52.90, 52.90, ''), -- 1x Pepperoni Especial
(17, 36, 1, 4.90, 4.90, ''), -- 1x Bebida

-- Pedido 18 (PU-2025-0018)
(18, 1, 1, 35.90, 35.90, ''), -- 1x Calabresa
(18, 2, 1, 32.90, 32.90, ''), -- 1x Margherita
(18, 30, 1, 21.00, 21.00, ''), -- 1x Sobremesa

-- Pedido 19 (PU-2025-0019)
(19, 16, 1, 31.90, 31.90, ''), -- 1x Alho e Óleo
(19, 20, 1, 39.90, 39.90, ''), -- 1x Catubresa
(19, 38, 1, 5.00, 5.00, ''), -- 1x Bebida

-- Pedido 20 (PU-2025-0020)
(20, 17, 1, 58.90, 58.90, ''), -- 1x Camarão com Catupiry
(20, 5, 1, 45.90, 45.90, ''), -- 1x Quatro Queijos
(20, 31, 1, 29.90, 29.90, ''); -- 1x Sobremesa especial
