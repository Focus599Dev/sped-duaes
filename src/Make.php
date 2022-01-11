<?php

namespace NFePHP\DUA;

/**
 * Classe a construção do xml 
 * @category  API
 * @package   NFePHP\DUA
 * @copyright FocusIT
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Marlon O. Barbosa <marlon.barbosa at focusit dot com dot br>
 * @link      https://github.com/Focus599Dev/sped-duaes for the canonical source repository
 */

use DateTime;
use stdClass;
use DOMElement;
use RuntimeException;
use NFePHP\Common\Keys;
use NFePHP\Common\Strings;
use NFePHP\Common\DOMImproved as Dom;
use NFePHP\DUA\Exception\DocumentsException;

class Make
{
    
	/**
     * @var string
     */
	protected $method;

	/**
     * @var DOMElement
     */
    protected $emisDua;

	/**
     * @var DOMElement
     */
    protected $consDua;

	/**
     * @var \NFePHP\Common\DOMImproved
    */
    public $dom;

	/**
     * @var string
     */
    public $xml;

	public function __construct($method){

		$this->method = $method;

		$this->dom = new Dom('1.0', 'UTF-8');
        
		$this->dom->preserveWhiteSpace = false;
        
		$this->dom->formatOutput = false;

	}

	public function monta(){

		if (!method_exists($this, 'monta' . $this->method)){
			throw DocumentsException::wrongDocument(19, $this->method);
		}

		$methodCall = 'monta' . $this->method;

		return $this->{$methodCall}();
	}

	protected function montaDuaEmissao(){
		
		$this->dom->appendChild($this->emisDua);

		$this->xml = $this->dom->saveXML();
        
		return true;
	}

	protected function montaDuaConsulta(){
		
		$this->dom->appendChild($this->consDua);

		$this->xml = $this->dom->saveXML();
        
		return true;
	}

	public function tagConsDua(stdClass $std){

		$possible = [
            'versao',
            'tpAmb',
            'nDua',
            'cpf_cnpj'
        ];

        $std = $this->equilizeParameters($std, $possible);

		$identificador = 'A <consDua> - ';

        $this->consDua = $this->dom->createElement("consDua");
		
		$this->consDua->setAttribute('versao', $std->versao);

		$this->consDua->setAttribute('xmlns', 'http://www.sefaz.es.gov.br/duae');

		$this->dom->addChild(
            $this->consDua,
            "tpAmb",
            $std->tpAmb,
            true,
            $identificador . "Identificação do Ambiente"
        );

		$this->dom->addChild(
            $this->consDua,
            "nDua",
            $std->nDua,
            true,
            $identificador . "Número do DUA."
        );

		if (strlen($std->cpf_cnpj) > 11){
			
			$this->dom->addChild(
				$this->consDua,
				"cnpj",
				$std->cpf_cnpj,
				true,
				$identificador . "CNPJ do cliente."
			);

		} else {

			$this->dom->addChild(
				$this->consDua,
				"cpf",
				$std->cpf_cnpj,
				true,
				$identificador . "CPF do cliente."
			);

		}

		return $this->consDua;
	}

	public function tagEmisDuaCda(stdClass $std){

		$possible = [
            'versao',
            'tpAmb',
            'cnpjEmi',
            'cnpjOrg',
            'cArea',
            'cServ',
            'cnpjPes',
            'dRef',
            'dVen',
            'dPag',
            'cMun',
            'xInf',
        ];

        $std = $this->equilizeParameters($std, $possible);

		$identificador = 'A <emisDua> - ';

        $this->emisDua = $this->dom->createElement("emisDua");
		
		$this->emisDua->setAttribute('versao', $std->versao);

		$this->emisDua->setAttribute('xmlns', 'http://www.sefaz.es.gov.br/duae');

		$this->dom->addChild(
            $this->emisDua,
            "tpAmb",
            $std->tpAmb,
            true,
            $identificador . "Identificação do Ambiente"
        );

		// $this->dom->addChild(
        //     $this->emisDua,
        //     "cnpjEmi",
        //     $std->cnpjEmi,
        //     true,
        //     $identificador . "CNPJ Emissao"
        // );

		$this->dom->addChild(
            $this->emisDua,
            "cnpjOrg",
            $std->cnpjOrg,
            true,
            $identificador . "CNPJ do órgão"
        );

		$this->dom->addChild(
            $this->emisDua,
            "cArea",
            $std->cArea,
            true,
            $identificador . "Código da área"
        );

		$this->dom->addChild(
            $this->emisDua,
            "cServ",
            $std->cServ,
            true,
            $identificador . "Código do serviço"
        );

		$this->dom->addChild(
            $this->emisDua,
            "cnpjPes",
            $std->cnpjPes,
            true,
            $identificador . "CPF ou CNPJ da pessoa"
        );

		$this->dom->addChild(
            $this->emisDua,
            "dRef",
            $std->dRef,
            true,
            $identificador . "Ano e mês de referência"
        );

		$this->dom->addChild(
            $this->emisDua,
            "dVen",
            $std->dVen,
            true,
            $identificador . "Data de vencimento"
        );

		$this->dom->addChild(
            $this->emisDua,
            "dPag",
            $std->dPag,
            true,
            $identificador . "Data de pagamento"
        );

		$this->dom->addChild(
            $this->emisDua,
            "cMun",
            $std->cMun,
            true,
            $identificador . "Código do município da tabela do IBGE"
        );

		$this->dom->addChild(
            $this->emisDua,
            "xInf",
            $std->xInf,
            true,
            $identificador . "Informações Complementares"
        );

		return  $this->emisDua;

	}

	public function completeEmisDuaCda(stdClass $std){

		$possible = [
            'vRec',
            'qtde',
			'xIde'
        ];

        $std = $this->equilizeParameters($std, $possible);

		$identificador = 'B <emisDua> - ';

		if (!$this->emisDua)
			$this->emisDua = $this->dom->createElement("emisDua");
		
		$this->dom->addChild(
			$this->emisDua,
			"vRec",
			$std->vRec,
			false,
			$identificador . "Valor da Receita"
		);

		$this->dom->addChild(
			$this->emisDua,
			"qtde",
			$std->qtde,
			false,
			$identificador . "Quantidade do Serviço"
		);

		$this->dom->addChild(
			$this->emisDua,
			"xIde",
			$std->xIde,
			false,
			$identificador . "Identificador do Contribuinte"
		);
	}

	/**
     * Returns xml string and assembly it is necessary
     * @return string
     */
    public function getXML(){
       
        return $this->xml;
    }

	/**
     * Includes missing or unsupported properties in stdClass
     * @param stdClass $std
     * @param array $possible
     * @return stdClass
     */
    protected function equilizeParameters(stdClass $std, $possible){
        $arr = get_object_vars($std);
        foreach ($possible as $key) {
            if (!array_key_exists($key, $arr)) {
                $std->$key = null;
            }
        }
        return $std;
    }

}
