# ErrorHandler

Classe PHP responsável por interceptar erros do PHP e convertê-los em exceções, permitindo centralizar o tratamento de erros de uma aplicação.

## 📋 Descrição

O `ErrorHandler` permite registrar um manipulador de erros para transformar erros capturados do PHP em exceções.

Ele pode ser integrado à biblioteca [`tayron/exceptions`](https://packagist.org/packages/tayron/exceptions), responsável pelo tratamento e registro das exceções, e à biblioteca [`tayron/template`](https://packagist.org/packages/tayron/template), utilizada para exibir páginas de erro ao usuário.

## 📦 Instalação

Instale o pacote utilizando o Composer:

```bash
composer require tayron/error-handler:1.0.0
```

Ou adicione a dependência ao arquivo `composer.json`:

```json
{
    "require": {
        "tayron/error-handler": "1.0.0"
    }
}
```

Depois, execute:

```bash
composer install
```

## 🚀 Utilização

Inicialize o `ErrorHandler` no construtor do Front Controller da aplicação:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

ErrorHandler::getInstance();
```

A inicialização deve ocorrer no início da aplicação, antes da execução do código que precisa ter seus erros tratados.

## ⚙️ Funcionamento

O fluxo de tratamento funciona da seguinte maneira:

1. O `ErrorHandler` é inicializado.
2. Os erros abrangidos pelo manipulador são interceptados.
3. Os erros configurados são convertidos em exceções.
4. A implementação responsável pelo tratamento pode registrar os erros em logs.
5. A biblioteca de templates pode ser utilizada para apresentar uma página de erro ao usuário.

O comportamento efetivo depende da implementação e das configurações do PHP.

## 🔗 Dependências relacionadas

- **[tayron/exceptions](https://packagist.org/packages/tayron/exceptions):** biblioteca para tratamento de exceções e registro de erros.
- **[tayron/template](https://packagist.org/packages/tayron/template):** biblioteca para renderização de templates e páginas de erro.

## 🔧 Implementação personalizada

Caso não queira utilizar a biblioteca `tayron/exceptions`, crie sua própria implementação de tratamento de exceções e configure-a conforme a API disponibilizada pelo `ErrorHandler`.

## 🔐 Segurança

Em ambientes de produção:

- Não exiba mensagens de erro detalhadas aos usuários.
- Registre informações relevantes para diagnóstico em logs protegidos.
- Não exponha senhas, tokens ou outros dados sensíveis.
- Configure o PHP para não exibir erros diretamente na resposta HTTP.
- Implemente um tratamento adequado para exceções não capturadas e erros fatais.

**Importante:** um manipulador de erros não garante a captura de todos os tipos de falha do PHP.

## 📋 Requisitos

- PHP: versão mínima a confirmar no pacote.
- Composer.

Consulte o `composer.json` do projeto para verificar os requisitos exatos.

## 📄 Licença

Consulte o arquivo `LICENSE` do repositório para obter informações sobre a licença do projeto.

## 🔗 Links

- [Repositório no Packagist](https://packagist.org/packages/tayron/error-handler)
- [tayron/exceptions](https://packagist.org/packages/tayron/exceptions)
- [tayron/template](https://packagist.org/packages/tayron/template)
- [Documentação oficial do PHP](https://www.php.net/manual/pt_BR/)
- [Documentação do Composer](https://getcomposer.org/doc/)
