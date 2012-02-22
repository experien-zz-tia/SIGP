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

        Ext.getCmp('btnGuardar').on('click',this.registrar);
        
        Ext.getCmp('btnAdelantePersonal').on('click', this.habilitar_AdP);
        Ext.getCmp('btnAtrasContacto').on('click', this.habilitar_AC);
        
        this.cargar;
       
    },
    
    habilitar_AdP:function(){
    	Ext.getCmp('ptnPersonal').disable();
		Ext.getCmp('ptnContacto').enable();
		Ext.getCmp('panelPasante').setActiveTab(1);
    },
    
    habilitar_AC:function(){
    	Ext.getCmp('ptnPersonal').enable();    	
		Ext.getCmp('ptnContacto').disable();
		Ext.getCmp('panelPasante').setActiveTab(0);
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
	cargar:function(){
			Ext.Ajax.request({
				url: '/SIGP/pasante/buscarPasanteExistente',
				method: 'POST',
				params: 'id = 2',
				success: function(respuesta, request) {
	      			var jsonData = Ext.util.JSON.decode(respuesta.responseText);
	      				if ((jsonData.success ==true) && (jsonData.errorMsj=='')){
	      					evento = "registrar";
	      				}else if((jsonData.success ==true) && (jsonData.errorMsj!='')){
         	        		var datos = jsonData.datos;
         	        		Ext.getCmp('registroPasanteForm').getForm().reset();
     	        			
         	        		Ext.getCmp('dataFecha').setValue(fech);
     	        			Ext.getCmp('txtNombre').setValue(datos.nombre);
     	        			Ext.getCmp('txtApellido').setValue(datos.apellido);
     	        			Ext.getCmp('txtTelefono').setValue(datos.telefono);
     	        			Ext.getCmp('txtCorreo').setValue(datos.email);
     	        			Ext.getCmp('txtRepetirCorreo').setValue(datos.email);
     	        			Ext.getCmp('txtDecanato').setValue(datos.decanato);
     	        			Ext.getCmp('txtIndice').setValue(datos.indiceAcademico);
     	        			Ext.getCmp('txtSemestre').setValue(datos.semestre);
     	        			Ext.getCmp('txtCarrera').setValue(datos.carrera);
     	        			Ext.getCmp('txtDireccion').setValue(datos.direccion);
     	        			Ext.getCmp('cmbEstado').setValue(datos.estado);
     	        			Ext.getCmp('cmbCiudad').setValue(datos.ciudad);
     	        			Ext.getCmp('txtTelefono').setValue(datos.telefono);
     	        			
     	        			if (datos.sexo=='F'){
     	        				Ext.getCmp('opcFemenino').setValue(true);
     	        			} else {
     	        				Ext.getCmp('opcMasculino').setValue(true);
     	        			}
     	        			
	         	        	evento = "actualizar";
         	        		
//         	        		Ext.getCmp('txtDescripcion').focus();       				
	      				}
				}
		});	
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
			                Ext.getCmp('registroPasanteForm').getForm().reset();
			                Ext.getCmp('frmActualizarPasantesWin').close();                                    	                           
			                }
			    	   		});
				   			}  
				  }); 
		} else {
				Ext.MessageBox.show({
					title: "Error",
					msg: "Datos incompletos o no v&aacute;lidos, por favor verifique.",
					width:400,
					buttons: Ext.MessageBox.OK,
					icon: Ext.MessageBox.ERROR
		    });
		}
    },
    
    cancelar:function(){
    	Ext.getCmp('registroPasanteForm').getForm().reset();
    }
});

