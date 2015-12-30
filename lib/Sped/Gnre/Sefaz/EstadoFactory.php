<?php

/**
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

namespace Sped\Gnre\Sefaz;

class EstadoFactory
{

    /**
     * @param string $estado
     * @return Sped\Gnre\Sefaz\Estados\Padrao
     */
    public function create($estado = 'BA')
    {
        $classe = sprintf(
            '\Sped\Gnre\Sefaz\Estados\%s',
            $estado
        );

        if (!class_exists($classe)) {
            $classe = '\Sped\Gnre\Sefaz\Estados\BA';
        }

        return new $classe();
    }
}