Ext.QuickTips.init(); 
 frmActualizarCoordinacion = Ext.extend( frmActualizarCoordinacionUi, {
    initComponent: function() {
         frmActualizarCoordinacion.superclass.initComponent.call(this);
		Ext.getCmp('btnActualizar').on('click',this.actualizar);
		Ext.getCmp('btnLimpiar').on('click',this.limpiar);
		Ext.getCmp('btnSalir').on('click',this.salir);
		Ext.getCmp('cmbDecanato').on('select',this.cargarEmpleados);
	    },
	    cargarEmpleados:function(){
	    	Ext.getCmp('cmbEmpleado').clearValue();
	  	  	Ext.getCmp('cmbEmpleado').store.reload({params: {idDecanato: Ext.getCmp('cmbDecanato').getValue()}});
	    },
	buscar:function(){	
		Ext.Ajax.request({
			url: '/SIGP/Coordinacion/buscar',
			method: 'POST',
			params: {id : Ext.getCmp('txtId').getValue()},
			success: function(respuesta, request) {
	      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
	      				if((jsonData.success == true)){
         	        		var datos = jsonData.datos;
         	        		Ext.getCmp('txtNombre').setValue(datos.nombre);
         	        		
         	        		var cmbDecanato = Ext.getCmp('cmbDecanato');      					
          					var storeDec = cmbDecanato.getStore();
          					storeDec.load({
          					   callback: function() {
          					      cmbDecanato.setValue(datos.decanato_id);
          					   }
          					});
          					
          					var cmbEmpl = Ext.getCmp('cmbEmpleado');                          
                            var storeEmpl = cmbEmpl.getStore();
                            storeEmpl.load({
                               params: {idDecanato: datos.decanato_id},
                               callback: function() {
                            	   cmbEmpl.setValue(datos.empleado_id);
                               }
                            });
                            
                            Ext.getCmp('txtDireccion').setValue(datos.direccion);
         	        		Ext.getCmp('txtTelefono').setValue(datos.telefono);
         	        		Ext.getCmp('txtEmail').setValue(datos.email);
	      				}
				}
		});
},
	limpiar:function(){
		Ext.getCmp('txtNombre').reset();
	},
	salir:function(){
        Ext.getCmp('frmActualizarCoordinacionForm').getForm().reset();
        Ext.getCmp('frmActualizarCoordinacionWin').close();                                    	                           

	},
	actualizar:function(){
		if (Ext.getCmp('frmActualizarCoordinacionForm').getForm().isValid()){
					 Ext.Ajax.request({
	      			url: '/SIGP/configuracion/actualizarCoordinacion',
	      			method: 'POST',
	      			params: {
						 txtId: Ext.getCmp('txtId').getValue(),
						 decanato: Ext.getCmp('cmbDecanato').getValue(),
						 empleado: Ext.getCmp('cmbEmpleado').getValue(),
						 txtEmail: Ext.getCmp('txtEmail').getValue(),
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
	      						Ext.getCmp('frmActualizarCoordinacionWin').close();
//	      			     	 	 stCoordinaciones.reload();
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

