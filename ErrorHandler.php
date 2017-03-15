<?php

namespace Tayron;

use Tayron\exceptions\Exception;

/**
 * Classe que intercepta todos os erros lançados pelo PHP e os transforma em uma exceção
 *
 * @author Tayron Miranda <dev@tayron.com.br>
 */
final class ErrorHandler 
{
    /**
     * Armazena instancia de ErrorHandler
     * 
     * @var ErrorHandler
     */
    private static $instance;

    /**
     * Template da mensagem de erro
     * 
     * @var string
     */
    private $template = '<b>%s</b><br /> <b>Arquivo: </b>%s <br /> <b>Linha do arquivo:</b> %s<br /> <b>Tipo de erro:</b> %s <br /> <b>Versão do PHP:</b> %s';

    /**
     * Lista com tipos de erros do PHP
     * @see http://php.net/manual/en/errorfunc.constants.php
     * 
     * @var array
     */
    private $errorList = array(
        E_ERROR => 'E_ERROR: Erros fatais em tempo de execução. Estes indicam erros que não podem ser recuperados, como problemas de alocação de memória. A execução do script é interrompida',
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR: Indica que um erro provavelmente perigoso aconteceu, mas não deixou o Engine em um estado instáve',
        E_WARNING => 'E_WARNING: Avisos em tempo de execução (erros não fatais)',
        E_PARSE => 'E_PARSE : Erro em tempo de compilação. Erros gerados pelo interpretador',
        E_NOTICE => 'E_NOTICE: Notícia em tempo de execução. Indica que o script encontrou alguma coisa que pode indicar um erro, mas que também possa acontecer durante a execução normal do script',
        E_STRICT => 'E_STRICT: Notícias em tempo de execução. Permite ao PHP sugerir mudanças ao seu código as quais irão assegurar melhor interoperabilidade e compatibilidade futura do seu código',
        E_DEPRECATED => 'E_DEPRECATED: Avisos em tempo de execução. Habilite-o para receber avisos sobre código que não funcionará em futuras versões',
        E_CORE_ERROR => 'E_CORE_ERROR: Erro fatal que acontece durante a inicialização do PHP. Este é parecido com E_ERROR, exceto que é gerado pelo núcleo do PHP',
        E_CORE_WARNING => 'E_CORE_WARNING: Avisos (erros não fatais) que aconteçam durante a inicialização do PHP. Este é parecido com E_WARNING, exceto que é gerado pelo núcleo do PHP',
        E_COMPILE_ERROR => 'E_COMPILE_ERROR: Erro fatal em tempo de compilação. Este é parecido com E_ERROR, exceto que é gerado pelo Zend Scripting Engine',
        E_COMPILE_WARNING => 'E_COMPILE_WARNING: Aviso em tempo de compilação. Este é parecido com E_WARNING, exceto que é geredo pelo Zend Scripting Engine',
        E_USER_ERROR => 'E_USER_ERROR: Erro gerado pelo usuário. Este é parecido com E_ERROR, exceto que é gerado pelo código PHP usando a função trigger_error()',
        E_USER_WARNING => 'E_USER_WARNING: Aviso gerado pelo usuário. Este é parecido com E_WARNING, exceto que é gerado pelo código PHP usando a função trigger_error()',
        E_USER_NOTICE => 'E_USER_NOTICE: Notícia gerada pelo usuário. Este é parecido com E_NOTICE, exceto que é gerado pelo código PHP usando a função trigger_error()',
        E_USER_DEPRECATED => 'E_USER_DEPRECATED: Mensagem de aviso gerado pelo usuário. Este é como um E_DEPRECATED, exceto que é gerado em código PHP usando a função trigger_error()'
    );

    /**
     * ErrorHandler::__construct
     * 
     * Impede com que o objeto seja instanciado
     * 
     * @return void
     */
    final private function __construct() 
    {
        
    }

    /**
     * ErrorHandler::__clone
     * 
     * Impede que a classe Requisição seja clonada
     *
     * @throws Exception Lança execção caso o usuário tente clonar este classe
     *
     * @return void
     */
    final public function __clone() 
    {
        throw new Exception('A classe Erro não pode ser clonada.');
    }

    /**
     * ErrorHandler::__wakeup
     * 
     * Impede que a classe Requisição execute __wakeup
     *
     * @throws Exception Lança execção caso o usuário tente executar este método
     *
     * @return void
     */
    final public function __wakeup() 
    {
        throw new Exception('A classe Erro não pode executar __wakeup.');
    }

    /**
     * ErrorHandler::getInstance
     * 
     * Retorna uma instância única de uma classe.
     *
     * @return ErrorHandler Retorna instancia única de ErrorHandler
     */
    public static function getInstance() 
    {
        if (!static::$instance) {
            static::$instance = new static();
        }

        error_reporting(0);
        set_error_handler(array(self::$instance, 'setExecutionError'));
        register_shutdown_function(array(self::$instance, 'setError'));

        return self::$instance;
    }

    /**
     * ErrorHandler::setExecutionError
     * 
     * Dispara uma exceção caso algum erro em tempo de execução ocorra
     * 
     * @link http://php.net/manual/pt_BR/function.set-error-handler.php
     * 
     * @param int $errorLevel Contém o nível de erro que aconteceu, como um inteiro. 
     * @param string $formatedMessage Contém a mensagem de erro, como uma string. 
     * @param string $fileName Contém o nome do arquivo no qual o erro ocorreu, como uma string. 
     * @param int $fileLine Contém o número da linha na qual o erro ocorreu, como um inteiro. 
     * @param array $contextError Conter uma matriz de cada váriavel que exista no escopo aonde o erro aconteceu.
     * 
     * @throws Exception Objeto responsável por tratar a exceção da aplicalção
     * @return void
     */
    public function setExecutionError($errorLevel, $messageErro, $fileName, $fileLine, array $contextError) 
    {
        $formatedMessage = sprintf($this->template, $messageErro, $fileName, $fileLine, 
            $this->getErrorDescription($errorLevel), PHP_VERSION);
        
        throw new Exception($formatedMessage);
    }

    /**
     * ErrorHandler::setError
     * 
     * Dispara uma exceção caso algum erro em tempo de execução ocorra
     * 
     * @throws Exception Objeto responsável por tratar a exceção da aplicalção
     * @return void
     */
    public function setError() 
    {
        $error = error_get_last();
        if (is_array($error)) {
            $formatedMessage = sprintf($this->template, $error['message'], 
                $error['file'], $error['line'], $this->getErrorDescription($error['type']), PHP_VERSION);
            
            throw new Exception($formatedMessage, false);
        }
    }

    /**
     * ErrorHandler::getErrorDescription
     * 
     * Método que retorna uma descrição do erro gerado pelo PHP
     * 
     * @param int $errorLevel Código do erro informado pelo PHP
     * @return string com descrição do erro gerado pelo PHP
     */
    public function getErrorDescription($errorLevel) 
    {
        return (isset($this->errorList[$errorLevel])) ? $this->errorList[$errorLevel] : $errorLevel;
    }
}