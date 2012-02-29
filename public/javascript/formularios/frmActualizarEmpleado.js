Ext.QuickTips.init(); 
frmActualizarEmpleado = Ext.extend(frmActualizarEmpleadoUi, {
    initComponent: function() {
        frmActualizarEmpleado.superclass.initComponent.call(this);
		Ext.getCmp('btnActualizar').on('click',this.actualizar);
		Ext.getCmp('btnLimpiar').on('click',this.limpiar);
		Ext.getCmp('btnSalir').on('click',this.salir);
		this.buscar();
	    },
	    
	buscar:function(){
	var cedula = Ext.getCmp('txtCedula');
		Ext.Ajax.request({
			url: '/SIGP/empleado/buscarExistente',
			method: 'POST',
			success: function(respuesta, request) {
	      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
	      				if ((jsonData.success ==true) && (jsonData.errorMsj=='')){
	      				}else if((jsonData.success ==true) && (jsonData.errorMsj!='')){
         	        				var datos = jsonData.datos;
	         	        			Ext.getCmp('txtNombre').setValue(datos.nombre);
	         	        			Ext.getCmp('txtApellido').setValue(datos.apellido);
	         	        			Ext.getCmp('txtCorreo').setValue(datos.email);
	         	        			Ext.getCmp('txtCorreoRepetir').setValue(datos.email);
	         	        			if (jsonData.datos.tipo == 'A') {
										Ext.getCmp('radioTipo').setValue('radioA', true);
									} else if (jsonData.tipo == 'C') {
										Ext.getCmp('radioTipo').setValue('radioC', true);
									}else{
										Ext.getCmp('radioTipo').setValue('radioS', true);
									}
	         	        			Ext.getCmp('radioTipo').disable();
	         	        			Ext.getCmp('txtCedula').disable();
	         	        			Ext.getCmp('txtIdEmpleado').setValue(datos.id);
	         	        			Ext.getCmp('txtCedula').setValue(datos.cedula);
	         	        			habilitarCampos(true);
         	        				Ext.getCmp('txtNombre').focus();
	         	        			
         	        		 }}});       		
	},
	limpiar:function(){
		Ext.getCmp('txtCedula').enable();
		Ext.getCmp('txtCedula').reset();
		Ext.getCmp('txtNombre').reset();
		Ext.getCmp('txtApellido').reset();
		Ext.getCmp('txtCorreo').reset();
		Ext.getCmp('txtCorreoRepetir').reset();
     	Ext.getCmp('radioTipo').reset();
	},
	salir:function(){
        Ext.getCmp('frmActualizarEmpleadoForm').getForm().reset();
        Ext.getCmp('frmActualizarEmpleadoWin').hide();                                    	                           

	},
	actualizar:function(){
		if (Ext.getCmp('frmActualizarEmpleadoForm').getForm().isValid()){
					 Ext.Ajax.request({
	      			url: '/SIGP/empleado/actualizarEmpleados',
	      			method: 'POST',
	      			params: {
							txtCedula: Ext.getCmp('txtCedula').getValue(),
							txtNombre:	Ext.getCmp('txtNombre').getValue(),
							txtApellido: Ext.getCmp('txtApellido').getValue(),
							txtCorreo: Ext.getCmp('txtCorreo').getValue(),
	      					txtIdEmpleado: Ext.getCmp('txtIdEmpleado').getValue()
	      			},
	      			success: function(respuesta, request) {
	      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
	      				if ((jsonData.success ==true)){
	      					 Ext.MessageBox.show({  
	      			           title: 'Informaci&oacute;n',  
	      			           msg: 'Actualizaci&oacute;n exitosa.',  
	      			           buttons: Ext.MessageBox.OK,  
	      			           icon: Ext.MessageBox.INFO,
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

 function habilitarCampos(flag){
	if (flag==true){
		Ext.getCmp('txtNombre').enable();
		Ext.getCmp('txtApellido').enable();
		Ext.getCmp('txtCorreo').enable();
		Ext.getCmp('txtCorreoRepetir').enable();
     	Ext.getCmp('radioTipo').enable();
	}else{
		Ext.getCmp('txtNombre').disable();
		Ext.getCmp('txtApellido').disable();
		Ext.getCmp('txtCorreo').disable();
		Ext.getCmp('txtCorreoRepetir').disable();
     	Ext.getCmp('radioTipo').disable();
		
	}
	
}
