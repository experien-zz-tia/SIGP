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

frmActualizarPasantesCoord = Ext.extend(frmActualizarPasantesCoordUi, {
    initComponent: function() {
        frmActualizarPasantesCoord.superclass.initComponent.call(this);
        Ext.getCmp('cmbEstado').on('select',this.cargarCiudades);
        Ext.getCmp('cmbDecanato').on('select',this.buscarCarreras);
        Ext.getCmp('cmbCarrera').on('select',this.cargarSemestres);

        Ext.getCmp('btnGuardar').on('click',this.registrar);
        
        Ext.getCmp('btnAdelantePersonal').on('click', this.habilitar_AdP);
        Ext.getCmp('btnAtrasContacto').on('click', this.habilitar_AC);
        
        this.cargar();
        Ext.getCmp('txtNombre').focus();
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
    	cargarPasante("-");
    },
    registrar:function(){
    	if (Ext.getCmp('actualizacionPasanteForm').getForm().isValid()){
			 Ext.getCmp('actualizacionPasanteForm').getForm().submit(
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
			                msg: 'Actualizaci&oacute;n Exitosa. <BR>Se ha enviado un correo electr&oacute;nico a la direccion ingresada con la notificaci&oacute;n.',  
			                buttons: Ext.MessageBox.OK,  
			                icon: Ext.MessageBox.INFO,
			                
			                fn: function (){
			                Ext.getCmp('ptnPersonal').enable();    	
			        		Ext.getCmp('ptnContacto').disable();
			        		Ext.getCmp('panelPasante').setActiveTab(0);
			        		//cargarPasante();
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
    	Ext.getCmp('actualizacionPasanteForm').getForm().reset();
    }
});

function cargarPasante(vCed){
	Ext.Ajax.request({
		url: '/SIGP/pasante/buscarPasanteExistente',
		method: 'POST',
		params: {id: vCed},
		success: function(respuesta, request) {
  			var jsonData = Ext.util.JSON.decode(respuesta.responseText);
  				if ((jsonData.success ==true) && (jsonData.errorMsj=='')){
  					evento = "registrar";
  				}else if((jsonData.success ==true) && (jsonData.errorMsj!='')){
 	        		var datos = jsonData.datos;
 	        		Ext.getCmp('actualizacionPasanteForm').getForm().reset();
	        			
 	        		Ext.getCmp('dataFecha').setValue(datos.fchNacimiento);
	        			Ext.getCmp('txtNombre').setValue(datos.nombre);
	        			Ext.getCmp('txtApellido').setValue(datos.apellido);
	        			Ext.getCmp('txtTelefono').setValue(datos.telefono);
	        			Ext.getCmp('txtCorreo').setValue(datos.email);
	        			Ext.getCmp('txtRepetirCorreo').setValue(datos.email);
	        			//Ext.getCmp('cmbDecanato').setValue(datos.decanato);
	        			Ext.getCmp('txtIndice').setValue(datos.indiceAcademico);
	        			Ext.getCmp('cmbSemestre').setValue(datos.semestre);
	        			//Ext.getCmp('cmbCarrera').setValue(datos.carrera);
	        			Ext.getCmp('txtDireccion').setValue(datos.direccion);
	        			//Ext.getCmp('cmbEstado').setValue(datos.estado);
	        			//Ext.getCmp('cmbCiudad').setValue(datos.ciudad);
	        			Ext.getCmp('txtTelefono').setValue(datos.telefono);
	        			
	        			if (datos.sexo=='F'){
	        				Ext.getCmp('opcFemenino').setValue(true);
	        			} else {
	        				Ext.getCmp('opcMasculino').setValue(true);
	        			}
	        			var cmbDecanato = Ext.getCmp('cmbDecanato');      					
  					var storeDec = cmbDecanato.getStore();
  					storeDec.load({
  					   callback: function() {
  					      cmbDecanato.setValue(datos.decanato);
  					   }
  					});
  					
  					var cmbCarr = Ext.getCmp('cmbCarrera');                          
                    var storeDpto = cmbCarr.getStore();
                    storeDpto.load({
                       params: {idDecanato: datos.decanato},
                       callback: function() {
                          cmbCarr.setValue(datos.carrera);
                       }
                    });
                    
                    var cmbSem = Ext.getCmp('cmbSemestre');                          
                    var storeSem = cmbSem.getStore();
                    storeSem.load({
                       params: {idCarrera: datos.carrera},
                       callback: function() {
                          cmbSem.setValue(datos.semestre);
                       }
                    });
                    
                    var cmbModalidad = Ext.getCmp('cmbModalidadPasantia');      					
  					var storeMod = cmbModalidad.getStore();
  					storeMod.load({
  					   callback: function() {
  						cmbModalidad.setValue(datos.modalidadPasantia);
  					   }
  					});
  					
  					var cmbTipo = Ext.getCmp('cmbTipoPasantia');      					
  					var storeTipo = cmbTipo.getStore();
  					storeTipo.load({
  					   callback: function() {
  						cmbTipo.setValue(datos.tipoPasantia);
  					   }
  					});
  					

                    var cmbEstado = Ext.getCmp('cmbEstado');      					
  					var storeEst = cmbEstado.getStore();
  					storeEst.load({
  					   callback: function() {
  					      cmbEstado.setValue(datos.estado);
  					   }
  					});
  					
  					var cmbCiud = Ext.getCmp('cmbCiudad');                          
                    var storeCiu = cmbCiud.getStore();
                    storeCiu.load({
                       params: {idEstado: datos.estado},
                       callback: function() {
                          cmbCiud.setValue(datos.ciudad);
                       }
                    });
  					
     	        	evento = "actualizar";
 	        		
// 	        		Ext.getCmp('txtDescripcion').focus();       				
  				}
		}
});	
}

