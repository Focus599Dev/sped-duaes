<?php
namespace NFePHP\DUA\Exception;

/**
 * Exceptions
 * @category  API
 * @package   NFePHP\DUA
 * @copyright FocusIT
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Marlon O. Barbosa <marlon.barbosa at focusit dot com dot br>
 * @link      https://github.com/Focus599Dev/sped-duaes for the canonical source repository
 */


class DocumentsException extends \InvalidArgumentException implements ExceptionInterface
{
    public static $list = [
        8 => "A configuração (config.json) não é válido {{msg}}.",
		14 => "Falha na validação do TXT:\n {{msg}}.",
        16 => "O txt tem um campo não definido {{msg}}",
        17 => "O txt não está no formato adequado.",
        19 => "Metodo {{msg}} não existe",
    ];
    
    public static function wrongDocument($code, $msg = '')
    {
        $msg = self::replaceMsg(self::$list[$code], $msg);
        return new static($msg);
    }
    
    private static function replaceMsg($input, $msg)
    {
        return str_replace('{{msg}}', $msg, $input);
    }
}
