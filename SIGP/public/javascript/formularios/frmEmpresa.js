
var sw= false;

Ext.apply(Ext.form.VTypes, {
	soloLetrasNumerosMask: /[a-zA-Z0-9]/,
	soloLetrasNumeros: function(value,field){  
    return true ;
},
soloLetrasNumerosText: 'Solo debe contener A-Z,a-z,0-9 .<br/>Por favor, verifiquelas.'
});

Ext.apply(Ext.form.VTypes, {
	soloNumeroMask: /[ \d\-\(\)]/,
	soloNumero: function(value,field){  
    return value.replace(/[ \-\(\)]/g,'').length == 12 ;
}

});

Ext.apply(Ext.form.VTypes, {
	password: function(val, field) {
	if (field.campoInicialClave) {
		var pwd = Ext.getCmp(field.campoInicialClave);
		return (val == pwd.getValue());
	}
	
	return true;
},
passwordText: 'Contrase\u00f1as no coinciden.<br/>Por favor, verifiquelas.'
});

Ext.apply(Ext.form.VTypes, {
	emailIguales: function(val, field) {
	if (field.campoInicial) {
		var email = Ext.getCmp(field.campoInicial);
		if  (val != email.getValue()){
			field.markInvalid(); 
			return false;
		}
	}
	return true;
},
emailIgualesText:'Cuentas de correo no coindicen.<Br/>Por favor, verifiquelas.'
});

Ext.QuickTips.init(); 
frmEmpresa = Ext.extend(frmEmpresaUi, {
    initComponent: function() {
        frmEmpresa.superclass.initComponent.call(this);
		Ext.getCmp('cmbEstado').on('select',this.actualizarCiudades);
		Ext.getCmp('btnRegistrar').on('click',this.registrarEmpresa);
		Ext.getCmp('btnCancelar').on('click',this.cancelar);
		Ext.getCmp('txtUsuario').on('blur',this.usuarioUnico);
	    },
	actualizarCiudades:function(){
	  Ext.getCmp('cmbCiudad').clearValue();
	  Ext.getCmp('cmbCiudad').store.reload({params: {idEstado: Ext.getCmp('cmbEstado').getValue()}});
	},
	registrarEmpresa:function(){
		// Se verifica que los campos marcados como obligatorios
		// (allowBlank:false) esten llenos
		if (Ext.getCmp('registroEmpresaForm').getForm().isValid() && sw){
			 Ext.getCmp('registroEmpresaForm').getForm().submit({ waitMsg : 'Enviando datos...', 
				 													params:{estado:Ext.getCmp('cmbEstado').getValue(),
				                                                        ciudad:Ext.getCmp('cmbCiudad').getValue(),
				                                                        clave:hex_md5(Ext.getCmp('txtClave').getValue())
				                                                        },
				                                                failure: function (form, action){
                                      	                                   Ext.MessageBox.show({  
			                                    	                        title: 'Error',  
			                                    	                        msg: 'Error al registrar.',  
			                                    	                        buttons: Ext.MessageBox.OK,  
			                                    	                        icon: Ext.MessageBox.ERROR  
			                                    	                      });  
			                                    	                     },  
			                                                    success: function (form, request){   
			                                    	                       Ext.MessageBox.show({  
			                                      	                        title: 'Informaci&oacute;n',  
			                                      	                        msg: 'Registro exitoso.<BR>Antes de continuar confirme su registro accediendo a la cuenta de correo ingresada.',  
			                                      	                        buttons: Ext.MessageBox.OK,  
			                                      	                        icon: Ext.MessageBox.INFO,
			                                      	                        fn: function (){
			                                    	                    	   Ext.getCmp('registroEmpresaForm').getForm().reset();
			                                    	                    	   Ext.getCmp('frmEmpresaWin').close();                                    	                           

			                                    	                       }
			                                      	                       });
			                                    	                      }  
				                                                    }); 
		} else {
			if (sw==false){
				Ext.MessageBox.show({
				     title: "Error",
				     msg: "Nombre de usuario no v&aacute;lido, por favor intente con uno diferente.",
				     width:400,
				     buttons: Ext.MessageBox.OK,
				     icon: Ext.MessageBox.ERROR
				    });
				
			}else{
		   Ext.MessageBox.show({
		     title: "Error",
		     msg: "Datos incompletos o no v&aacute;lidos, por favor verifique.",
		     width:400,
		     buttons: Ext.MessageBox.OK,
		     icon: Ext.MessageBox.ERROR
		    });
		  }
	}},
	cancelar:function(){
		 Ext.getCmp('registroEmpresaForm').getForm().reset();
	},

	usuarioUnico:function(){
		//Verificar que el nombre de usuario sea unico
		var username = Ext.getCmp('txtUsuario');
		if (username.getValue().length > 5){
			Ext.Ajax.request({
				url: '/SIGP/registro/findUsername',
				method: 'POST',
				params: 'username=' + username.getValue(),
				success: function(o) {
				if (o.responseText == 1) {
					username.markInvalid('Nombre de usuario en uso o no permitido.<Br/> Escriba uno diferente.');
					sw=false;
				} else if (o.responseText == 0){
				//	username.clearInvalid();
					sw=true;
				}
	        	}
			});
		}
	}

});
