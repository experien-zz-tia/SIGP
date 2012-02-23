panelGestionEmpleados = Ext.extend(panelGestionEmpleadosUi, {
	initComponent : function() {
		panelGestionEmpleados.superclass.initComponent.call(this);
		Ext.getCmp('btnAgregar').on('click',this.agregar);
        Ext.getCmp('btnModificar').on('click',this.modificar);
        Ext.getCmp('btnEliminar').on('click',this.eliminar);
	}
	,
    agregar:function(){
    	var frm = new frmEmpleado({
        	renderTo: Ext.getBody()
        });
        
    	 frm.show();
    },
     modificar:function(){
    	var grid = Ext.getCmp('gridGestionEmpleados');
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
        	 var frm = new frmEmpleado({
        		renderTo: Ext.getBody()
        	});
        	  Ext.getCmp('txtCedula').setValue(index.get('cedula'));
        	  Ext.getCmp('txtNombre').setValue(index.get('nombre'));
        	  Ext.getCmp('txtApellido').setValue(index.get('apellido'));
        	  Ext.getCmp('txtCorreo').setValue(index.get('correo'));
        	  Ext.getCmp('txtCorreoRepetir').setValue(index.get('correo'));
        	  Ext.getCmp('txtIdEmpleado').setValue(index.get('empleadoId'));
        	  switch(index.get('tipo')){
			  	case 'Administrador':
				  Ext.getCmp('radioTipo').setValue('radioA', true);
				  break;
				case 'Coordinador':
				 Ext.getCmp('radioTipo').setValue('radioC', true);
				  break;
				default:
				  Ext.getCmp('radioTipo').setValue('radioS', true);
				
				}
      		  habilitarCampos(true);
      		  Ext.getCmp('txtCedula').disable();
      		  Ext.getCmp('radioTipo').disable();
        	  Ext.getCmp('btnActualizar').show();
        	  Ext.getCmp('btnRegistrar').hide();
        	 frm.show();
          }
     }
     , eliminar:function(){
    	var grid = Ext.getCmp('gridGestionEmpleados');
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
        	 Ext.Msg.confirm('Confirmaci&oacute;n','&iquest; Est&aacute; seguro de eliminar el empleado seleccionado?',function(btn){  
         	        if(btn === 'yes'){  
			        	  Ext.Ajax.request({
			      			url: '/SIGP/empleado/eliminar',
			      			method: 'POST',
			      			waitMsg : 'Enviando datos...', 
			      			params: 'pEmpleadoId=' + index.get('empleadoId'),
			      			success: function(respuesta, request) {
			      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
			      				if (jsonData.success == true){
			      					stEmpleados.reload();
				      				Ext.Msg.alert('Operaci&oacute;n exitosa','Se ha eliminado el empleado: '+index.get('nombre')+' '+index.get('apellido'));
			      				}else{
			      					 Ext.MessageBox.show({  
				      	                title: 'Error.',  
				      	                msg: 'No se ha eliminado el empleado:<BR>'+jsonData.errorMsj,  
				      	                buttons: Ext.MessageBox.OK,  
				      	                icon: Ext.MessageBox.ERROR
				      	               });	
			      				}         				
			      			},
			      			failure: function ( respuesta, request) {
			      				Ext.MessageBox.show({
			 	        		     title: "Operaci&oacute;n no realizada.",
			 	        		     msg: "No se ha eliminado el empleado. Intente de nuevo.",
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