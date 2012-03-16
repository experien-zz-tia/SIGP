panelGestionEstados = Ext.extend(panelGestionEstadosUi, {
	initComponent : function() {
		panelGestionEstados.superclass.initComponent.call(this);
		Ext.getCmp('btnAgregar').on('click',this.agregar);
        Ext.getCmp('btnModificar').on('click',this.modificar);
        Ext.getCmp('btnEliminar').on('click',this.eliminar);
       Ext.getCmp('gridGestionEstados').store.reload();
	},
    agregar:function(){
    	var frm = new frmEstado({
        	renderTo: Ext.getBody()
        });
        
    	 frm.show();
    },
     modificar:function(){
    	var grid = Ext.getCmp('gridGestionEstados');
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
        	 var frm = new frmEstado({
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
    	var grid = Ext.getCmp('gridGestionEstados');
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
        	 Ext.Msg.confirm('Confirmaci&oacute;n','&iquest; Est&aacute; seguro de eliminar el Estado de Venezuela seleccionado?',function(btn){  
         	        if(btn === 'yes'){  
			        	  Ext.Ajax.request({
			      			url: '/SIGP/configuracion/eliminarEstado',
			      			method: 'POST',
			      			waitMsg : 'Enviando datos...', 
			      			params: {txtId: id},
			      			success: function(respuesta, request) {
			      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
			      				if (jsonData.success == true){
				      				 Ext.MessageBox.show({  
										 title: 'Operaci&oacute;n exitosa',  
										 msg: 'Se ha eliminado el Estado de Venezuela: '+index.get('nombre'),  
										 buttons: Ext.MessageBox.OK,  
										 icon: Ext.MessageBox.INFO,
										 fn: function (){
				      					 	Ext.getCmp('gridGestionEstados').store.reload();
										 }
									 });
			      					
			      				}else{
			      					 Ext.MessageBox.show({  
				      	                title: 'Error.',  
				      	                msg: 'No se ha eliminado el Estado de Venezuela:<BR>'+jsonData.errorMsj,  
				      	                buttons: Ext.MessageBox.OK,  
				      	                icon: Ext.MessageBox.ERROR
				      	               });	
			      				}         				
			      			},
			      			failure: function (respuesta, request) {
			      				Ext.MessageBox.show({
			 	        		     title: "Operaci&oacute;n no realizada.",
			 	        		     msg: "No se ha eliminado el Estado de Venezuela. Intente de nuevo.",
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