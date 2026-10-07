```php
<?php

declare(strict_types=1);

final class ErrorHandler
{
    private static ?self $instance = null;

    private function __construct()
    {
        $this->registerHandlers();
    }

    private function __clone()
    {
    }

    /**
     * Retorna a única instância do ErrorHandler.
     */
    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * Registra as configurações globais de tratamento de erros.
     */
    private function registerHandlers(): void
    {
        error_reporting(E_ALL);

        set_error_handler(
            [$this, 'setExecutionError']
        );

        register_shutdown_function(
            [$this, 'setError']
        );
    }

    /**
     * Converte erros PHP em ErrorException.
     *
     * @throws ErrorException
     */
    public function setExecutionError(
        int $level,
        string $message,
        string $file,
        int $line
    ): bool {
        // Ignora níveis que não estejam habilitados.
        if (!(error_reporting() & $level)) {
            return false;
        }

        throw new ErrorException(
            $message,
            0,
            $level,
            $file,
            $line
        );
    }

    /**
     * Detecta erros fatais durante o encerramento do script.
     */
    public function setError(): void
    {
        $error = error_get_last();

        if ($error === null) {
            return;
        }

        $fatalErrors = [
            E_ERROR,
            E_PARSE,
            E_CORE_ERROR,
            E_COMPILE_ERROR,
        ];

        if (!in_array($error['type'], $fatalErrors, true)) {
            return;
        }

        error_log(
            sprintf(
                '[FATAL ERROR] %s [%s:%d]',
                $error['message'],
                $error['file'],
                $error['line']
            )
        );
    }
}
```
