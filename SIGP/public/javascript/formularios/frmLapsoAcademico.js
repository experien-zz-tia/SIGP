frmLapsoAcademico = Ext.extend(frmLapsoAcademicoUi, {
    initComponent: function() {
        frmLapsoAcademico.superclass.initComponent.call(this);
        Ext.getCmp('btnLimpiar').on('click',this.limpiar);
        Ext.getCmp('btnSalir').on('click',this.salir);
        Ext.getCmp('btnGuardar').on('click',this.grabarLapso);
        Ext.getCmp('dateFechaInicio').on('select',this.solicitarLapso);
        Ext.getCmp('btnActualizar').on('click',this.actualizarLapso);
    },
     salir:function(){
    	 Ext.getCmp('formLapsoAcademico').getForm().reset();
    	 Ext.getCmp('frmLapsoAcademicoWin').close();    
    },
    limpiar:function(){
    	if ( !Ext.getCmp('btnActualizar').isVisible()){
    		Ext.getCmp('txtLapso').reset();
    	}
		Ext.getCmp('dateFechaFin').reset();
		Ext.getCmp('dateFechaInicio').reset();
    },
     solicitarLapso:function(){
     	if ( !Ext.getCmp('btnActualizar').isVisible()){
    	 Ext.Ajax.request({
	      			url: '/SIGP/lapsoAcademico/solicitarLapso',
	      			method: 'POST',
	      			params: {dateFechaInicio:Ext.getCmp('dateFechaInicio').getRawValue()
	      				},
	      			success: function(respuesta, request) {
	      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
	      				if ((jsonData.success ==true)){
	      					Ext.getCmp('txtLapso').setValue(jsonData.lapso);
	      				}else{
	      				  Ext.MessageBox.show({  
	      	                title: 'Error.',  
	      	                msg: 'No se puede generar el identificador del lapso.<BR>Por favor, intente de nuevo.',  
	      	                buttons: Ext.MessageBox.OK,  
	      	                icon: Ext.MessageBox.ERROR
	      	               });	
	      				}         				
	      			},
	      			failure: function ( respuesta, request) {
	      				Ext.MessageBox.show({
	 	        		     title: "Operaci&oacute;n no realizada.",
	 	        		     msg: "No se ha podido generar el identidicador. Intente de nuevo.",
	 	        		     width:400,
	 	        		     buttons: Ext.MessageBox.OK,
	 	        		     icon: Ext.MessageBox.ERROR
	 	        		    });
	      			}
	      		});
    	}
    },
       
     grabarLapso:function(){
    // Se verifica que los campos marcados como obligatorios
	// (allowBlank:false) esten llenos
	if (Ext.getCmp('formLapsoAcademico').getForm().isValid()){
	        Ext.getCmp('formLapsoAcademico').getForm().submit({ waitMsg : 'Enviando datos...', 
	        												params: {txtLapso:Ext.getCmp('txtLapso').getValue()},
			                                                failure: function (form, action){
                                  	                                   Ext.MessageBox.show({  
		                                    	                        title: 'Error',  
		                                    	                        msg: 'Error al registrar.',  
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
				                                    	                   		  Ext.getCmp('formLapsoAcademico').getForm().reset();
    	 																		  Ext.getCmp('frmLapsoAcademicoWin').close();     
				                                    	                    	  stLapsosAcademicos.reload();
				                                    	                       	}
				                                      	                       });
		                                    	                     }  //End Success
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
},
actualizarLapso:function(){
	if (Ext.getCmp('formLapsoAcademico').getForm().isValid()){
	     Ext.Ajax.request({
	      			url: '/SIGP/lapsoAcademico/modificar',
	      			method: 'POST',
	      			params: {txtIdLapso:Ext.getCmp('txtIdLapso').getValue(),
	  					dateFechaFin:Ext.getCmp('dateFechaFin').getRawValue(),
	  					dateFechaInicio:Ext.getCmp('dateFechaInicio').getRawValue()
	      				},
	      			success: function(respuesta, request) {
	      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
	      				if ((jsonData.success ==true) && (jsonData.errorMsj=='')){
	      					 Ext.MessageBox.show({  
	      			           title: 'Informaci&oacute;n',  
	      			           msg: 'Registro exitoso.',  
	      			           buttons: Ext.MessageBox.OK,  
	      			           icon: Ext.MessageBox.INFO,
	      			           fn: function (){
		      			     	  stLapsosAcademicos.reload();
	      			        	}
	      			          });
	      				}else{
	      				  Ext.MessageBox.show({  
	      	                title: 'Actualizaci&oacute;n no completada.',  
	      	                msg: 'Se han presentado los siguientes problemas: <BR>'+jsonData.errorMsj+'<BR>Por favor, verifique.',  
	      	                buttons: Ext.MessageBox.OK,  
	      	                icon: Ext.MessageBox.ERROR
	      	               });	
	      				}         				
	      			},
	      			failure: function ( respuesta, request) {
	      				Ext.MessageBox.show({
	 	        		     title: "Operaci&oacute;n no realizada.",
	 	        		     msg: "No se ha podido actualizar. Intente de nuevo.",
	 	        		     width:400,
	 	        		     buttons: Ext.MessageBox.OK,
	 	        		     icon: Ext.MessageBox.ERROR
	 	        		    });
	      			}
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

}
});
