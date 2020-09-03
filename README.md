<h1 align="center">
    Promotest
</h1>

<h4 align="center"> 
	Teste 1.0 🗡️ Done!
</h4>
<p align="center">	
	
  <img alt="Repository size" src="https://img.shields.io/github/repo-size/RonaldoMoraes/promobit">
    
  
  <a href="https://github.com/RonaldoMoraes/promobit/commits/master">
    <img alt="GitHub last commit" src="https://img.shields.io/github/last-commit/RonaldoMoraes/promobit">
  </a>

  <a>
    <img alt="Stargazers" src="https://img.shields.io/github/stars/RonaldoMoraes/promobit?style=social">
  </a>
</p>

## 💻 Aplicação

Projeto: https://github.com/RonaldoMoraes/promobit/projects/1

API RESTful com:

- CRUD de usuário
  - Create
  - Read
  - Update
  - Delete
  - List (*brinde)
- Login
- Recuperação de senha. Página: **localhost:8081/reset-password**
 > Para recuperar a senha é necessário configurar o .env com configs do gmail/mandrill/mailgun

Utilizando também de:
- TDD
- Autenticação com JWT (stateless)
- Envio de email (GMail)

## :rocket: Tecnologias

O teste foi desenvolvido com as seguintes ferramentas:

- PHP 7.2.11
- Composer 1.8.4
- Symfony 5.1
- PHPUnit 7.5.20
- MySQL 8 (docker) & Mariadb 10.3.10 (local)
- Docker 19.03 & Docker Compose 1.26.2

## Documentação

[Postman](https://documenter.getpostman.com/view/3747276/TVCgxS4X) - published

Também tem um export da collection do Postman na raiz do projeto chamado [Promobit.postman_collection.json](https://github.com/RonaldoMoraes/promobit/blob/master/Promobit.postman_collection.json)

### Instalar local sem docker 

```bash
# Clone este repositório
$ git clone https://github.com/RonaldoMoraes/promobit

# Entre na pasta source do projeto
$ cd promobit/src

# Instale as dependências
$ composer install

# Configure as variáveis de ambiente
$ cp .env.example .env

# Crie o banco de dados
$ php bin/console doctrine:database:create

# Rode as Migrations
$ php bin/console doctrine:migrations:migrate

# Baixa e instala o Symfony CLI
$ curl -sS https://get.symfony.com/cli/installer | bash
$ mv /root/.symfony/bin/symfony /usr/local/bin/symfony

# Start server com symfony cli
$ symfony server:start

# Rodando na porta 8000
```

### Com docker e docker-compose manualmente

```bash
# Clone este repositório
$ git clone https://github.com/RonaldoMoraes/promobit

# Entre na pasta do projeto
$ cd promobit

# Configure as variáveis de ambiente
$ cp .env.example .env

# Configure as variáveis de ambiente
$ cp src/.env.example src/.env

# Rode o docker-compose
$ docker-compose up -d

# Rodando na porta 8081
```

### Com docker-compose no script de remake

```bash
# Clone este repositório
$ git clone https://github.com/RonaldoMoraes/promobit

# Entre na pasta do projeto
$ cd promobit

# Configure as variáveis de ambiente
$ cp .env.example .env

# Configure as variáveis de ambiente
$ cp src/.env.example src/.env

# Dê permissões de execução ao script
$ chmod +x docker-rmk.sh

# Rode o script com ou sem "-d" para não ver o log dos containers ou ver, respectivamente
# Não vê logs
$ ./docker-rmk.sh -d

# Starta e fica nos logs
$ ./docker-rmk.sh

# Rodando na porta 8081
```

### Para executar os testes (necessário phpunit)

```bash
# Clone este repositório
$ git clone https://github.com/RonaldoMoraes/promobit

# Entre na pasta source do projeto
$ cd promobit/src

# Configure as variáveis de ambiente teste
$ cp .env.example .env.test

# Instala as libs do projeto
$ composer install

# Roda os testes
$ php bin/phpunit
```

### To Do (or should have done)
- Melhorar (bastante) os testes
- Implementar teste da listagem de usuários
- Usar fixtures nos testes
- Padronizar retorno da API com [JSEND](https://github.com/omniti-labs/jsend)
- Melhorar validação dos requests (POSTs e PUT)
- Melhorar variáveis de ambiente dos arquivos docker/compose
