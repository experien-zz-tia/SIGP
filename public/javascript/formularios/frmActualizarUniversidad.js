Ext.QuickTips.init(); 
 frmActualizarUniversidad = Ext.extend( frmActualizarUniversidadUi, {
    initComponent: function() {
         frmActualizarUniversidad.superclass.initComponent.call(this);
		Ext.getCmp('btnActualizar').on('click',this.actualizar);
		Ext.getCmp('btnLimpiar').on('click',this.limpiar);
		Ext.getCmp('btnSalir').on('click',this.salir);
		Ext.getCmp('cmbEstado').on('select',this.cargarCiudades);
	    },
	cargarCiudades:function(){
	    	Ext.getCmp('cmbCiudad').clearValue();
	  	  	Ext.getCmp('cmbCiudad').store.reload({params: {idEstado: Ext.getCmp('cmbEstado').getValue()}});
	    },
	buscar:function(){	
		Ext.Ajax.request({
			url: '/SIGP/universidad/buscar',
			method: 'POST',
			params: {id : Ext.getCmp('txtId').getValue()},
			success: function(respuesta, request) {
	      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
	      				if((jsonData.success == true)){
         	        		var datos = jsonData.datos;
         	        		Ext.getCmp('txtNombre').setValue(datos.nombre);
         	        		
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
        Ext.getCmp('frmActualizarUniversidadForm').getForm().reset();
        Ext.getCmp('frmActualizarUniversidadWin').close();                                    	                           

	},
	actualizar:function(){
		if (Ext.getCmp('frmActualizarUniversidadForm').getForm().isValid()){
					 Ext.Ajax.request({
	      			url: '/SIGP/configuracion/actualizarUniversidad',
	      			method: 'POST',
	      			params: {
						 txtId: Ext.getCmp('txtId').getValue(),
						 estado: Ext.getCmp('cmbEstado').getValue(),
						 ciudad: Ext.getCmp('cmbCiudad').getValue(),
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
	      						Ext.getCmp('frmActualizarUniversidadWin').close();
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

