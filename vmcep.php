<?php
/**
 * $Id: vmcep.php 1.0.0 2012-06-04 12:52:52 Luiz Weber $
 * @package	    Virtuemart Webservice CEP 
 * @subpackage	Virtuemart CEP
 * @version     1.0.0
 * @description Plugin de Cep para a tela do cadastro do Virtuemart.
 * @copyright	  Copyright © 2016 - Weber TI .
 * @license		  GNU General Public License v2.0
 * @author		  Luiz Weber
 * @author mail	weber@weber.eti.br
 * @website		  http://weber.eti.br
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
 
class plgSystemVmcep extends JPlugin {

    function plgSystemVmcep( &$subject, $config ) {
       parent::__construct( $subject, $config );
    } 
   
    function onAfterRoute() {

		$view 						= JRequest::getVar('view');
      	$ativar_cep					= $this->params->get('ativar_cep');
      	$ativar_cpf	    			= $this->params->get('ativar_cpf');
      	$campo_cpf					= $this->params->get('campo_cpf');
      	$campo_cnpj					= $this->params->get('campo_cnpj');

      	$campo_telefone				= $this->params->get('campo_telefone');
      	$campo_celular				= $this->params->get('campo_celular');

      	$campo_endereco				= $this->params->get('campo_endereco');
      	$campo_bairro				= $this->params->get('campo_bairro');

      	$ativar_autocomplete 		= $this->params->get('ativar_autocomplete');
      	$mesmo_campo_email_usuario 	= $this->params->get('mesmo_campo_email_usuario');

      	$campo_tipo_cadastro		= $this->params->get('campo_tipo_cadastro');
      	$campo_separador			= $this->params->get('campo_separador');

      	$validar_usuario			= $this->params->get('validar_usuario');
      	$validar_email				= $this->params->get('validar_email');
      	$formatar_cpf				= $this->params->get('formatar_cpf');

      	$format 					= JRequest::getVar('format');

      	// valida o formato
      	$funcao_usuario = JRequest::getVar('funcao_vmcep');
      	if (in_array($funcao_usuario,array('validarusuario','validaremail'))) {
      		$valor = JRequest::getVar('valor');
      		if (empty($valor) or trim($valor) == "") {
      			die('2');
      		}

      		if ($funcao_usuario == 'validarusuario') {
      			$campo = 'username';
      		} elseif ($funcao_usuario == 'validaremail') {
      			$campo  = 'email';
      			$mail 	= JFactory::getMailer();
				if (!$mail->ValidateAddress($valor))
					die('1');
      		} else {
      			die('1');
      		} 		


      		$db = JFactory::getDBO(); 
			$query = "SELECT count(u.id) as total FROM #__users u
					WHERE u.".$campo." = ".$db->quote($valor);
			$db->setQuery($query);
			$existe = $db->loadResult();
			if ($existe) {
				echo '3';
			} else {
				echo '0';
			}
			die();
		}		

		// insere a validação
		if ($view == 'user' or ($view=='cart' and $format!='json' and $format!='raw')) {				

			$url =  JURI::root();
			$url_plugin = $url.'/plugins/system/vmcep/assets/js/';
			$pais = 30;

			if ($ativar_cep) {
				$js_cep = "processarCep(false); processarCep(true);";
			} else {
				$js_cep = "";
			}

			$js_usuario 		= "";
			$js_validarusuario 	= "";
			$js_validaremail 	= "";
			$js_formatarcpf 	= "";

			if ($mesmo_campo_email_usuario) {
				$js_usuario = "
				jQuery('#username_field').parent().parent().hide();
				jQuery('input#email_field').bind(\"keyup paste blur\", function() {
					jQuery('input#username_field').val(jQuery(this).val());
				});";
			} elseif ($validar_usuario) {
				$js_validarusuario = "				
				jQuery('#username_field').after('<div id=\"username_div\"></div>');
				jQuery('#username_field').blur(function(){
					validarUsuario();
				});
				";
			}

			if ($validar_email) {
				$js_validaremail = "
				jQuery('#email_field').after('<div id=\"email_div\"></div>');
				jQuery('#email_field').blur(function(){
					validarEmail();
				});
				";
			}


			$js_cpf_cnpj = "";
			if ($ativar_cpf) {
				$js_cpf_cnpj = "";

				if ($campo_cpf != $campo_cnpj) {
					$js_cpf_cnpj .= "
					jQuery('input[name=".$campo_telefone."]').mask('(99) 9999-9999?9');
					jQuery('input[name=shipto_".$campo_telefone."]').mask('(99) 9999-9999?9');

					jQuery('input[name=".$campo_celular."]').mask('(99) 9999-9999?9');
					jQuery('input[name=shipto_".$campo_celular."]').mask('(99) 9999-9999?9');

					jQuery('input[name=".$campo_cpf."]').mask('999.999.999-99').validacpf();
					jQuery('input[name=shipto_".$campo_cpf."]').mask('999.999.999-99').validacpf();
					jQuery('input[name=".$campo_cnpj."]').mask('99.999.999/9999-99').validacnpj();
					jQuery('input[name=shipto_".$campo_cnpj."]').mask('99.999.999/9999-99').validacnpj();

					// validar cpf
					jQuery('input[name=".$campo_cpf."]').change(function(e){						
						jQuery('input[name=".$campo_cpf."]').validacpf();
					});

					// validar cnpj
					jQuery('input[name=".$campo_cnpj."]').change(function(e){						
						jQuery('input[name=".$campo_cnpj."]').validacnpj();
					});

					jQuery('input[name=shipto_".$campo_cpf."]').change(function(e){						
						jQuery('input[name=shipto_".$campo_cpf."]').validacpf();
					});

					// validar cnpj
					jQuery('input[name=shipto_".$campo_cnpj."]').change(function(e){						
						jQuery('input[name=shipto_".$campo_cnpj."]').validacnpj();
					});
					";

					if ($formatar_cpf) {
						$js_formatarcpf = "
						jQuery('input[name=".$campo_cpf."]').closest('form').submit(function(){
							removePontos('".$campo_cpf."');
							removePontos('shipto_".$campo_cpf."');
							removePontos('".$campo_cnpj."');
							removePontos('shipto_".$campo_cnpj."');							
						});";						
					}

				} else {
					$js_cpf_cnpj .= '					
					jQuery("input[name='.$campo_cpf.']").keydown(function(e){
				        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
				            (e.keyCode == 65 && e.ctrlKey === true) ||
				            (e.keyCode == 67 && e.ctrlKey === true) ||
				            (e.keyCode == 88 && e.ctrlKey === true) ||
				            (e.keyCode >= 35 && e.keyCode <= 39)) {
				                 return;
				        }
				        // Ensure that it is a number and stop the keypress
				        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
				            e.preventDefault();
				        } else {			        	

						    try {
						    	jQuery("input[name='.$campo_cpf.']").unmask();
						    } catch (e) {}
						    
						    var tamanho = jQuery("input[name='.$campo_cpf.']").val().length;						    
						    if(tamanho < 11){
						        jQuery("input[name='.$campo_cpf.']").mask("999.999.999-99");						        
							} else {
						        jQuery("input[name='.$campo_cpf.']").mask("99.999.999/9999-99");						        
						    }                   
				        }
					});

					jQuery("input[name='.$campo_cpf.']").change(function(e){
						var tamanho = jQuery("input[name='.$campo_cpf.']").val().length;
						if(tamanho <= 14){
							jQuery("input[name='.$campo_cpf.']").validacpf();
					    } else {
							jQuery("input[name='.$campo_cpf.']").validacnpj();
						}
					});
					
					jQuery("input[name='.$campo_telefone.']").mask(SPMaskBehavior, spOptions);
					jQuery("input[name=shipto_'.$campo_telefone.']").mask(SPMaskBehavior, spOptions);

					jQuery("input[name='.$campo_celular.']").mask(SPMaskBehavior, spOptions);
					jQuery("input[name=shipto_'.$campo_celular.']").mask(SPMaskBehavior, spOptions);
					';			      

					if ($formatar_cpf) {
						$js_formatarcpf = "
						jQuery('input[name=".$campo_cpf."]').closest('form').submit(function(){
							removePontos('".$campo_cpf."');
							removePontos('shipto_".$campo_cpf."');							
						});";						
					}
				}
			}

			$js_autocomplete = '';
			if (!$ativar_autocomplete) {
				$js_autocomplete = "jQuery('#adminForm').attr('autocomplete','false')";
				$js_autocomplete = "jQuery('#adminForm input, #adminForm select').attr('autocomplete','false')";
			}

			$js_cart = "";
			if ($view != 'cart') {
				$js_cart = "jQuery(document).bind(\"ajaxComplete\", function(){
			    		processarCep(false);
			    		processarCep(true);
					});";
			}

			$js_tipo_cadastro = "";
			if ($campo_tipo_cadastro) {
				$js_tipo_cadastro = "// verifica se o cadastro é do tipo juridica ou fisica
				if (jQuery('input[name=".$campo_tipo_cadastro."]').length > 0) {

					if (jQuery('.fieldrevs_".$campo_separador."').length > 0) {
						if (jQuery('input[name=".$campo_tipo_cadastro."]').val() != '2') {
							jQuery('.fieldrevs_".$campo_separador."').hide();
						}

						jQuery('input[name=".$campo_tipo_cadastro."]').click(function(){
							if (jQuery(this).val() == '2') {
								jQuery('.fieldrevs_".$campo_separador."').show('slow');	
							} else {
								jQuery('.fieldrevs_".$campo_separador."').hide('slow');	
							}
						});	
					} else {							

						if (jQuery('input[name=".$campo_tipo_cadastro."]').val() != '2') {
							jQuery('#".$campo_cnpj."_field').parent().parent().parent().parent().parent().hide();
						}

						jQuery('input[name=".$campo_tipo_cadastro."]').click(function(){
							if (jQuery(this).val() == '2') {
								jQuery('#".$campo_cnpj."_field').parent().parent().parent().parent().parent().show('slow');
							} else {
								jQuery('#".$campo_cnpj."_field').parent().parent().parent().parent().parent().hide('slow');
							}
						});	

					}
				}";
			}

			$script = '
			<!-- cpf/cnpj -->						
			'.($campo_cpf == $campo_cnpj?'<script language="javascript" src="'.$url_plugin.'jquery.mask.js"></script>':'<script language="javascript" src="'.$url_plugin.'jquery_mask.js"></script>').'
			<script language="javascript" src="'.$url_plugin.'validar.js"></script>
			<script language="javascript">'.
				"var arr_est=new Array(); arr_est['AC']=109;arr_est['AL']=110;arr_est['AP']=111;arr_est['AM']=112;arr_est['BA']=113;arr_est['CE']=114;arr_est['DF']=115;arr_est['ES']=116;arr_est['GO']=117;arr_est['MA']=118;arr_est['MT']=119;arr_est['MS']=120;arr_est['MG']=121;arr_est['PA']=124;arr_est['PB']=123;arr_est['PR']=122;arr_est['PE']=125;arr_est['PI']=126;arr_est['RJ']=129;arr_est['RN']=127;arr_est['RS']=128;arr_est['RO']=130;arr_est['RR']=131;arr_est['SC']=132;arr_est['SP']=134;arr_est['SE']=133;arr_est['TO']=135;
				var url_plugin ='".$url."/plugins/system/vmcep/buscacep.php';
				// var url_janelacep ='".$url."/plugins/system/vmcep/janelacep.php';
				var url_janelacep ='http://m.correios.com.br/movel/buscaCep.do';

				jQuery(document).ready(function($) {
					".$js_cep."					
					".$js_cpf_cnpj."
					".$js_cart."
					".$js_autocomplete."
					".$js_usuario."
					".$js_validarusuario ."
					".$js_validaremail ."
					".$js_tipo_cadastro."
					".$js_formatarcpf."
				});

				function validarUsuario() {
					var valor 	= jQuery('#username_field').val();
					var status  = false;
					var style   = 'color:red';
					var msg   	= '';
					jQuery('#username_div').html('Validando usuário ...');
					jQuery.ajax({
					  	url: '".$url."/index.php?funcao_vmcep=validarusuario&valor='+valor,
					  	success: function(data) {
					  		switch (data) {
					  			case '0': msg = 'Usuário válido'; status = true; style ='color:green'; break;
					  			case '1': msg = 'Usuário inválido'; break;					  			
					  			case '2': msg = 'Usuário não foi informado'; break;					  								  			
					  			case '3': msg = 'Usuário já existe'; break;					  								  			
					  			default:  msg = 'Usuário inválido'; break;
					  		}					  		
					    	jQuery('#username_div').html('<b style=\"'+style+'\">'+msg+'</b>');
					    	if (!status) {
					  			jQuery('#username_field').addClass('invalid');
					  		} else {					  		
					    		jQuery('#username_field').removeClass('invalid');
					  		}
					  	}
					});
				}

				function validarEmail() {
					var valor = jQuery('#email_field').val();
					var status  = false;
					var style   = 'color:red';
					var msg   	= '';
					jQuery('#email_div').html('Validando e-mail ...');
					jQuery.ajax({
					  	url: '".$url."/index.php?funcao_vmcep=validaremail&valor='+valor,
					  	success: function(data) {
					  		switch (data) {
					  			case '0': msg = 'E-mail válido'; status = true; style ='color:green'; break;
					  			case '1': msg = 'E-mail inválido'; break;					  			
					  			case '2': msg = 'E-mail não foi informado'; break;					  								  			
					  			case '3': msg = 'E-mail já existe'; break;					  								  			
					  			default:  msg = 'E-mail inválido'; break;
					  		}					  		
					    	jQuery('#email_div').html('<b style=\"'+style+'\">'+msg+'</b>');
					    	if (!status) {
					  			jQuery('#email_field').addClass('invalid');
					  		} else {					  		
					    		jQuery('#email_field').removeClass('invalid');
					  		}
					  	}
					});
				}

				function processarCep(st){
					if (!st) {
						var zip_field 		 	= '#zip_field';
						var cep_hidden 		 	= '#cep_hidden1';
						var cep_hidden_field 	= 'cep_hidden1';
						var carr_div_field 	 	= 'carr_div1';
						var carr_div 		 	= '#carr_div1';
						var virtuemart_country_id = 'select[name=virtuemart_country_id]';
						var virtuemart_state_id = 'select[name=virtuemart_state_id]';
						var city_field 		 	= '#city_field';
						var address_1_field  	= '#".$campo_endereco."_field';
						var address_2_field  	= '#".$campo_bairro."_field';
					} else {
						var zip_field 			= '#shipto_zip_field';
						var cep_hidden 			= '#cep_hidden2';
						var cep_hidden_field 	= 'cep_hidden2';
						var carr_div_field 		= 'carr_div2';
						var carr_div 			= '#carr_div2';
						var virtuemart_country_id = 'select[name=shipto_virtuemart_country_id]';
						var virtuemart_state_id = 'select[name=shipto_virtuemart_state_id]';
						var city_field 			= '#shipto_city_field';
						var address_1_field 	= '#shipto_".$campo_endereco."_field';
						var address_2_field 	= '#shipto_".$campo_bairro."_field';
					}

					if (jQuery(zip_field).length == 1 && jQuery(cep_hidden).length == 0) {
						jQuery(zip_field).after('<div id=\"'+carr_div_field+'\" style=\"display:none\"></div>');
						jQuery(zip_field).after('<a href=\"javascript:void(0);\" name=\"buscar_cep\" id=\"buscar_cep\" title=\"Esqueceu seu cep? Clique aqui.\" alt=\"Esqueceu seu cep? Clique aqui.\"  class=\"button_cep\" onclick=\"cep_correios_buscar(); return false;\" style=\"vertical-align:middle\">Esqueci meu cep<a/><input type=\"hidden\" name=\"'+cep_hidden_field+'\" id=\"'+cep_hidden_field+'\"/>');
						jQuery(zip_field).css('width','150px');
						jQuery(zip_field).mask('99999-999');
						jQuery(zip_field).live('blur',function() {
							jQuery(cep_hidden).val(jQuery(zip_field).val());
							jQuery(carr_div).show().html('Carregando dados...');
							var valor_cep = jQuery(cep_hidden).val();
							jQuery.ajax({
								url: url_plugin,
								data: {cep: valor_cep },
	  							success: function(response) {
									eval(\"var arr = \"+response + \";\")
									jQuery(virtuemart_country_id).val(".$pais.");
									jQuery(virtuemart_country_id).trigger('liszt:updated');

									jQuery('div:first').ajaxComplete(function(e, xhr, settings) {										
										jQuery(virtuemart_state_id).val(arr_est[arr.uf]);
										jQuery(virtuemart_state_id).trigger('liszt:updated');								
										jQuery(city_field).val(arr.cidade);
										jQuery(address_1_field).val(arr.endereco);
										jQuery(address_2_field).val(arr.bairro);
										jQuery(carr_div).hide();
									});
									jQuery(zip_field).val(jQuery(cep_hidden).val());							
	  							}
	  						});						
						});
					}
				}

				function cep_correios_buscar() {	
					window.open(url_janelacep,'janelacep','width=425,height=350,scrollbars=0,border=0'); 	
					return false;
				}
				
				function removePontos(nome){
					var el = jQuery('input[name='+nome+']');
					el.val(el.val().replace(/\./g,''));
					el.val(el.val().replace(/-/g,''));
					el.val(el.val().replace(/\//g,''));
				}

				".
			'</script>';		

			if (!defined('SCRIPT_CEP')) {
				$document =JFactory::getDocument();
				if (method_exists($document, 'addCustomTag')) {
					$document->addCustomTag($script);					
					define('SCRIPT_CEP',true);
				}
			}
			
		}
    }
    
} // END PLUGIN  Vmcep
