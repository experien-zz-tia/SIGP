Ext.QuickTips.init(); 
frmCoordinacion = Ext.extend(frmCoordinacionUi, {
    initComponent: function() {
        frmCoordinacion.superclass.initComponent.call(this);
		Ext.getCmp('btnRegistrar').on('click',this.registrar);
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
			url: '/SIGP/coordinacion/buscar',
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
		if (Ext.getCmp('frmCoordinacionForm').getForm().isValid() ){
			 Ext.getCmp('frmCoordinacionForm').getForm().submit({ waitMsg : 'Enviando datos...',
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
							 Ext.getCmp('frmCoordinacionForm').getForm().reset();
							 Ext.getCmp('frmCoordinacionWin').close();             
							 //stCoordinaciones.reload();
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
	limpiar:function(){
		Ext.getCmp('txtNombre').reset();
	},
	salir:function(){
        Ext.getCmp('frmCoordinacionForm').getForm().reset();
        Ext.getCmp('frmCoordinacionWin').close();                                    	                           

	}

});

