```php
<?php

declare(strict_types=1);

namespace Tayron;

use Tayron\exceptions\Exception;

/**
 * Centraliza o tratamento de erros da aplicação.
 *
 * Converte erros de execução tratáveis em exceções e
 * identifica erros fatais ocorridos durante o shutdown.
 */
final class ErrorHandler
{
    private static ?self $instance = null;

    /**
     * Template padrão da mensagem de erro.
     */
    private const ERROR_TEMPLATE =
        '<b>%s</b><br>' .
        '<b>Arquivo:</b> %s<br>' .
        '<b>Linha:</b> %s<br>' .
        '<b>Tipo de erro:</b> %s<br>' .
        '<b>Versão do PHP:</b> %s';

    /**
     * Descrições dos principais tipos de erro do PHP.
     */
    private const ERROR_TYPES = [
        E_ERROR => 'Erro fatal em tempo de execução.',
        E_WARNING => 'Aviso em tempo de execução.',
        E_PARSE => 'Erro de análise/sintaxe.',
        E_NOTICE => 'Aviso sobre possível problema durante a execução.',
        E_CORE_ERROR => 'Erro fatal durante a inicialização do PHP.',
        E_CORE_WARNING => 'Aviso durante a inicialização do PHP.',
        E_COMPILE_ERROR => 'Erro fatal durante a compilação.',
        E_COMPILE_WARNING => 'Aviso durante a compilação.',
        E_USER_ERROR => 'Erro gerado pela aplicação.',
        E_USER_WARNING => 'Aviso gerado pela aplicação.',
        E_USER_NOTICE => 'Aviso gerado pela aplicação.',
        E_RECOVERABLE_ERROR => 'Erro recuperável durante a execução.',
        E_DEPRECATED => 'Recurso ou comportamento obsoleto.',
        E_USER_DEPRECATED => 'Recurso ou comportamento obsoleto gerado pela aplicação.',
    ];

    /**
     * Impede instanciação externa.
     */
    private function __construct()
    {
    }

    /**
     * Impede clonagem da instância.
     *
     * @throws Exception
     */
    private function __clone(): void
    {
        throw new Exception(
            'A classe ErrorHandler não pode ser clonada.'
        );
    }

    /**
     * Impede desserialização da instância.
     *
     * @throws Exception
     */
    public function __wakeup(): void
    {
        throw new Exception(
            'A classe ErrorHandler não pode ser desserializada.'
        );
    }

    /**
     * Retorna a instância única do ErrorHandler.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
            self::$instance->register();
        }

        return self::$instance;
    }

    /**
     * Registra os mecanismos de tratamento de erros.
     */
    private function register(): void
    {
        set_error_handler(
            [$this, 'handleError'],
            E_ALL
        );

        register_shutdown_function(
            [$this, 'handleShutdown']
        );
    }

    /**
     * Trata erros de execução convertendo-os em exceções.
     *
     * @throws Exception
     */
    public function handleError(
        int $errorLevel,
        string $message,
        string $file,
        int $line
    ): bool {
        if (!$this->shouldHandle($errorLevel)) {
            return false;
        }

        $errorMessage = $this->formatError(
            $message,
            $file,
            $line,
            $errorLevel
        );

        throw new Exception($errorMessage);
    }

    /**
     * Trata erros fatais durante o encerramento do script.
     */
    public function handleShutdown(): void
    {
        $error = error_get_last();

        if ($error === null) {
            return;
        }

        if (!$this->isFatalError($error['type'])) {
            return;
        }

        $message = $this->formatError(
            $error['message'],
            $error['file'],
            $error['line'],
            $error['type']
        );

        /*
         * Nesse ponto não devemos depender de throw,
         * pois o PHP já está encerrando a execução.
         */
        error_log(strip_tags($message));
    }

    /**
     * Verifica se o tipo de erro deve ser tratado.
     */
    private function shouldHandle(int $errorLevel): bool
    {
        return in_array(
            $errorLevel,
            [
                E_WARNING,
                E_NOTICE,
                E_USER_ERROR,
                E_USER_WARNING,
                E_USER_NOTICE,
                E_RECOVERABLE_ERROR,
                E_DEPRECATED,
                E_USER_DEPRECATED,
            ],
            true
        );
    }

    /**
     * Verifica se o erro é fatal.
     */
    private function isFatalError(int $errorLevel): bool
    {
        return in_array(
            $errorLevel,
            [
                E_ERROR,
                E_PARSE,
                E_CORE_ERROR,
                E_COMPILE_ERROR,
            ],
            true
        );
    }

    /**
     * Obtém a descrição do tipo de erro.
     */
    public function getErrorDescription(int $errorLevel): string
    {
        return self::ERROR_TYPES[$errorLevel]
            ?? 'Tipo de erro desconhecido.';
    }

    /**
     * Formata a mensagem de erro.
     */
    private function formatError(
        string $message,
        string $file,
        int $line,
        int $errorLevel
    ): string {
        return sprintf(
            self::ERROR_TEMPLATE,
            $message,
            $file,
            $line,
            $this->getErrorDescription($errorLevel),
            PHP_VERSION
        );
    }
}
```
