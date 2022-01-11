<?php

namespace NFePHP\DUA;

/**
 * Converts DUA from text format to xml
 * @category  API
 * @package   NFePHP\DUA
 * @copyright FocusIT
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Marlon O. Barbosa <marlon.barbosa at focusit dot com dot br>
 * @link      https://github.com/Focus599Dev/sped-duaes for the canonical source repository
 */

use NFePHP\DUA\Common\ValidTXT;
use NFePHP\Common\Strings;
use NFePHP\DUA\Exception\DocumentsException;
use NFePHP\DUA\Factories\Parser;

class Convert
{
    public $txt;
    public $dados;
    public $notas;
    public $layouts = [];
    public $xmls = [];
    public $method = '';
    
    /**
     * Constructor method
     * @param string $txt
     */
    public function __construct($txt = '', $method)
    {
        if (!empty($txt)) {
            $this->txt = trim($txt);
        }

		$this->method =  $method;
        $this->dados = explode("\n", $txt);

    }
    
    /**
     * Static method to convert Txt to Xml
     * @param string $txt
     * @param string $method
     * @return array
     */
    public static function parse($txt, $method)
    {
        $conv = new static($txt, $method);
        return $conv->toXml();
    }
    
    /**
     * Convert all nfe in XML, one by one
     * @param string $txt
     * @return array
     * @throws \NFePHP\NFe\Exception\DocumentsException
     */
    public function toXml($txt = '')
    {
        if (!empty($txt)) {
            $this->txt = trim($txt);
        }
        
        $txt = Strings::removeSomeAlienCharsfromTxt($this->txt);
        
        $this->notas = [$this->dados];

        $this->validNotas();

        $i = 0;

        foreach ($this->notas as $nota) {
           
			$version = $this->layouts[$i];
            $parser = new Parser($this->method, $version);
            
            $this->xmls[] = $parser->toXml($nota);
            $i++;
        }
        return $this->xmls;
    }

	/**
     * Read and set all layouts in NFes
     * @param array $nota
     */
    protected function loadLayouts($nota)
    {
        if (empty($nota)) {
            throw DocumentsException::wrongDocument(17, '');
        }
        foreach ($nota as $campo) {
            
            $fields = explode('|', $campo);
            
            if ($fields[0] == 'A') {
                $this->layouts[] = $fields[1];
                break;
            }
        }
    }

  
    /**
     * Valid all NFes in txt and get layout version for each nfe
     */
    protected function validNotas()
    {
        foreach ($this->notas as $nota) {
            $this->loadLayouts($nota);
            $this->isValidTxt($nota, $this->layouts[0]);
        }
    }


    /**
     * Valid txt structure
     * @param array $nota
     * @throws \NFePHP\NFe\Exception\DocumentsException
     */
    protected function isValidTxt($nota, $version)
    {
        $errors = ValidTXT::isValid(implode("\n", $nota), $this->method, $version);

        if (!empty($errors)) {
            throw DocumentsException::wrongDocument(14, implode("\n", $errors));
        }
    }
}
