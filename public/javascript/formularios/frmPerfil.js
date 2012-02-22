var evento = "";
frmPerfil = Ext.extend(frmPerfilUi, {
    initComponent: function() {
        frmPerfil.superclass.initComponent.call(this);
        Ext.getCmp('btnCancelar').on('click',this.cancelar);
        Ext.getCmp('btnGuardar').on('click',this.guardarPerfil);
        this.cargar();
    },
    
    cancelar:function(){
    	 Ext.getCmp('frmPerfil').getForm().reset();
    },

	cargar:function(){
			Ext.Ajax.request({
				url: '/SIGP/perfil/buscarPasanteExistente',
				method: 'POST',
				params: 'id = 2',
				success: function(respuesta, request) {
	      			var jsonData = Ext.util.JSON.decode(respuesta.responseText);
	      				if ((jsonData.success ==true) && (jsonData.errorMsj=='')){
	      					evento = "registrar";
	      				}else if((jsonData.success ==true) && (jsonData.errorMsj!='')){
         	        		var datos = jsonData.datos;
         	        		Ext.getCmp('txtDescripcion').setValue(datos.descripcion);
	         	        	Ext.getCmp('txtExperiencia').setValue(datos.experiencia);
	         	        	Ext.getCmp('txtCursos').setValue(datos.cursos);
	         	        	evento = "actualizar";
         	        		
         	        		Ext.getCmp('txtDescripcion').focus();       				
	      				}
				}
		});	
},
    guardarPerfil:function(){	
    			Ext.getCmp('frmPerfil').getForm().submit({
    				waitMsg : 'Enviando datos...', 
                    
    				params:{pPasanteId: '53',
    						tipoevento: evento},
    				
					failure: function (form, action){          	                                 
							Ext.MessageBox.show({  
								title: 'Error',  
								msg: 'Error al guardar perfil.',  
								buttons: Ext.MessageBox.OK,  
								icon: Ext.MessageBox.ERROR  
							});  
						},  
						success: function (form, action){                                              
							Ext.MessageBox.show({  
								title: 'Informaci&oacute;n',  
								msg: 'Registro exitoso.',  
								buttons: Ext.MessageBox.OK,  
								icon: Ext.MessageBox.INFO,
								fn: function (){
									Ext.getCmp('frmPerfil').getForm().reset();
									Ext.getCmp('frmPerfilWin').close();
							}
							});
						}
    			}); 
    }
});
