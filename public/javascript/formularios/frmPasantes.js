var sw = false;

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
	soloLetrasNumerosMask: /[a-zA-Z0-9]/,
	soloLetrasNumeros: function(value,field){  
    return true ;
},
soloLetrasNumerosText: 'Solo debe contener A-Z,a-z,0-9 .<br/>Por favor, verifiquelas.'
});


Ext.apply(Ext.form.VTypes, {
	soloNumeroMask: /[ \d\.\(\)]/,
	soloNumero: function(value,field){  
    return value.replace(/[ \.\(\)]/g,'').length == 12 ;
}

});

Ext.QuickTips.init(); 

frmPasantes = Ext.extend(frmPasantesUi, {
    initComponent: function() {
        frmPasantes.superclass.initComponent.call(this);
        Ext.getCmp('cmbEstado').on('select',this.cargarCiudades);
        Ext.getCmp('cmbDecanato').on('select',this.buscarCarreras);
        //this.buscarCarrera;
        Ext.getCmp('btnBuscar').on('click',this.buscarPasante);
        Ext.getCmp('btnGuardar').on('click',this.registrar);
        
        Ext.getCmp('btnAtrasPersonal').on('click', this.habilitar_AP );
        Ext.getCmp('btnAdelantePersonal').on('click', this.habilitar_AdP);
        Ext.getCmp('btnAtrasContacto').on('click', this.habilitar_AC);
        Ext.getCmp('btnAdelanteContacto').on('click', this.habilitar_AdC);
        Ext.getCmp('btnAtrasAcceso').on('click', this.habilitar_AA);
       
        Ext.getCmp('btnCancelar').on('click',this.cancelar);
        Ext.getCmp('txtUsuario').on('blur',this.usuarioUnico);
    },
    
    habilitar_AP:function(){
    	Ext.getCmp('ptnPersonal').disable();
		Ext.getCmp('ptnIdentificacion').enable();
		Ext.getCmp('panelPasante').setActiveTab(0);
    },
    
    habilitar_AdP:function(){
    	Ext.getCmp('ptnPersonal').disable();
		Ext.getCmp('ptnIdentificacion').disable();
		Ext.getCmp('ptnContacto').enable();
		Ext.getCmp('panelPasante').setActiveTab(2);
    },
    
    habilitar_AdC:function(){
    	Ext.getCmp('ptnPersonal').disable();
		Ext.getCmp('ptnAcceso').enable();
		Ext.getCmp('ptnContacto').disable();
		Ext.getCmp('panelPasante').setActiveTab(3);
    },
    
    habilitar_AC:function(){
    	Ext.getCmp('ptnPersonal').enable();    	
		Ext.getCmp('ptnAcceso').disable();
		Ext.getCmp('ptnContacto').disable();
		Ext.getCmp('panelPasante').setActiveTab(1);
    },
    
    habilitar_AA:function(){
    	Ext.getCmp('ptnContacto').enable();
		Ext.getCmp('ptnAcceso').disable();
		Ext.getCmp('panelPasante').setActiveTab(2);
    },
    
    
    cargarCiudades:function(){
    	Ext.getCmp('cmbCiudad').clearValue();
  	  	Ext.getCmp('cmbCiudad').store.reload({params: {idEstado: Ext.getCmp('cmbEstado').getValue()}});
    },
    
    buscarCarreras:function(){
    	Ext.getCmp('cmbCarrera').clearValue();
  	  	Ext.getCmp('cmbCarrera').store.reload({params: {idDecanato: Ext.getCmp('cmbDecanato').getValue()}});
    },
    
    registrar:function(){
    	if (Ext.getCmp('registroPasanteForm').getForm().isValid() && sw){
			 Ext.getCmp('registroPasanteForm').getForm().submit(
				  { waitMsg : 'Enviando datos...', 
					params:{estado:Ext.getCmp('cmbEstado').getValue(),
					  		ciudad:Ext.getCmp('cmbCiudad').getValue(),
					  		carrera: Ext.getCmp('cmbCarrera').getValue(),
					  		decanato: Ext.getCmp('cmbDecanato').getValue(),
					  		tipoPasantia: Ext.getCmp('cmbTipoPasantia').getValue(),
					  		modalidad: Ext.getCmp('cmbModalidadPasantia').getValue(),
					  		opcF: Ext.getCmp('opcFemenino').getValue(),
					  		opcM: Ext.getCmp('opcMasculino').getValue(),
					  		clave:hex_md5(Ext.getCmp('txtClave').getValue())
				   			},
				   
				   failure: function (form, action){
					   		Ext.MessageBox.show({  
			                title: 'Error',  
			                msg: 'Error al registrar pasante.',  
			                buttons: Ext.MessageBox.OK,  
			                icon: Ext.MessageBox.ERROR  
			                });  
				   			},
			       
			       success: function (form, request){   
			    	   		Ext.MessageBox.show({  
			                title: 'Informaci&oacute;n',  
			                msg: 'Registro exitoso. <BR>Antes de continuar confirme su registro accediendo a la cuenta de correo ingresada.',  
			                buttons: Ext.MessageBox.OK,  
			                icon: Ext.MessageBox.INFO,
			                
			                fn: function (){
			                Ext.getCmp('registroPasanteForm').getForm().reset();
			                Ext.getCmp('frmPasantesWin').close();                                    	                           
			                }
			    	   		});
				   			}  
				  }); 
		} else {
			if (sw == false){
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
		}
    },
    	
    buscarPasante:function(){
    	var ced = Ext.getCmp('txtCedula').getValue();
    	var fech = Ext.getCmp('dataFecha').getRawValue();
    	
    	if (Ext.getCmp('txtCedula').getRawValue().length > 0){
    		Ext.Ajax.request({
    			url: '/SIGP/pasante/buscarPasante',
    			method: 'POST',
    			
    			params: {cedula:Ext.getCmp('txtCedula').getValue(),
    					 fecha: Ext.getCmp('dataFecha').getRawValue(),
    					 sexoF: Ext.getCmp('opcFemenino').getValue(),
    					 sexoM: Ext.getCmp('opcMasculino').getValue(),
    					},

    			success: function(respuesta, request) {
    	      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
    	      				
    	      				if ((jsonData.success ==true) && (jsonData.errorMsj=='')){
	         	        			
         	        			Ext.getCmp('ptnPersonal').enable();
         	        			Ext.getCmp('ptnIdentificacion').disable();
         	        			
         	        			habilitarCampos(true);
         	        			Ext.getCmp('panelPasante').setActiveTab(1);
    	      					
    	      				}else if((jsonData.success ==true) && (jsonData.errorMsj!='')){
    	      					Ext.MessageBox.show({
    	      						title: "Error",
    	      						msg: "Estudiante ya se encuentra registrado. Por favor verifique.",
    	      						width:400,
    	      						buttons: Ext.MessageBox.OK,
    	      						icon: Ext.MessageBox.ERROR
    	      			    });
    	         	        			
    	      				};
             	        
    	      				}
    				});
    		}
    	},
    
    cancelar:function(){
    	Ext.getCmp('registroPasanteForm').getForm().reset();
    },
    
    usuarioUnico:function(){
		var username = Ext.getCmp('txtUsuario');
		if (username.getValue().length > 5){
			Ext.Ajax.request({
				url: 'findUsername',
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

function habilitarCampos(flag){
	if (flag==true){
		Ext.getCmp('txtNombre').enable();
		Ext.getCmp('txtApellido').enable();
		Ext.getCmp('cmbDecanato').enable();
		Ext.getCmp('cmbCarrera').enable();
		Ext.getCmp('txtIndice').enable();
     	Ext.getCmp('txtSemestre').enable();
     	Ext.getCmp('opcFemenino').enable();
     	Ext.getCmp('opcMasculino').enable();
	}else{
		Ext.getCmp('txtNombre').disable();
		Ext.getCmp('txtApellido').disable();
		Ext.getCmp('txtDecanato').disable();
		Ext.getCmp('txtCarrera').disable();
		Ext.getCmp('txtIndice').disable();
     	Ext.getCmp('txtSemestre').disable();
     	Ext.getCmp('opcFemenino').disable();
     	Ext.getCmp('opcMasculino').disable();
		
	}
	
}