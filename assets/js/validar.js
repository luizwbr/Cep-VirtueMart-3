jQuery.noConflict();

jQuery.fn.validacpf = function(){ 

        CPF = jQuery(this).val();
        if(!CPF){ return false;}
        erro  = new String;
        cpfv  = CPF;
        if(cpfv.length == 14 || cpfv.length == 11){
            cpfv = cpfv.replace('.', '');
            cpfv = cpfv.replace('.', '');
            cpfv = cpfv.replace('-', '');
  
            var nonNumbers = /\D/;
    
            if(nonNumbers.test(cpfv)){
                erro = "A verificacao de CPF suporta apenas números!";
            }else{
                if (cpfv == "00000000000" ||
                    cpfv == "11111111111" ||
                    cpfv == "22222222222" ||
                    cpfv == "33333333333" ||
                    cpfv == "44444444444" ||
                    cpfv == "55555555555" ||
                    cpfv == "66666666666" ||
                    cpfv == "77777777777" ||
                    cpfv == "88888888888" ||
                    cpfv == "99999999999") {    
                            
                    erro = "Número de CPF inválido!"
                }
                var a = [];
                var b = new Number;
                var c = 11;
  
                for(i=0; i<11; i++){
                    a[i] = cpfv.charAt(i);
                    if (i < 9) b += (a[i] * --c);
                }
                if((x = b % 11) < 2){
                    a[9] = 0
                }else{
                    a[9] = 11-x
                }
                b = 0;
                c = 11;
                for (y=0; y<10; y++) b += (a[y] * c--);
    
                if((x = b % 11) < 2){
                    a[10] = 0;
                }else{
                    a[10] = 11-x;
                }
                if((cpfv.charAt(9) != a[9]) || (cpfv.charAt(10) != a[10])){
                    erro = "Número de CPF inválido.";
                }
            }
        } else {
            if(cpfv.length == 0){
                return false;
            }else{
                erro = "Número de CPF inválido.";
            }
        }
        if (erro.length > 0){
            jQuery(this).val('');
            alert(erro);
            setTimeout(function(){jQuery(this).focus();},100);
            return false;
        }
        return jQuery(this);

}

jQuery.fn.validacnpj = function(){ 
        cnpj = jQuery(this).val();
        erro  = new String;
        if(!cnpj){ return false;}
        cnpj = jQuery.trim(cnpj);// retira espaços em branco
        // DEIXA APENAS OS NÚMEROS
        cnpj = cnpj.replace('/','');
        cnpj = cnpj.replace('.','');
        cnpj = cnpj.replace('.','');
        cnpj = cnpj.replace('-','');
     
        var numeros, digitos, soma, i, resultado, pos, tamanho, digitos_iguais;
        digitos_iguais = 1;
     
        if (cnpj.length < 14 && cnpj.length < 15){
           erro = "Número de CNPJ inválido.";
        }
        for (i = 0; i < cnpj.length - 1; i++){
            if (cnpj.charAt(i) != cnpj.charAt(i + 1)){
                digitos_iguais = 0;
                break;
            }
        }
     
        if (!digitos_iguais){
            tamanho = cnpj.length - 2
            numeros = cnpj.substring(0,tamanho);
            digitos = cnpj.substring(tamanho);
            soma = 0;
            pos = tamanho - 7;
     
            for (i = tamanho; i >= 1; i--){
                soma += numeros.charAt(tamanho - i) * pos--;
                if (pos < 2){
                    pos = 9;
                }
            }
            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
            if (resultado != digitos.charAt(0)){
                erro = "Número de CNPJ inválido.";
            }
            tamanho = tamanho + 1;
            numeros = cnpj.substring(0,tamanho);
            soma = 0;
            pos = tamanho - 7;
            for (i = tamanho; i >= 1; i--){
                soma += numeros.charAt(tamanho - i) * pos--;
                if (pos < 2){
                    pos = 9;
                }
            }
            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
            if (resultado != digitos.charAt(1)){
                erro = "Número de CNPJ inválido.";
            }            
        } else {
            erro = "Número de CNPJ inválido.";
        }

        if (erro.length > 0){
            jQuery(this).val('');
            alert(erro);
            setTimeout(function(){jQuery(this).focus();},100);
            return false;
        }
        return jQuery(this);

}


var SPMaskBehavior = function (val) {
  return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
},
spOptions = {
  onKeyPress: function(val, e, field, options) {
      field.mask(SPMaskBehavior.apply({}, arguments), options);
    }
};


