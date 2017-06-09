## ErrorHandler

Classe que intercepta todos os erros lançados pelo PHP e os transforma em uma exceção


## Tutorial
Basta apenas executar ErrorHandler::getInstance() no construtor do Front Controller da aplicação, 
para que todos os erros lançados pelo PHP sejam capturados e disparados como Exceção para a classe Exception (https://packagist.org/packages/tayron/exceptions)


A classe exception citado acima, irá gerar um log com erro e irá utilizar a classe Template (https://packagist.org/packages/tayron/template) 
para exibir o erro para o usuário.

Observação: Caso não queira utilizar a classe Excepion, deve-se criar sua própria implementação usa-la em ErrorHandler.


## Utilização via composer

```sh
    "require": {
        ...
        "tayron/error-handler" : "1.0.0"
        ... 
    },    
```
