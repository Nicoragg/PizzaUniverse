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

INSERT INTO users (username, email, password) VALUES 
('admin', 'admin@admin.com', MD5('admin123'));

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
