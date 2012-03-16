var cmb;
Ext.QuickTips.init(); 
frmDepartamento = Ext.extend(frmDepartamentoUi, {
    initComponent: function() {
        frmDepartamento.superclass.initComponent.call(this);
		Ext.getCmp('btnRegistrar').on('click',this.registrar);
		Ext.getCmp('cmbDecanato').on('select',this.actualizarVar);
		Ext.getCmp('btnActualizar').on('click',this.actualizar);
		Ext.getCmp('btnLimpiar').on('click',this.limpiar);
		Ext.getCmp('btnSalir').on('click',this.salir);
	},
	actualizarVar:function(){
		cmb = Ext.getCmp('cmbDecanato').getValue();
	},
	registrar:function(){
		// Se verifica que los campos marcados como obligatorios
		// (allowBlank:false) esten llenos
		if (Ext.getCmp('frmDepartamentoForm').getForm().isValid() ){
			cmb = Ext.getCmp('cmbDecanato').getValue();
			 Ext.getCmp('frmDepartamentoForm').getForm().submit({ waitMsg : 'Enviando datos...',
				 params:{
				 	decanato: Ext.getCmp('cmbDecanato').getValue()
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
      						id = cmb;
		      				if (id != null){
		      					Ext.getCmp('gridGestionDepartamentos').store.setBaseParam('idDecanato', id);
		      					Ext.getCmp('frmDepartamentoWin').close();
	      						Ext.getCmp('gridGestionDepartamentos').store.load();
	      						
	      						var cmbDecanato = Ext.getCmp('cmbDecanatoDep');      					
	          					var storeDec = cmbDecanato.getStore();
	          					storeDec.load({
	          					   callback: function() {
	          					      cmbDecanato.setValue(id);
	          					   }
	          					});
		      				}  else {
		      					Ext.getCmp('frmDepartamentoWin').close();
		      					Ext.getCmp('gridGestionDepartamentos').store.reload({params: {idDecanato: '-1'}});
		      				}
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
			url: '/SIGP/departamento/buscar',
			method: 'POST',
			params: {id : Ext.getCmp('txtId').getValue()},
			success: function(respuesta, request) {
	      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
	      				if((jsonData.success == true)){
         	        		var datos = jsonData.datos;
         	        		Ext.getCmp('txtDescripcion').setValue(datos.descripcion);
         	        		
         	        		var cmbDecanato = Ext.getCmp('cmbDecanato');      					
          					var storeDec = cmbDecanato.getStore();
          					storeDec.load({
          					   callback: function() {
          					      cmbDecanato.setValue(datos.decanato_id);
          					   }
          					});
	      				}
				}
		});
},
actualizar:function(){
	if (Ext.getCmp('frmDepartamentoForm').getForm().isValid()){
				 Ext.Ajax.request({
      			url: '/SIGP/configuracion/actualizarDepartamento',
      			method: 'POST',
      			params: {
					 txtId: Ext.getCmp('txtId').getValue(),
					 decanato: Ext.getCmp('cmbDecanato').getValue(),
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
      						id = cmb;
		      				if (id != null){
		      					Ext.getCmp('gridGestionDepartamentos').store.setBaseParam('idDecanato', id);
		      					Ext.getCmp('frmDepartamentoWin').close();
		      					
	      						Ext.getCmp('gridGestionDepartamentos').store.load();
	      						var cmbDecanato = Ext.getCmp('cmbDecanatoDep');      					
	          					var storeDec = cmbDecanato.getStore();
	          					storeDec.load({
	          					   callback: function() {
	          					      cmbDecanato.setValue(id);
	          					   }
	          					});
		      				}  else {
		      					Ext.getCmp('frmDepartamentoWin').close();
		      					Ext.getCmp('gridGestionDepartamentos').store.reload({params: {idDecanato: '-1'}});
		      				}
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
		Ext.getCmp('frmDepartamentoForm').getForm().reset();
	},
	salir:function(){
        Ext.getCmp('frmDepartamentoForm').getForm().reset();
        Ext.getCmp('frmDepartamentoWin').close();                                    	                           

	}

});

