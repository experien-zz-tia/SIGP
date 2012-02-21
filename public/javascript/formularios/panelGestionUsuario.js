panelGestionUsuario = Ext.extend(panelGestionUsuarioUi, {
	initComponent : function() {
		panelGestionUsuario.superclass.initComponent.call(this);
		Ext.getCmp('btnReset').on('click',this.reasignarClave);
        Ext.getCmp('btnEliminar').on('click',this.eliminar);
        Ext.getCmp('btnReactivar').on('click',this.reactivar);
	}
	,
     reasignarClave:function(){
    	var grid = Ext.getCmp('gridGestionUsuario');
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
        
        	 	 Ext.Msg.confirm('Confirmaci&oacute;n','&iquest; Est&aacute; seguro de reasignar la clave del usuario seleccionado?',function(btn){  
         	        if(btn === 'yes'){  
			        	  Ext.Ajax.request({
			      			url: '/SIGP/usuario/generarNuevaClave',
			      			method: 'POST',
			      			params: 'pUsuarioId=' + index.get('usuarioId'),
			      			success: function(respuesta, request) {
			      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
			      				if (jsonData.success == true){
			      					stUsuarios.reload();
				      				Ext.Msg.alert('Operaci&oacute;n exitosa','Se ha asignado la clave: '+jsonData.resultado+' al usuario : '+index.get('nombreUsuario'));
			      				}else{
			      					 Ext.MessageBox.show({  
				      	                title: 'Error.',  
				      	                msg: 'No se ha reasignado la clave:<BR>'+jsonData.errorMsj,  
				      	                buttons: Ext.MessageBox.OK,  
				      	                icon: Ext.MessageBox.ERROR
				      	               });	
			      				}         				
			      			},
			      			failure: function ( respuesta, request) {
			      				Ext.MessageBox.show({
			 	        		     title: "Operaci&oacute;n no realizada.",
			 	        		     msg: "No se ha reasignado la clave. Intente de nuevo.",
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
     , eliminar:function(){
    	var grid = Ext.getCmp('gridGestionUsuario');
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
        	 Ext.Msg.confirm('Confirmaci&oacute;n','&iquest; Est&aacute; seguro de eliminar el usuario seleccionado?',function(btn){  
         	        if(btn === 'yes'){  
			        	  Ext.Ajax.request({
			      			url: '/SIGP/usuario/eliminar',
			      			method: 'POST',
			      			params: 'pUsuarioId=' + index.get('usuarioId'),
			      			success: function(respuesta, request) {
			      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
			      				if (jsonData.success == true){
			      					stUsuarios.reload();
				      				Ext.Msg.alert('Operaci&oacute;n exitosa','Se ha eliminado el usuario: '+index.get('nombreUsuario'));
			      				}else{
			      					 Ext.MessageBox.show({  
				      	                title: 'Error.',  
				      	                msg: 'No se ha eliminado el usuario:<BR>'+jsonData.errorMsj,  
				      	                buttons: Ext.MessageBox.OK,  
				      	                icon: Ext.MessageBox.ERROR
				      	               });	
			      				}         				
			      			},
			      			failure: function ( respuesta, request) {
			      				Ext.MessageBox.show({
			 	        		     title: "Operaci&oacute;n no realizada.",
			 	        		     msg: "No se ha eliminado el usuario. Intente de nuevo.",
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
   
    , reactivar:function(){
    	var grid = Ext.getCmp('gridGestionUsuario');
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
        	 Ext.Msg.confirm('Confirmaci&oacute;n','&iquest; Est&aacute; seguro de reactivar el usuario seleccionado?',function(btn){  
         	        if(btn === 'yes'){  
			        	  Ext.Ajax.request({
			      			url: '/SIGP/usuario/reactivar',
			      			method: 'POST',
			      			params: 'pUsuarioId=' + index.get('usuarioId'),
			      			success: function(respuesta, request) {
			      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
			      				if (jsonData.success == true){
			      					stUsuarios.reload();
				      				Ext.Msg.alert('Operaci&oacute;n exitosa','Se ha reactivado el usuario: '+index.get('nombreUsuario'));
			      				}else{
			      					 Ext.MessageBox.show({  
				      	                title: 'Error.',  
				      	                msg: 'No se ha reactivado el usuario:<BR>'+jsonData.errorMsj,  
				      	                buttons: Ext.MessageBox.OK,  
				      	                icon: Ext.MessageBox.ERROR
				      	               });	
			      				}         				
			      			},
			      			failure: function ( respuesta, request) {
			      				Ext.MessageBox.show({
			 	        		     title: "Operaci&oacute;n no realizada.",
			 	        		     msg: "No se ha reactivado el usuario. Intente de nuevo.",
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