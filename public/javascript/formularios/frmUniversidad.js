Ext.QuickTips.init(); 
frmUniversidad = Ext.extend(frmUniversidadUi, {
    initComponent: function() {
        frmUniversidad.superclass.initComponent.call(this);
		Ext.getCmp('btnRegistrar').on('click',this.registrar);
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
			url: '/SIGP/Universidad/buscar',
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
		if (Ext.getCmp('frmUniversidadForm').getForm().isValid() ){
			 Ext.getCmp('frmUniversidadForm').getForm().submit({ waitMsg : 'Enviando datos...',
				 params:{estado:Ext.getCmp('cmbEstado').getValue(),
			  			ciudad:Ext.getCmp('cmbCiudad').getValue()
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
							 Ext.getCmp('frmUniversidadForm').getForm().reset();
							 Ext.getCmp('frmUniversidadWin').close();             
							 //stUniversidad.reload();
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
        Ext.getCmp('frmUniversidadForm').getForm().reset();
        Ext.getCmp('frmUniversidadWin').close();                                    	                           

	}

});

