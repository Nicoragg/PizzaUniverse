# PizzaUniverse
Site de Pizzaria - Sistema de Gerenciamento

## 📋 Pré-requisitos
- Sistema operacional Linux (Ubuntu/Debian)
- Acesso root/sudo
- Conexão com internet

## 🚀 Configuração do Ambiente

### 1. Atualização do Sistema
```bash
sudo apt update && sudo apt upgrade -y
```

### 2. Instalação de Dependências Base
```bash
sudo apt install curl gpg gcc g++ plocate git-all mysql-server -y
```

### 3. Configuração do MySQL

#### 3.1 Configuração Segura do MySQL
```bash
sudo mysql_secure_installation
```

#### 3.2 Criação do Usuário de Desenvolvimento
```bash
sudo mysql -u root -p
```

Execute os seguintes comandos no MySQL:
```sql
CREATE USER 'dev'@'localhost' IDENTIFIED BY 'admin123';
GRANT ALL PRIVILEGES ON *.* TO 'dev'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
EXIT;
```

### 4. Instalação do PHP e Extensões
```bash
sudo apt install php php-mysql php-cli php-curl php-json php-cgi php-xml php-mbstring php-zip php-gd -y
```

### 5. Configuração do Git
```bash
git config --global user.email "seu-email@exemplo.com"
git config --global user.name "Seu Nome"
```

### 6. Configuração do Banco de Dados

#### 6.1 Importar Estrutura do Banco
Primeiro, acesse o diretório do projeto:
```bash
cd $HOME/PizzaUniverse
```

Depois conecte no MySQL:
```bash
sudo mysql -u dev -p
```

No prompt do MySQL, execute:
```sql
source $HOME/PizzaUniverse/universe_db.sql
EXIT;
```

**Nota:** Se o comando `source` não funcionar com variáveis de ambiente, use o caminho completo:
```sql
source /home/dev/PizzaUniverse/universe_db.sql
```

### 7. Configuração do PHP
Copie o arquivo de configuração do PHP:
```bash
cp php.ini /etc/php/8.x/apache2/php.ini
```
*Substitua 8.x pela versão do PHP instalada*

### 8. Teste da Conexão
Execute o arquivo de teste para verificar se tudo está funcionando:
```bash
php test_connection.php
```

### 9. Iniciar o Servidor de Desenvolvimento
Para iniciar o servidor PHP built-in:
```bash
php -S localhost:3000
```

Após executar o comando, acesse http://localhost:3000 no seu navegador.

## 📝 Convenções de Nomenclatura

### Resumo das Práticas:

| Contexto | Padrão Recomendado | Exemplo |
|----------|-------------------|---------|
| Arquivos com classes | PascalCase | UsuarioModel.php |
| Scripts, funções, configs | snake_case | envio_email.php |
| camelCase | ❌ Evitar | minhaClasse.php |

### Estrutura do Projeto
```
PizzaUniverse/
├── assets/
│   ├── images/
│   ├── js/
│   └── stylesheets/
├── controllers/
├── dal/
├── helpers/
├── models/
├── views/
│   ├── components/
│   └── pages/
├── autoload.php
├── index.php
├── php.ini
├── test_connection.php
└── universe_db.sql
```

## 🔧 Uso do Sistema

1. Após seguir todos os passos de configuração
2. Execute `php test_connection.php` para validar a configuração
3. Inicie o servidor com `php -S localhost:3000`
4. Acesse http://localhost:3000 no navegador
5. Utilize as credenciais configuradas durante a instalação

## 🐛 Solução de Problemas

### Erro de Conexão com MySQL
- Verifique se o serviço MySQL está rodando: `sudo systemctl status mysql`
- Reinicie o serviço se necessário: `sudo systemctl restart mysql`

### Erro de Permissões PHP
- Verifique as permissões dos arquivos: `ls -la`
- Ajuste as permissões se necessário: `chmod 644 *.php`

### Extensões PHP Faltando
- Liste as extensões instaladas: `php -m`
- Instale extensões faltantes: `sudo apt install php-[extensao]`

### Erro no Source do MySQL
Se o comando `source` não funcionar com variáveis de ambiente, use o caminho absoluto:
```sql
source /home/dev/PizzaUniverse/universe_db.sql
```

### Servidor não Inicia
- Verifique se a porta 3000 está disponível: `lsof -i :3000`
- Use uma porta diferente se necessário: `php -S localhost:8080`

## 📞 Suporte
Para dúvidas ou problemas, consulte a documentação ou entre em contato com a equipe de desenvolvimento.
