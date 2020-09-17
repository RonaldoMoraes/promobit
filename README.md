<h1 align="center">
    Promotest
</h1>

<h4 align="center"> 
	Teste 2.0 🗡️ Done!
</h4>
<p align="center">	
	
  <img alt="Repository size" src="https://img.shields.io/github/repo-size/RonaldoMoraes/promobit">
    
  
  <a href="https://github.com/RonaldoMoraes/promobit/commits/master">
    <img alt="GitHub last commit" src="https://img.shields.io/github/last-commit/RonaldoMoraes/promobit">
  </a>
[![HitCount](http://hits.dwyl.com/RonaldoMoraes/promobit.svg)](http://hits.dwyl.com/RonaldoMoraes/promobit)
  <a>

</p>

[Projeto](https://github.com/RonaldoMoraes/promobit/projects/1) e [branch](https://github.com/RonaldoMoraes/promobit/tree/rc-1.0) da versão com o teste 1.0

[Projeto](https://github.com/RonaldoMoraes/promobit/projects/2) da versão atual 2.0

## 💻 Aplicação

Projeto: 

API RESTful com:

- CRUD de usuário
  - Create
  - Read
  - Update
  - Delete
  - List
- Login
- [Recuperação de senha](https://promobit.herokuapp.com/reset-password)
 > Para testar local, o env com o DSN do SendGrid está configurado no arquivo .env.example

Utilizando também de:
- TDD
- Autenticação com JWT (stateless)
- Envio de email (SendGrid)
- Armazenamento do token de login num MongoDB, com cache usando Redis
- Cache com Redis das informações do usuário nos métodos do CRUD, visando agilizar o tempo de resposta no Read
- CI/CD com Heroku.

## :rocket: Tecnologias

O teste foi desenvolvido com as seguintes ferramentas:

- PHP 7.2.11
- Composer 1.8.4
- Symfony 5.1
- PHPUnit 7.5.20
- MySQL 5.7
- Supervisor
- Redis
- RabbitMQ
- MongoDB local e [MongoDB Atlas](https://cloud.mongodb.com/v2) em produção
- Docker 19.03 & Docker Compose 1.26.2
- SendGrid
- Heroku

## :factory: Produção
1 Cluster de 3-replica-set de MongoDB no Atlas

1 Servidor na Digital Ocean com os containers
- MySQL
- RabbitMQ / [Manager](http://ronaldomoraes.com.br:15672/)
- Redis

1 App no Heroku com
- [API](https://promobit.herokuapp.com/)
- Supervisor - Como o Heroku free sleepa a aplicação quando fica sem acesso por um tempo, as vezes o supervisor pode parar de rodar, então, pode haver problemas no worker que manda pra fila do RabbitMQ
- CI/CD - build & deploy automatizado ao dar push para branch master

## 🗎 Documentação

[Postman](https://documenter.getpostman.com/view/3747276/TVCgxS4X) - published

Também tem um export da collection do Postman na raiz do projeto chamado [Promobit.postman_collection.json](https://github.com/RonaldoMoraes/promobit/blob/master/Promobit.postman_collection.json)

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
$ docker-compose -f docker-compose.dev.yml up -d

# Rodando na porta 8080
```

### Para executar os testes (phpunit)

```bash
# Clone este repositório
$ git clone https://github.com/RonaldoMoraes/promobit

# Entre na pasta source do projeto
$ cd promobit/src

# Configure as variáveis de ambiente teste
$ cp .env.example .env

# Rode o docker-compose
$ docker-compose -f docker-compose.dev.yml up -d

# Roda os testes
$ php bin/phpunit --coverage-html testscoverage

# Abra a página html gerada para ver o test coverage
# Windows
$ start chrome testscoverage/index.html
# Ubuntu
$ google-chrome testscoverage/index.html

```
