var cmb;
Ext.QuickTips.init(); 
frmEstado = Ext.extend(frmEstadoUi, {
    initComponent: function() {
        frmEstado.superclass.initComponent.call(this);
		Ext.getCmp('btnRegistrar').on('click',this.registrar);
		Ext.getCmp('btnActualizar').on('click',this.actualizar);
		Ext.getCmp('btnLimpiar').on('click',this.limpiar);
		Ext.getCmp('btnSalir').on('click',this.salir);
	},
	registrar:function(){
		// Se verifica que los campos marcados como obligatorios
		// (allowBlank:false) esten llenos
		if (Ext.getCmp('frmEstadoForm').getForm().isValid() ){
			 Ext.getCmp('frmEstadoForm').getForm().submit({ waitMsg : 'Enviando datos...',
				 params:{ },
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
		      					Ext.getCmp('frmEstadoWin').close();
		      					Ext.getCmp('gridGestionEstados').store.reload();
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
			url: '/SIGP/Estado/buscar',
			method: 'POST',
			params: {id : Ext.getCmp('txtId').getValue()},
			success: function(respuesta, request) {
	      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
	      				if((jsonData.success == true)){
         	        		var datos = jsonData.datos;
         	        		Ext.getCmp('txtDescripcion').setValue(datos.nombre);
	      				}
				}
		});
},
actualizar:function(){
	if (Ext.getCmp('frmEstadoForm').getForm().isValid()){
				 Ext.Ajax.request({
      			url: '/SIGP/configuracion/actualizarEstado',
      			method: 'POST',
      			params: {
					 txtId: Ext.getCmp('txtId').getValue(),
					 txtDescripcion: Ext.getCmp('txtDescripcion').getValue()
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
		      					Ext.getCmp('frmEstadoWin').close();
		      					Ext.getCmp('gridGestionEstados').store.reload();
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
		//Ext.getCmp('txtDescripcion').reset();
		Ext.getCmp('frmEstadoForm').getForm().reset();
	},
	salir:function(){
        Ext.getCmp('frmEstadoForm').getForm().reset();
        Ext.getCmp('frmEstadoWin').close();                                    	                           

	}

});

