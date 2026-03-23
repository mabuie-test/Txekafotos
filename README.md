# Txekafotos

Plataforma web MVC em PHP 8+ para pedidos de restauração, edição, montagem e composição de fotos com pagamentos M-Pesa via API Débito, tracking do pedido, revisões, feedbacks, showcases de antes/depois, marketing dinâmico e painel administrativo completo.

## Principais módulos

- Landing page comercial com hero, banners, showcases e feedbacks publicados.
- Criação de pedidos com upload da foto principal, anexos opcionais e descrição detalhada.
- Integração real com M-Pesa via API Débito (`POST /wallets/{wallet_id}/c2b/mpesa` e `GET /transactions/{debito_reference}/status`).
- Página pública de acompanhamento do pedido com aprovação, revisão e feedback.
- Backoffice administrativo com dashboard, pedidos, revisões, financeiro, relatórios, showcases e homepage.
- Logs de auditoria e scripts CLI para automação operacional.

## Stack

- PHP 8.1+
- MySQL 8+
- PDO com prepared statements
- Bootstrap 5
- JavaScript vanilla para previews de upload
- Composer para PSR-4 e suporte opcional a `.env` com `vlucas/phpdotenv`

## Estrutura MVC

```text
app/
  Core/
  Controllers/
  Models/
  Services/
  Middleware/
  Views/
config/
database/
public/
routes/
scripts/
storage/
```

## Instalação

1. Clone o projecto e entre na pasta.
2. Copie o ficheiro de ambiente:

```bash
cp .env.example .env
```

3. Ajuste as credenciais da base de dados e da API Débito no `.env`.
4. Instale dependências opcionais do Composer:

```bash
composer install
```

5. Crie a base de dados e importe o esquema e o seed:

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seed.sql
```

6. Garanta permissões de escrita em `storage/`.
7. Inicie localmente:

```bash
php -S localhost:8000 -t public
```

## Variáveis de ambiente

```env
APP_NAME=Txekafotos
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=txekafotos
DB_USER=root
DB_PASS=

DEBITO_BASE_URL=https://my.debito.co.mz/api/v1
DEBITO_TOKEN=
DEBITO_WALLET_ID=

MAX_UPLOAD_MB=5
MAX_EXTRA_IMAGES=5
MAX_REVISIONS=2
```

## Credenciais seed do admin

- **Email:** `admin@txekafotos.com`
- **Senha:** `Admin@123`

> Altere a senha imediatamente em ambiente real.

## Fluxo operacional

1. Cliente cria pedido com foto principal, anexos e descrição.
2. Sistema grava pedido em `pendente_pagamento`.
3. Cliente inicia pagamento M-Pesa.
4. A API Débito devolve `debito_reference` e a transação fica pendente.
5. O script `scripts/check_payments.php` ou uma ação manual sincroniza o status.
6. Após confirmação, o pedido fica `pago` e entra na fila de edição.
7. Admin muda para `em_edicao`, faz upload da versão final e o pedido passa para `concluido`.
8. Cliente aprova, pede revisão ou deixa feedback quando elegível.

## Cron sugerido

```cron
*/5 * * * * /usr/bin/php /caminho/para/Txekafotos/scripts/check_payments.php >> /caminho/para/Txekafotos/storage/logs/check_payments.log 2>&1
0 0 * * * /usr/bin/php /caminho/para/Txekafotos/scripts/daily_metrics.php >> /caminho/para/Txekafotos/storage/logs/daily_metrics.log 2>&1
0 2 * * * /usr/bin/php /caminho/para/Txekafotos/scripts/cleanup_temp.php >> /caminho/para/Txekafotos/storage/logs/cleanup_temp.log 2>&1
```

## Segurança aplicada

- PDO com prepared statements.
- Hash seguro de senhas com `password_hash`.
- Proteção CSRF em rotas mutáveis.
- Sessões com cookies `httponly` e `SameSite=Lax`.
- Uploads validados por MIME e extensão.
- Pastas sensíveis fora da raiz pública.
- Auditoria administrativa e operacional em `activity_logs`.

## Observações de produção

- Aponte o document root para `public/`.
- Em Apache, use o `.htaccess` incluído; em Nginx, faça rewrite de todas as rotas para `public/index.php`.
- Configure HTTPS antes de operar pagamentos reais.
- Substitua a estratégia simples de carregamento de `.env` pelo fluxo via Composer em produção.
