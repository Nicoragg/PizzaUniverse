# 🍕 PizzaUniverse
Sistema de Gerenciamento para Pizzaria

![Status](https://img.shields.io/badge/Status-Em%20Desenvolvimento-yellow)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue)
![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-orange)
![License](https://img.shields.io/badge/License-MIT-green)

## 📖 Sobre o Projeto

O PizzaUniverse é um sistema completo de gerenciamento para pizzarias, desenvolvido em PHP puro seguindo o padrão MVC (Model-View-Controller). O sistema oferece funcionalidades essenciais para administrar pedidos, clientes, cardápio de pizzas e usuários do sistema.

## ✨ Funcionalidades

- 👥 **Gerenciamento de Usuários**: Cadastro, edição e controle de acesso
- 👤 **Gerenciamento de Clientes**: Cadastro e manutenção de dados dos clientes
- 🍕 **Cardápio de Pizzas**: Criação e edição do menu de pizzas
- 📋 **Controle de Pedidos**: Gestão completa dos pedidos realizados
- 🏠 **Dashboard**: Painel administrativo com visão geral do sistema
- 🔐 **Sistema de Autenticação**: Login seguro com validação de sessão

## 🛠️ Tecnologias Utilizadas

- **Backend**: PHP 7.4+
- **Banco de Dados**: MySQL 8.0+
- **Frontend**: HTML5, CSS3, JavaScript
- **Arquitetura**: MVC (Model-View-Controller)
- **Segurança**: CSRF Token, Validação de dados

## 📁 Estrutura do Projeto

```
PizzaUniverse/
├── assets/                     # Recursos estáticos
│   ├── images/                 # Imagens do sistema
│   ├── js/                     # Scripts JavaScript
│   └── stylesheets/            # Arquivos CSS
├── controllers/                # Controladores MVC
├── dal/                        # Data Access Layer (DAL)
├── helpers/                    # Classes auxiliares
├── models/                     # Modelos de dados
├── views/                      # Views e templates
│   ├── components/             # Componentes reutilizáveis
│   └── pages/                  # Páginas do sistema
├── autoload.php               # Autoloader de classes
├── index.php                  # Ponto de entrada principal
├── php.ini                    # Configurações PHP
├── test_connection.php        # Teste de conexão DB
└── universe_db.sql           # Script do banco de dados
```

## 📋 Pré-requisitos

- **Sistema Operacional**: Linux (Ubuntu/Debian recomendado)
- **PHP**: Versão 7.4 ou superior
- **MySQL**: Versão 8.0 ou superior
- **Privilégios**: Acesso root/sudo
- **Conectividade**: Conexão com internet para downloads

## 🚀 Instalação e Configuração

### 1. Preparação do Sistema

```bash
# Atualizar o sistema
sudo apt update && sudo apt upgrade -y

# Instalar dependências essenciais
sudo apt install curl gpg gcc g++ plocate git-all mysql-server -y
```

### 2. Configuração do MySQL

#### 2.1 Configuração Inicial de Segurança
```bash
sudo mysql_secure_installation
```

#### 2.2 Criação do Usuário de Desenvolvimento
```bash
sudo mysql -u root -p
```

Execute no console MySQL:
```sql
CREATE USER 'dev'@'localhost' IDENTIFIED BY 'admin123';
GRANT ALL PRIVILEGES ON *.* TO 'dev'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
EXIT;
```

#### 2.3 Importar Estrutura do Banco de Dados
```bash
cd /home/dev/PizzaUniverse
mysql -u dev -padmin123 < universe_db.sql
```

### 3. Configuração do PHP

#### 3.1 Instalação do PHP e Extensões
```bash
sudo apt install php php-mysql php-cli php-curl php-json php-cgi php-xml php-mbstring php-zip php-gd -y
```

#### 3.2 Atualização da base de dados (plocate)
```bash
sudo updatedb
```

#### 3.3 Configuração Personalizada
```bash
# Substituir 8.x pela versão instalada do PHP
sudo cp php.ini /etc/php/8.x/cli/php.ini
```

### 4. Configuração do Git (Opcional)
```bash
git config --global user.email "seu-email@exemplo.com"
git config --global user.name "Seu Nome"
```

### 5. Validação da Instalação

#### 5.1 Teste de Conexão com Banco
```bash
php test_connection.php
```

#### 5.2 Iniciar Servidor de Desenvolvimento
```bash
php -S localhost:3000
```

🌐 **Acesse**: http://localhost:3000

## 📝 Convenções de Desenvolvimento

### Nomenclatura de Arquivos

| Contexto | Padrão | Exemplo |
|----------|--------|---------|
| Classes | PascalCase | `UserController.php` |
| Scripts/Funções | snake_case | `test_connection.php` |
| Arquivos CSS/JS | kebab-case | `sweet-alert.css` |

### Padrões de Código

- **PSR-4**: Autoloading de classes
- **MVC**: Separação clara de responsabilidades
- **Validação**: Sanitização de todos os inputs
- **Segurança**: Implementação de CSRF tokens

## 🔧 Uso do Sistema

### Primeira Execução

1. **Verificar Instalação**: Execute `php test_connection.php`
2. **Iniciar Servidor**: Execute `php -S localhost:3000`
3. **Acessar Sistema**: Navegue para http://localhost:3000
4. **Login**: Use as credenciais configuradas na instalação

### Funcionalidades Principais

- **Dashboard**: Visão geral do sistema
- **Usuários**: `/users` - Gerenciar usuários do sistema
- **Clientes**: `/customers` - Cadastro de clientes
- **Pizzas**: `/pizzas` - Gerenciar cardápio
- **Pedidos**: `/orders` - Controle de pedidos

## 🐛 Solução de Problemas

### Problemas com MySQL

**Serviço não está rodando:**
```bash
sudo systemctl status mysql
sudo systemctl restart mysql
```

**Erro de autenticação:**
```bash
# Verificar usuário criado
mysql -u dev -padmin123 -e "SELECT USER();"
```

### Problemas com PHP

**Extensões faltando:**
```bash
# Listar extensões instaladas
php -m

# Instalar extensão específica
sudo apt install php-[nome-extensao]
```

**Problemas de permissão:**
```bash
# Verificar permissões
ls -la *.php

# Ajustar permissões
chmod 644 *.php
chmod 755 controllers/ models/ views/
```

### Problemas com o Servidor

**Porta em uso:**
```bash
# Verificar porta 3000
lsof -i :3000

# Usar porta alternativa
php -S localhost:8080
```

**Erro no arquivo de configuração:**
```bash
# Verificar sintaxe PHP
php -l index.php

# Verificar logs de erro
tail -f /var/log/apache2/error.log
```

## 🔒 Segurança

- **CSRF Protection**: Tokens implementados em formulários
- **SQL Injection**: Uso de prepared statements
- **XSS Protection**: Sanitização de outputs
- **Session Security**: Configurações seguras de sessão

## 📊 Status do Desenvolvimento

- [x] Sistema de autenticação
- [x] CRUD de usuários
- [x] CRUD de clientes
- [x] CRUD de pizzas
- [x] Sistema de pedidos
- [x] Dashboard básico
- [ ] Relatórios avançados
- [ ] API REST
- [ ] Sistema de notificações

## 🤝 Contribuição

1. Faça fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 📞 Suporte

- **Documentação**: Consulte este README
- **Issues**: Abra uma issue no repositório
- **Contato**: Entre em contato com a equipe de desenvolvimento

---

**Desenvolvido com ❤️ pela equipe PizzaUniverse**
