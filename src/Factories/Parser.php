<?php

namespace NFePHP\DUA\Factories;

/**
 * Classe de conversão do TXT para XML
 * @category  API
 * @package   NFePHP\DUA\FActories
 * @copyright FocusIT
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Marlon O. Barbosa <marlon.barbosa at focusit dot com dot br>
 * @link      https://github.com/Focus599Dev/sped-duaes for the canonical source repository
 */

use NFePHP\DUA\Make;
use NFePHP\DUA\Exception\DocumentsException;
use NFePHP\Common\Strings;

class Parser
{

	/**
     * @var array
     */
    protected $structure;

	/**
     * @var string
     */
	protected $version;

	/**
     * @var string
     */
	protected $method;

	/**
     * @var Make
     */
	protected $make;

	/**
     * @var Array
     */
	protected $methods = array(
		'duaEmissao' => 'DuaEmissao',
		'duaConsulta' => 'DuaConsulta',
		'duaObterPdf' => 'DuaObterPdf'
	);

	public function __construct($method, $version = '1.01')
    {
        $ver = str_replace('.', '', $version);
        
		$path = realpath(__DIR__."/../../storage/txtstructure/{$method}{$ver}.json");

		$this->structure = json_decode(file_get_contents($path), true);
        
		$this->version = $version;

		if (!isset( $this->methods[$method] ))
			throw DocumentsException::wrongDocument(19, $method);
			
		$this->method = $this->methods[$method];

        $this->make = new Make($this->method);
    }

	/**
     * Convert txt to XML
     * @param array $nota
     * @return string|null
    */
    public function toXml($nota)
    {
        $this->array2xml($nota);
        if ($this->make->monta()) {
            return $this->make->getXML();
        }

        return null;
    }

	 /**
     * Converte txt array to xml
     * @param array $nota
     * @return void
     */
    protected function array2xml($nota)
    {

        foreach ($nota as $lin) {
            
            $fields = explode('|', $lin);
            if (empty($fields)) {
                continue;
            }
            $metodo = strtolower(str_replace(' ', '', $fields[0])).'Entity' . $this->method;

            if (!method_exists(__CLASS__, $metodo)) {
                //campo não definido
                throw DocumentsException::wrongDocument(16, $lin);
            }
            $struct = $this->structure[strtoupper($fields[0])];

            $std = $this->fieldsToStd($fields, $struct);
            $this->$metodo($std);
        }
    }

    /**
     * Creates stdClass for all tag fields
     * @param array $dfls
     * @param string $struct
     * @return stdClass
     */
    protected static function fieldsToStd($dfls, $struct)
    {
        $sfls = explode('|', $struct);
        $len = count($sfls)-1;
        $std = new \stdClass();

        for ($i = 1; $i < $len; $i++) {
            $name = $sfls[$i];
            
            if (isset($dfls[$i]))
                $data = $dfls[$i];
            else 
                $data = '';

            if (!empty($name)) {

                $std->$name = Strings::replaceSpecialsChars($data);
            }
        }

        return $std;
    }

	/**
     * Create tag emisDuaCda [A]
     * A|versao|tpAmb|cnpjEmi|cnpjOrg|cArea|cServ|cnpjPes|dRef|dVen|dPag|cMun|xInf|
     * @param stdClass $std
     * @return void
     */
    protected function aEntityDuaEmissao(\stdClass $std)
    {
		$std->versao = '1.00';

        $this->make->tagEmisDuaCda($std);
    }

	/**
     * Complete tag emisDuaCda [B]
     * B|vRec|qtde|xIde|
     * @param stdClass $std
     * @return void
     */
    protected function bEntityDuaEmissao($std)
    {
        $this->make->completeEmisDuaCda($std);
    }

	/**
     * Create tag obterPdfDua [A]
     * A|versao|tpAmb|nDua|cpf_cnpj
     * @param stdClass $std
     * @return void
     */
    protected function aEntityDuaConsulta(\stdClass $std)
    {
		$std->versao = '1.00';

        $this->make->tagConsDua($std);
    }

	/**
     * Create tag consDua [A]
     * A|versao|tpAmb|nDua|cpf_cnpj
     * @param stdClass $std
     * @return void
     */
    protected function aEntityDuaObterPdf(\stdClass $std)
    {
		$std->versao = '1.00';

        $this->make->tagConsDua($std);
    }

	
}