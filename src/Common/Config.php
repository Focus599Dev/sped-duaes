<?php

namespace NFePHP\DUA\Common;

/**
 * Class for validation of config
 * @category  API
 * @package   NFePHP\DUA
 * @copyright FocusIT
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Marlon O. Barbosa <marlon.barbosa at focusit dot com dot br>
 * @link      https://github.com/Focus599Dev/sped-duaes for the canonical source repository
 */


use stdClass;
use NFePHP\DUA\Exception\DocumentsException;

class Config
{
    /**
     * Validate method
     * @param string $content config.json
     * @return boolean
     */
    public static function validate($content)
    {
        if (!is_string($content)) {
            throw DocumentsException::wrongDocument(8, "Não foi passado um json.");
        }
        $std = json_decode($content);
        if (! is_object($std)) {
            throw DocumentsException::wrongDocument(8, "Não foi passado um json valido.");
        }

        return $std;
    }
    
}
