Ext.QuickTips.init(); 
 frmActualizarDecanato = Ext.extend( frmActualizarDecanatoUi, {
    initComponent: function() {
         frmActualizarDecanato.superclass.initComponent.call(this);
		Ext.getCmp('btnActualizar').on('click',this.actualizar);
		Ext.getCmp('btnLimpiar').on('click',this.limpiar);
		Ext.getCmp('btnSalir').on('click',this.salir);
		Ext.getCmp('cmbEstado').on('select',this.cargarCiudades);
		//Ext.getCmp('txtCedula').on('blur',this.buscar);
		
	    },
	cargarCiudades:function(){
	    	Ext.getCmp('cmbCiudad').clearValue();
	  	  	Ext.getCmp('cmbCiudad').store.reload({params: {idEstado: Ext.getCmp('cmbEstado').getValue()}});
	    },
	buscar:function(){	
		Ext.Ajax.request({
			url: '/SIGP/decanato/buscar',
			method: 'POST',
			params: {id : Ext.getCmp('txtId').getValue()},
			success: function(respuesta, request) {
	      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
	      				if((jsonData.success == true)){
         	        		var datos = jsonData.datos;
         	        		Ext.getCmp('txtNombre').setValue(datos.nombre);
         	        		
         	        		var cmbUniversidad = Ext.getCmp('cmbUniversidad');      					
          					var storeEst = cmbUniversidad.getStore();
          					storeEst.load({
          					   callback: function() {
          						cmbUniversidad.setValue(datos.universidad_id);
          					   }
          					});
          					
         	        		var cmbEstado = Ext.getCmp('cmbEstado');      					
          					var storeEst = cmbEstado.getStore();
          					storeEst.load({
          					   callback: function() {
          					      cmbEstado.setValue(datos.estado_id);
          					   }
          					});
          					
          					var cmbCiud = Ext.getCmp('cmbCiudad');                          
                            var storeCiu = cmbCiud.getStore();
                            storeCiu.load({
                               params: {idEstado: datos.estado_id},
                               callback: function() {
                                  cmbCiud.setValue(datos.ciudad_id);
                               }
                            });
                            
                            Ext.getCmp('txtDireccion').setValue(datos.direccion);
         	        		Ext.getCmp('txtTelefono').setValue(datos.telefono);
	      				}
				}
		});
},
	limpiar:function(){
		Ext.getCmp('txtNombre').reset();
	},
	salir:function(){
        Ext.getCmp('frmActualizarDecanatoForm').getForm().reset();
        Ext.getCmp('frmActualizarDecanatoWin').close();                                    	                           

	},
	actualizar:function(){
		if (Ext.getCmp('frmActualizarDecanatoForm').getForm().isValid()){
					 Ext.Ajax.request({
	      			url: '/SIGP/configuracion/actualizarDecanato',
	      			method: 'POST',
	      			params: {
						 txtId: Ext.getCmp('txtId').getValue(),
						 estado: Ext.getCmp('cmbEstado').getValue(),
						 ciudad: Ext.getCmp('cmbCiudad').getValue(),
						 universidad: Ext.getCmp('cmbUniversidad').getValue(),
						 txtLogo: Ext.getCmp('txtLogo').getValue(),
						 txtTelefono: Ext.getCmp('txtTelefono').getValue(),
						 txtDireccion: Ext.getCmp('txtDireccion').getValue(),
						 txtNombre: Ext.getCmp('txtNombre').getValue()
	      						},
	      			success: function(respuesta, request) {
	      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
	      				if ((jsonData.success ==true)){
	      					 Ext.MessageBox.show({  
	      			           title: 'Informaci&oacute;n',  
	      			           msg: 'Actualizaci&oacute;n exitosa',  
	      			           buttons: Ext.MessageBox.OK,  
	      			           icon: Ext.MessageBox.INFO,
	      			           fn: function (){
	      						Ext.getCmp('frmActualizarDecanatoWin').close();
//	      			     	 	 stDecanatos.reload();
	      			        	}
	      			          });
	      				}else{
	      				  Ext.MessageBox.show({  
	      	                title: 'Actualizaci&oacute;n no completada.',  
	      	                msg: 'No se  actualizaron los  campos.',  
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

