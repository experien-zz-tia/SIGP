/**
 * Script con validaciones varias
 * @author Robert Arrieche
 */


//Busca en la cadena caracteres en blanco (" "), en caso de encontrar alguno retorna false  
function tieneVacio(campo) {  
        for ( i = 0; i < campo.length; i++ ) {  
                if ( campo.charAt(i) != " " ) {  
                        return true  
                }  
        }  
        return false  
}  
  
//Valida que los campos esten llenos antes de enviar la solicitud
//Requiere de la lib: md5 y formularios/mensajes  
function validaForm(formulario) {  
        if( tieneVacio(formulario.usuario.value) == false ) {  
                Ext.example.msg('Datos incompletos', 'Por favor, ingrese el nombre de usuario.');
                formulario.usuario.focus()   
                return false  
        } else   
        	if  (tieneVacio(formulario.clave.value) == false ) {  
                Ext.example.msg('Datos incompletos', 'Por favor, ingrese la contrase\u00f1a.');
                formulario.clave.focus()   
                return false  
             }else{
             	formulario.clave.value = hex_md5(formulario.clave.value);
                return true;  
        }  
          
}  

