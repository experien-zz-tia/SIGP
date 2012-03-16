panelGestionCarreras = Ext.extend(panelGestionCarrerasUi, {
	initComponent : function() {
		panelGestionCarreras.superclass.initComponent.call(this);
		Ext.getCmp('btnAgregar').on('click',this.agregar);
        Ext.getCmp('btnModificar').on('click',this.modificar);
        Ext.getCmp('btnEliminar').on('click',this.eliminar);
       Ext.getCmp('gridGestionCarreras').store.reload({params: {idDecanato: '-1'}});
       Ext.getCmp('cmbDecanatoCarr').on('select',
				this.actualizarParametro);
       Ext.getCmp('btnLimpiarFiltro').on('click',this.limpiarFiltro);
	},
	actualizarParametro : function() {
		id = Ext.getCmp('cmbDecanatoCarr').getValue();
		Ext.getCmp('gridGestionCarreras').store.setBaseParam('idDecanato', id);
		Ext.getCmp('gridGestionCarreras').store.load();
	},
	limpiarFiltro : function() {
		Ext.getCmp('cmbDecanatoCarr').reset();
//		Ext.getCmp('gridGestionCarreras').store.reload({params: {idDecanato: '-1'}});
//		Ext.getCmp('gridGestionCarreras').store.setBaseParam('idDecanato', null);
		Ext.getCmp('gridGestionCarreras').store.setBaseParam('idDecanato', null);
		Ext.getCmp('gridGestionCarreras').store.load();
		//stCarreras.load();
	},
    agregar:function(){
    	var frm = new frmCarrera({
        	renderTo: Ext.getBody()
        });
        
    	 frm.show();
    },
     modificar:function(){
    	var grid = Ext.getCmp('gridGestionCarreras');
      	var index = grid.getSelectionModel().getSelected();

          if (!index) {
          	 Ext.MessageBox.show({
      		     title: " Seleccione una fila.",
      		     msg: "Debe seleccionar una fila antes de realizar la operaci&oacute;n.",
      		     width:400,
      		     buttons: Ext.MessageBox.OK,
      		     icon: Ext.MessageBox.WARNIRG
      		    });
          }else{
        	 var id = index.get('id');
        	 var frm = new frmCarrera({
        		renderTo: Ext.getBody()
        	});
        	 Ext.getCmp('txtId').setValue(id);
        	 frm.buscar();
        	 Ext.getCmp('btnActualizar').show();
       	  	 Ext.getCmp('btnRegistrar').hide();
        	 frm.show();
          }
     },
     eliminar:function(){
    	var grid = Ext.getCmp('gridGestionCarreras');
      	var index = grid.getSelectionModel().getSelected();

          if (!index) {
          	 Ext.MessageBox.show({
      		     title: " Seleccione una fila.",
      		     msg: "Debe seleccionar una fila antes de realizar la operaci&oacute;n.",
      		     width:400,
      		     buttons: Ext.MessageBox.OK,
      		     icon: Ext.MessageBox.WARNIRG
      		    });
          } else{
        	 var id = index.get('id');
        	 Ext.Msg.confirm('Confirmaci&oacute;n','&iquest; Est&aacute; seguro de eliminar la Carrera seleccionado?',function(btn){  
         	        if(btn === 'yes'){  
			        	  Ext.Ajax.request({
			      			url: '/SIGP/configuracion/eliminarCarrera',
			      			method: 'POST',
			      			waitMsg : 'Enviando datos...', 
			      			params: {txtId: id},
			      			success: function(respuesta, request) {
			      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
			      				if (jsonData.success == true){
				      				 Ext.MessageBox.show({  
										 title: 'Operaci&oacute;n exitosa',  
										 msg: 'Se ha eliminado la Carrera: '+index.get('nombre'),  
										 buttons: Ext.MessageBox.OK,  
										 icon: Ext.MessageBox.INFO,
									fn: function (){
				      					id = Ext.getCmp('cmbDecanatoCarr').getValue();
					      				if (id != null){
					      					Ext.getCmp('gridGestionCarreras').store.setBaseParam('idDecanato', id);
				      						Ext.getCmp('gridGestionCarreras').store.load();
					      				}  else {
					      					Ext.getCmp('gridGestionCarreras').store.reload({params: {idDecanato: '-1'}});
					      				}
										 }
									 });
			      					
			      				}else{
			      					 Ext.MessageBox.show({  
				      	                title: 'Error.',  
				      	                msg: 'No se ha eliminado la Coordinaci&oacute;n:<BR>'+jsonData.errorMsj,  
				      	                buttons: Ext.MessageBox.OK,  
				      	                icon: Ext.MessageBox.ERROR
				      	               });	
			      				}         				
			      			},
			      			failure: function (respuesta, request) {
			      				Ext.MessageBox.show({
			 	        		     title: "Operaci&oacute;n no realizada.",
			 	        		     msg: "No se ha eliminado la Coordincaci&oacute;n. Intente de nuevo.",
			 	        		     width:400,
			 	        		     buttons: Ext.MessageBox.OK,
			 	        		     icon: Ext.MessageBox.ERROR
			 	        		    });
			      			}
			      		});
          }
         	    });  	
     }
   }
});