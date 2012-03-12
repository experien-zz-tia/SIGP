Ext.QuickTips.init(); 
frmCarrera = Ext.extend(frmCarreraUi, {
    initComponent: function() {
        frmCarrera.superclass.initComponent.call(this);
		Ext.getCmp('btnRegistrar').on('click',this.registrar);
		Ext.getCmp('btnLimpiar').on('click',this.limpiar);
		Ext.getCmp('btnSalir').on('click',this.salir);
	},
	buscar:function(){	
		Ext.Ajax.request({
			url: '/SIGP/carrera/buscar',
			method: 'POST',
			params: { },
			success: function(respuesta, request) {
	      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
	      				if ((jsonData.success ==true) && (jsonData.errorMsj=='')){
	      					
	      				}else if((jsonData.success ==true) && (jsonData.errorMsj!='')){
         	        		var datos = jsonData.datos;
         	        		Ext.getCmp('txtNombre').setValue(datos.nombre);
	      				}
				}
		});
},
	registrar:function(){
		// Se verifica que los campos marcados como obligatorios
		// (allowBlank:false) esten llenos
		if (Ext.getCmp('frmCarreraForm').getForm().isValid() ){
			 Ext.getCmp('frmCarreraForm').getForm().submit({ waitMsg : 'Enviando datos...',
				 params:{
				 	decanato: Ext.getCmp('cmbDecanato').getValue(),
				 	empleado: Ext.getCmp('cmbEmpleado').getValue()
			  			},
				 failure: function (form, action){
					 Ext.MessageBox.show({  
						 title: 'Error',  
						 msg: 'Error al registrar',  
						 buttons: Ext.MessageBox.OK,  
						 icon: Ext.MessageBox.ERROR  
						 });  
					 },
				  success: function (form, request){   
						 Ext.MessageBox.show({  
							 title: 'Informaci&oacute;n',  
							 msg: 'Registro exitoso',  
							 buttons: Ext.MessageBox.OK,  
							 icon: Ext.MessageBox.INFO,
						fn: function (){
							 Ext.getCmp('frmCarreraForm').getForm().reset();
							 Ext.getCmp('frmCarreraWin').close();             
							 //stCarreraes.reload();
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
	}},
	buscar:function(){	
		Ext.Ajax.request({
			url: '/SIGP/carrera/buscar',
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
actualizar:function(){
	if (Ext.getCmp('frmActualizarCarreraForm').getForm().isValid()){
				 Ext.Ajax.request({
      			url: '/SIGP/configuracion/actualizarCarrera',
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
      						Ext.getCmp('frmActualizarCarreraWin').close();
//      			     	 	 stCarrera.reload();
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
},
	limpiar:function(){
		Ext.getCmp('txtNombre').reset();
	},
	salir:function(){
        Ext.getCmp('frmCarreraForm').getForm().reset();
        Ext.getCmp('frmCarreraWin').close();                                    	                           

	}

});

