

var sw= false;



Ext.QuickTips.init(); 
frmConfirmacionEmp = Ext.extend(frmConfirmacionEmpUi, {
    initComponent: function() {
        frmConfirmacionEmp.superclass.initComponent.call(this);
		Ext.getCmp('btnRegistrar').on('click',this.registrar);
		Ext.getCmp('btnCancelar').on('click',this.cancelar);
		Ext.getCmp('txtUsuario').on('blur',this.usuarioUnico);
	    },
	
	registrar:function(){
		// Se verifica que los campos marcados como obligatorios
		// (allowBlank:false) esten llenos
		if (Ext.getCmp('frmConfirmacionEmpForm').getForm().isValid() && sw){
			 Ext.getCmp('frmConfirmacionEmpForm').getForm().submit({ waitMsg : 'Enviando datos...', 
				 													params:{ pClave:hex_md5(Ext.getCmp('txtClave').getValue())
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
			                                      	                        msg: 'Registro exitoso.<BR>Ya Ud. puede ingresar al sistema usando su usuario y clave ingresados.',  
			                                      	                        buttons: Ext.MessageBox.OK,  
			                                      	                        icon: Ext.MessageBox.INFO,
			                                      	                        fn: function (){
			                                    	                    	   Ext.getCmp('frmConfirmacionEmpForm').getForm().reset();
			                                    	                    	   document.location.href='/SIGP/login/index';   
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
		     msg: "Datos incompletados o no v&aacute;lidos, por favor verifique.",
		     width:400,
		     buttons: Ext.MessageBox.OK,
		     icon: Ext.MessageBox.ERROR
		    });
		  }
	}},
	cancelar:function(){
		 Ext.getCmp('frmConfirmacionEmpForm').getForm().reset();
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
