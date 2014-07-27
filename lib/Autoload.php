<?php

/*
 * Este arquivo é parte do programa GNRE PHP
 * GNRE PHP é um software livre; você pode redistribuí-lo e/ou 
 * modificá-lo dentro dos termos da Licença Pública Geral GNU como 
 * publicada pela Fundação do Software Livre (FSF); na versão 2 da 
 * Licença, ou (na sua opinião) qualquer versão.
 * Este programa é distribuído na esperança de que possa ser  útil, 
 * mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer
 * MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * Licença Pública Geral GNU para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU
 * junto com este programa, se não, escreva para a Fundação do Software
 * Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Classe utilizada para carregar as classes utilizadas seguindo o padrão PSR-0 para uma
 * melhor padronização da API
 * @package	gnre
 * @subpackage  autoload
 * @author 	Matheus Marabesi <matheus.marabesi@gmail.com>
 * @link        https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
 * @version     1.0-0.0
 */
class Autoload {

    /**
     * Método utilizado para realizar o carregamento das classes utilizadas nessa API.
     * Sguindo os padrões PSR-0
     * @param  string   $className  Nome da classe a ser carregada
     * @since  1.0-0.0
     */
    public static function __autoload($className) {
        $className = ltrim($className, '\\');
        $fileName = '';
        $namespace = '';

        if ($lastNsPos = strrpos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }

        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        require $fileName;
    }

}

spl_autoload_register('Autoload::__autoload');
