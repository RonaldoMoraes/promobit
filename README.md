## Requisitos

- Docker
- Docker Compose

## Como subir o projeto

Para subir o projeto, copie o arquivo .env.example e renomeie para .env

Com a linha de comando na raiz do projeto:

```docker-compose up -d```

Abra o browser e acesse http://localhost:8081, se tudo der certo, verá a tela padrão do Symfony

## Endpoints

- /users (GET) - listagem de usuários
- /users (POST) - Criação de um novo usuário
- /users/{id} (GET) - Retorna um usuário
- /users/{id} (PUT) - Altera um usuário
- /users/{id} (DELETE) - Deleta um usuário

Nos endpoints de criação e edição, mande os dados do usuário no body no fomato json, por exemplo:
```
{
    "name": "person",
    "email": "person@example.com",
    "password": "p@ssword"
}
```
