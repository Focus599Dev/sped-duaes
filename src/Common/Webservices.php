<?php

namespace NFePHP\DUA\Common;

/**
 * Class to Read and preprocess WS parameters from xml storage
 * file to json encode or stdClass
 *
 * @category  API
 * @package   NFePHP\DUA
 * @copyright FocusIT
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Marlon O. Barbosa <marlon.barbosa at focusit dot com dot br>
 * @link      https://github.com/Focus599Dev/sped-duaes for the canonical source repository
 */

use SimpleXMLElement;

class Webservices
{
    public $json;
    public $std;
    
    /**
     * Constructor
     * @param string $xml path or xml content from
     *               nfe_ws3_mod55 or nfe_ws3_mod65
     */
    public function __construct($xml)
    {
        $this->toStd($xml);
    }
    
    /**
     * Get webservices parameters for specific conditions
     * the parameters with the authorizers are in a json file in
     * the storage folder
     * @param string $sigla
     * @param string $ambiente "homologacao" ou "producao"
     * @param int $modelo "55" ou "65"
     * @return boolean | \stdClass
     */
    public function get($sigla, $ambiente)
    {
        $autfile = realpath(__DIR__ . '/../../storage/autorizadores.json');
        $autorizadores = json_decode(file_get_contents($autfile), true);
        if (!key_exists($sigla, $autorizadores)) {
            throw new \RuntimeException(
                "Não existe o autorizador [$sigla] para os "
                . "webservices"
            );
        }
        $auto = $autorizadores[$sigla];
        if (empty($auto) || empty($this->std)) {
            return false;
        }
        if (empty($this->std->$auto)) {
            throw new \RuntimeException(
                "Não existem webservices cadastrados para  [$sigla]"
            );
        }
        $svw = $this->std->$auto->$ambiente;
       
        return $svw;
    }

    /**
     * Return WS parameters in a stdClass
     * @param string $xml
     * @return \stdClass
     */
    public function toStd($xml = '')
    {
        if (!empty($xml)) {
            $this->convert($xml);
        }
        return $this->std;
    }
    
    /**
     * Return WS parameters in json format
     * @return string
     */
    public function __toString()
    {
        return (string) $this->json;
    }
    
    /**
     * Read WS xml and convert to json and stdClass
     * @param string $xml
     */
    protected function convert($xml)
    {
        $resp = simplexml_load_string($xml, null, LIBXML_NOCDATA);
        $aWS = [];
        foreach ($resp->children() as $element) {
            $sigla = (string) $element->sigla;
            $aWS[$sigla] = [];
            if (isset($element->homologacao)) {
                $aWS[$sigla] += $this->extract($element->homologacao, 'homologacao');
            }
            if (isset($element->producao)) {
                $aWS[$sigla] += $this->extract($element->producao, 'producao');
            }
        }
        $this->json = json_encode($aWS);
        $this->std = json_decode(json_encode($aWS));
    }
    
    /**
     * Extract data from wbservices XML strorage to a array
     * @param SimpleXMLElement $node
     * @param string $environment
     * @return array
     */
    protected function extract(SimpleXMLElement $node, $environment)
    {
        $amb[$environment] = [];
        foreach ($node->children() as $children) {
            $name = (string) $children->getName();
            $method = (string) $children['method'];
            $operation = (string) $children['operation'];
            $version = (string) $children['version'];
            $url = (string) $children[0];
            $operations = [
                'method' => $method,
                'operation' => $operation,
                'version' => $version,
                'url' => $url
            ];
            $amb[$environment][$name] = $operations;
        }
        return $amb;
    }
}
