<?php

namespace NFePHP\DUA\Common;

/**
 * Validation for TXT representation of DUA
 * @category  API
 * @package   NFePHP\DUA
 * @copyright FocusIT
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Marlon O. Barbosa <marlon.barbosa at focusit dot com dot br>
 * @link      https://github.com/Focus599Dev/sped-duaes for the canonical source repository
 */


use NFePHP\Common\Strings;

class ValidTXT
{
    public static $errors = [];
    public static $entities = [];
    
    /**
     * Loads structure of txt from json file in storage folder
     * @param float $version
     */
    public static function loadStructure($method , $version = 1.01)
    {
        $path = realpath(__DIR__ . "/../../storage");
        $json = file_get_contents(
            $path . "/txtstructure//" . $method . ($version * 100) . '.json'
        );
        self::$entities = json_decode($json, true);
    }
    
    /**
     * Verifies the validity of txt according to the rules of the code
     * Important: The structures are in the storage folder and must be
     * obtained through reverse engineering with the free sender
     * @param string $txt
     * @return array
     */
    public static function isValid($txt, $method, $version)
    {
        self::loadStructure($method, $version);
        
        $txt = Strings::removeSomeAlienCharsfromTxt($txt);
        
        $rows = explode("\n", $txt);
        
        foreach ($rows as &$row) {

            if (empty($fields)) {
                continue;
            }

            $lastChar = substr($row, -1);
            
            $char = '';

            if ($lastChar != '|') {
               
                $row .= '|';
            }

            $fields = explode('|', $row);

            $count = count($fields);

            $ref = strtoupper($fields[0]);
            
            if (empty($ref)) {
                continue;
            }

            if (!array_key_exists($ref, self::$entities)) {
                self::$errors[] = "ERRO: Essa referência não está definida. [$row]";
                continue;
            }
            $default = count(explode('|', self::$entities[$ref]));
            if ($default > $count) {
                self::$errors[] = "ERRO: O número de parâmetros na linha "
                    . "está errado (esperado #$default) -> (encontrado #$count). [ $row ] Esperado [ "
                    . self::$entities[$ref]." ]";
            }
            foreach ($fields as $field) {
                if ($field != trim($field)) {
                    self::$errors[] = "ERRO: Existem espaços em algum "
                        . "campo dos dados. [$row]";
                    continue;
                }
                //permitindo acentuação, isso pode permitir algumas falhas de validação
                //mas em principio a SEFAZ autoriza o uso de algusn caracteres acentuados
                //apesar de recomendar que não sejam usados
                $newfield = preg_replace("/[^A-Za-zÀ-ú0-9 @,+-_.;:%$\[\]()\/]/", "", $field);
                if ($field != $newfield) {
                    self::$errors[] = "ERRO: Existem caracteres especiais, "
                        . "acentos ou aspas em algum campo dos dados. [" . htmlentities($row) . "]";
                    continue;
                }
            }
        }

        return self::$errors;
    }
}
