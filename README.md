
# NOTES ABOUT DOCS

  

### Creating User
```
php bin/console make:user
php bin/console make:entity
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```
> https://symfony.com/doc/current/security.html#a-create-your-user-class

  

### Encode password
> https://symfony.com/doc/current/security.html#c-encoding-passwords

  

### Authentication & Firewalls
> https://symfony.com/doc/current/security/guard_authentication.html#createtoken
> https://symfony.com/doc/current/security.html#a-authentication-firewalls
> https://smoqadam.me/posts/how-to-authenticate-user-in-symfony-5-by-jwt/#prerequisites
> https://medium.com/@walderlansena/como-configurar-autentica%C3%A7%C3%A3o-jwt-com-symfony-4-4f62a10fe24c

  
### Reset password
> https://symfony.com/doc/current/security/reset_password.html

  

### Testing

```
php bin/console make:unit-test
php bin/console make:functional-test
```
<!-- > https://symfony.com/doc/current/create_framework/unit_testing.html -->
> https://symfony.com/doc/current/testing/database.html
> https://symfony.com/doc/current/testing.html#unit-tests
> https://symfony.com/doc/current/testing.html#functional-tests