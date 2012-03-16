var cmb;
Ext.QuickTips.init(); 
frmCarrera = Ext.extend(frmCarreraUi, {
    initComponent: function() {
        frmCarrera.superclass.initComponent.call(this);
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
		if (Ext.getCmp('frmCarreraForm').getForm().isValid() ){
			cmb = Ext.getCmp('cmbDecanato').getValue();
			 Ext.getCmp('frmCarreraForm').getForm().submit({ waitMsg : 'Enviando datos...',
				 params:{
				 	decanato: Ext.getCmp('cmbDecanato').getValue(),
				 	regimen: Ext.getCmp('cmbRegimen').getValue(),
				 	plan: Ext.getCmp('cmbPlan').getValue(),
				 	duracion: Ext.getCmp('cmbDuracion').getValue()
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
		      					Ext.getCmp('gridGestionCarreras').store.setBaseParam('idDecanato', id);
		      					Ext.getCmp('frmCarreraWin').close();
	      						Ext.getCmp('gridGestionCarreras').store.load();
	      						
	      						var cmbDecanato = Ext.getCmp('cmbDecanatoCarr');      					
	          					var storeDec = cmbDecanato.getStore();
	          					storeDec.load({
	          					   callback: function() {
	          					      cmbDecanato.setValue(id);
	          					   }
	          					});
		      				}  else {
		      					Ext.getCmp('gridGestionCarreras').store.reload({params: {idDecanato: '-1'}});
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
          					
          					var cmbRegimen = Ext.getCmp('cmbRegimen');      					
          					var storeReg = cmbRegimen.getStore();
          					storeReg.load({
          					   callback: function() {
          					      cmbRegimen.setValue(datos.regimen);
          					   }
          					});
          					
          					var cmbPlan = Ext.getCmp('cmbPlan');      					
          					var storeP = cmbPlan.getStore();
          					storeP.load({
          					   callback: function() {
          					      cmbPlan.setValue(datos.plan);
          					   }
          					});
          					
          					var cmbDuracion = Ext.getCmp('cmbDuracion');      					
          					var storeDur = cmbDuracion.getStore();
          					storeDur.load({
          					   callback: function() {
          					      cmbDuracion.setValue(datos.duracion);
          					   }
          					});
          					
	      				}
				}
		});
},
actualizar:function(){
	if (Ext.getCmp('frmCarreraForm').getForm().isValid()){
				 Ext.Ajax.request({
      			url: '/SIGP/configuracion/actualizarCarrera',
      			method: 'POST',
      			params: {
					 txtId: Ext.getCmp('txtId').getValue(),
					 decanato: Ext.getCmp('cmbDecanato').getValue(),
					 regimen: Ext.getCmp('cmbRegimen').getValue(),
					 plan: Ext.getCmp('cmbPlan').getValue(),
					 duracion: Ext.getCmp('cmbDuracion').getValue(),
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
      						id = cmb;
		      				if (id != null){
		      					Ext.getCmp('gridGestionCarreras').store.setBaseParam('idDecanato', id);
		      					Ext.getCmp('frmCarreraWin').close();
		      					
	      						Ext.getCmp('gridGestionCarreras').store.load();
	      						var cmbDecanato = Ext.getCmp('cmbDecanatoCarr');      					
	          					var storeDec = cmbDecanato.getStore();
	          					storeDec.load({
	          					   callback: function() {
	          					      cmbDecanato.setValue(id);
	          					   }
	          					});
		      				}  else {
		      					Ext.getCmp('frmCarreraWin').close();
		      					Ext.getCmp('gridGestionCarreras').store.reload({params: {idDecanato: '-1'}});
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
		//Ext.getCmp('txtNombre').reset();
		Ext.getCmp('frmCarreraForm').getForm().reset();
	},
	salir:function(){
        Ext.getCmp('frmCarreraForm').getForm().reset();
        Ext.getCmp('frmCarreraWin').close();                                    	                           

	}

});

