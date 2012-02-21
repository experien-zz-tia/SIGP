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

frmActualizarPasantes = Ext.extend(frmActualizarPasantesUi, {
    initComponent: function() {
        frmActualizarPasantes.superclass.initComponent.call(this);
        Ext.getCmp('cmbEstado').on('select',this.cargarCiudades);
        Ext.getCmp('cmbDecanato').on('select',this.buscarCarreras);
        Ext.getCmp('cmbCarrera').on('select',this.cargarSemestres);

        Ext.getCmp('btnBuscar').on('click',this.buscarPasante);
        Ext.getCmp('btnGuardar').on('click',this.registrar);
        
        Ext.getCmp('btnAtrasPersonal').on('click', this.habilitar_AP );
        Ext.getCmp('btnAdelantePersonal').on('click', this.habilitar_AdP);
        Ext.getCmp('btnAtrasContacto').on('click', this.habilitar_AC);
        Ext.getCmp('btnAdelanteContacto').on('click', this.habilitar_AdC);
       
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
    
    
    habilitar_AC:function(){
    	Ext.getCmp('ptnPersonal').enable();    	
		Ext.getCmp('ptnContacto').disable();
		Ext.getCmp('panelPasante').setActiveTab(1);
    },
    
    cargarCiudades:function(){
    	Ext.getCmp('cmbCiudad').clearValue();
  	  	Ext.getCmp('cmbCiudad').store.reload({params: {idEstado: Ext.getCmp('cmbEstado').getValue()}});
    },
    
    buscarCarreras:function(){
    	Ext.getCmp('cmbCarrera').clearValue();
  	  	Ext.getCmp('cmbCarrera').store.reload({params: {idDecanato: Ext.getCmp('cmbDecanato').getValue()}});
    },
    
    cargarSemestres:function(){
    	Ext.getCmp('cmbSemestre').clearValue();
  	  	Ext.getCmp('cmbSemestre').store.reload({params: {idCarrera: Ext.getCmp('cmbCarrera').getValue()}});
    },
    
    registrar:function(){
    	if (Ext.getCmp('actualizarPasanteForm').getForm().isValid() && sw){
			 Ext.getCmp('actualizarPasanteForm').getForm().submit(
				  { waitMsg : 'Enviando datos...', 
					params:{estado:Ext.getCmp('cmbEstado').getValue(),
					  		ciudad:Ext.getCmp('cmbCiudad').getValue(),
					  		carrera: Ext.getCmp('cmbCarrera').getValue(),
					  		decanato: Ext.getCmp('cmbDecanato').getValue(),
					  		tipoPasantia: Ext.getCmp('cmbTipoPasantia').getValue(),
					  		modalidad: Ext.getCmp('cmbModalidadPasantia').getValue(),
					  		opcF: Ext.getCmp('opcFemenino').getValue(),
					  		opcM: Ext.getCmp('opcMasculino').getValue()
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
			                Ext.getCmp('actualizarPasanteForm').getForm().reset();
			                Ext.getCmp('frmActualizarPasantesWin').close();                                    	                           
			                }
			    	   		});
				   			}  
				  }); 
		} else {
				Ext.MessageBox.show({
					title: "Error",
					msg: "Datos incompletados o no v&aacute;lidos, por favor verifique.",
					width:400,
					buttons: Ext.MessageBox.OK,
					icon: Ext.MessageBox.ERROR
		    });
		}
    },
    	
    buscarPasante:function(){
    	var ced = Ext.getCmp('txtCedula').getValue();
    	
    	if (Ext.getCmp('txtCedula').getRawValue().length >= 7){
    		Ext.Ajax.request({
    			url: '/SIGP/pasante/buscarPasante',
    			method: 'POST',
    			
    			params: {cedula:Ext.getCmp('txtCedula').getValue()},

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
    	Ext.getCmp('actualizarPasanteForm').getForm().reset();
    },
});

function habilitarCampos(flag){
	if (flag==true){
		Ext.getCmp('txtNombre').enable();
		Ext.getCmp('txtApellido').enable();
		Ext.getCmp('cmbDecanato').enable();
		Ext.getCmp('cmbCarrera').enable();
		Ext.getCmp('txtIndice').enable();
     	Ext.getCmp('cmbSemestre').enable();
     	Ext.getCmp('opcFemenino').enable();
     	Ext.getCmp('opcMasculino').enable();
	}else{
		Ext.getCmp('txtNombre').disable();
		Ext.getCmp('txtApellido').disable();
		Ext.getCmp('txtDecanato').disable();
		Ext.getCmp('txtCarrera').disable();
		Ext.getCmp('txtIndice').disable();
     	Ext.getCmp('cmbSemestre').disable();
     	Ext.getCmp('opcFemenino').disable();
     	Ext.getCmp('opcMasculino').disable();
		
	}
	
}
