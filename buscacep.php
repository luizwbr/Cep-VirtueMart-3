<?php
/**
 * $Id: buscacep.php 1.0.0 2012-06-04 12:52:52 Luiz Weber $
 * @package	    Virtuemart Webservice CEP 
 * @subpackage	Virtuemart CEP
 * @version     1.0.0
 * @description Plugin de Cep para a tela do cadastro do Virtuemart.
 * @copyright	  Copyright © 2012 -  All rights reserved.
 * @license		  GNU General Public License v2.0
 * @author		  Luiz Weber
 *
 */

@$cep = $_REQUEST['cep'];
if (isset($cep) and $cep != '') {			
	$cep = str_replace(array(" ","","-",",","'",'"'),"",$cep);

	$url = "http://viacep.com.br/ws/".$cep."/json/";

	if (function_exists('curl_exec')) {
		$ch 		= curl_init();
		$timeout 	= 0; // set to zero for no timeout
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$conteudo_cep 	= curl_exec($ch);		
	} else {
		$conteudo_cep = file_get_contents($url);		
	}

	$cp = json_decode($conteudo_cep);

	$bairro = str_replace('&#39;',"'",$cp->bairro);

	echo "{uf:'".(strtoupper($cp->uf))."',";
	echo "cidade:'".addslashes($cp->localidade)."',";
	echo "bairro:'".addslashes($bairro)."',";
	echo "endereco:\"".addslashes($cp->logradouro)."\",";
	echo "cep:'".($cep)."'};"; 
	die();
}
