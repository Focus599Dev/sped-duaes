<?php

namespace NFePHP\DUA;

/**
 * Class responsible for communication with SEFAZ extends
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
use NFePHP\DUA\Common\Tools as ToolsCommon;
use RuntimeException;
use InvalidArgumentException;

class Tools extends ToolsCommon
{

	private $uf = 'ES';

	 /**
     * Emit DUA
     * @param string $xml
     * @return string
     */
    public function emitDua($xml)
    {
      
        $tpAmb = $this->tpAmb;

        //carrega serviço
        $servico = 'duaEmissao';

        $this->servico(
            $servico,
          	$this->uf,
            $tpAmb
        );
        if ($this->urlService == '') {
            $msg = "A url do serviço não foi encontrada!!!";
            throw new RuntimeException($msg);
        }

        $this->isValid($this->urlVersion, $xml, 'emisDua');

		$request = trim(preg_replace("/<\?xml.*?\?>/", "", $xml));

        $request = $this->makeBody('duaEmissao', $request);

        $this->lastRequest = $request;

        $this->lastResponse = $this->removeStuffs($this->sendRequest($request));

        return $this->lastResponse;
    }

	 /**
     * Consulta DUA
     * @param string $xml
     * @return string
     */
	public function consultaDua($xml){

		$tpAmb = $this->tpAmb;

        //carrega serviço
        $servico = 'duaConsulta';

        $this->servico(
            $servico,
			$this->uf,
            $tpAmb
        );
        if ($this->urlService == '') {
            $msg = "A url do serviço não foi encontrada!!!";
            throw new RuntimeException($msg);
        }

        $this->isValid($this->urlVersion, $xml, 'consDua');

		$request = trim(preg_replace("/<\?xml.*?\?>/", "", $xml));

        $request = $this->makeBody('duaConsulta', $request);

        $this->lastRequest = $request;

        $this->lastResponse = $this->removeStuffs($this->sendRequest($request));

        return $this->lastResponse;

	}

	/**
     * Download PDF DUA
     * @param string $xml
     * @return string
     */
	public function DownloadPDFDua($xml){

		$tpAmb = $this->tpAmb;

        //carrega serviço
        $servico = 'duaObterPdf';

        $this->servico(
            $servico,
			$this->uf,
            $tpAmb
        );
        if ($this->urlService == '') {
            $msg = "A url do serviço não foi encontrada!!!";
            throw new RuntimeException($msg);
        }

        $this->isValid($this->urlVersion, $xml, 'obterPdfDua');

		$request = trim(preg_replace("/<\?xml.*?\?>/", "", $xml));

        $request = $this->makeBody('duaObterPdf', $request);

        $this->lastRequest = $request;

        $this->lastResponse = $this->removeStuffs($this->sendRequest($request));

        return $this->lastResponse;

	}

	/**
     * Consulta de Área e Serviço DUA
     * @param string $xml
     * @return string
     */
	public function ConsultaAreaServico($xml){

		$tpAmb = $this->tpAmb;

        //carrega serviço
        $servico = 'duaConsultaAreaServico';

        $this->servico(
            $servico,
			$this->uf,
            $tpAmb
        );
		
        if ($this->urlService == '') {
            $msg = "A url do serviço não foi encontrada!!!";
            throw new RuntimeException($msg);
        }

        $this->isValid($this->urlVersion, $xml, 'consAreaServico');

		$request = trim(preg_replace("/<\?xml.*?\?>/", "", $xml));

        $request = $this->makeBody('duaConsultaAreaServico', $request);

        $this->lastRequest = $request;

        $this->lastResponse = $this->removeStuffs($this->sendRequest($request));

        return $this->lastResponse;

	}

}
