[![HitCount](http://hits.dwyl.com/RonaldoMoraes/promobit.svg)](http://hits.dwyl.com/RonaldoMoraes/promobit)

### Observações:
php bin/console messenger:setup-transports

Deixar um **serviço** (supervisor / pm2) monitorando o comando:
php bin/console messenger:consume amqp -vv

**Admin** do RabbitMQ na porta 15672

**Recuperar Senha** não manda segundo email SE token do banco ainda estiver válido