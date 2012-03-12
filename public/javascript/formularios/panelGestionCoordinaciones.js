panelGestionCoordinaciones = Ext.extend(panelGestionCoordinacionesUi, {
	initComponent : function() {
		panelGestionCoordinaciones.superclass.initComponent.call(this);
		Ext.getCmp('btnAgregar').on('click',this.agregar);
        Ext.getCmp('btnModificar').on('click',this.modificar);
        Ext.getCmp('btnEliminar').on('click',this.eliminar);
        Ext.getCmp('gridGestionCoordinaciones').store.reload();
	}
	,
    agregar:function(){
    	var frm = new frmCoordinacion({
        	renderTo: Ext.getBody()
        });
        
    	 frm.show();
    },
     modificar:function(){
    	var grid = Ext.getCmp('gridGestionCoordinaciones');
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
        	 var frm = new frmActualizarCoordinacion({
        		renderTo: Ext.getBody()
        	});
        	 Ext.getCmp('txtId').setValue(id);
        	 frm.buscar();
        	 frm.show();
          }
     },
     eliminar:function(){
    	var grid = Ext.getCmp('gridGestionCoordinaciones');
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
        	 Ext.Msg.confirm('Confirmaci&oacute;n','&iquest; Est&aacute; seguro de eliminar la Coordinacion seleccionado?',function(btn){  
         	        if(btn === 'yes'){  
			        	  Ext.Ajax.request({
			      			url: '/SIGP/configuracion/eliminarCoordinacion',
			      			method: 'POST',
			      			waitMsg : 'Enviando datos...', 
			      			params: {txtId: id},
			      			success: function(respuesta, request) {
			      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
			      				if (jsonData.success == true){
//			      					stCoordinaciones.reload();
				      				Ext.Msg.alert('Operaci&oacute;n exitosa','Se ha eliminado la Coordinacion: '+index.get('nombre'));
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