
panelEmpresas = Ext.extend(panelEmpresasUi, {
    initComponent: function() {
        panelEmpresas.superclass.initComponent.call(this);
        Ext.getCmp('btnEliminar').on('click',this.eliminarEmpresa);
        Ext.getCmp('btnAgregar').on('click',this.agregarEmpresa);
        Ext.getCmp('btnModificarEmp').on('click',this.modificar);
        Ext.getCmp('btnVerDetalles').on('click',this.verDetalles);
    },
     verDetalles:function(){
    	var grid = Ext.getCmp('gridEmpresas');
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
        	 var frmEmpresa = new frmEmpresaResumen({
         	     renderTo: Ext.getBody()
         	         	 });
         	 Ext.getCmp('txtRif').setValue(index.get('rif'));
      		 Ext.getCmp('txtRazonSocial').setValue(index.get('razonSocial'));
      		 Ext.getCmp('txtContacto').setValue(index.get('contacto'));
         	 stOfertasEmpresa.setBaseParam('pEmpresa_id',index.get('id'));
			 stOfertasEmpresa.load({params: {start: 0, limit: 5}}); 
			 stPasantias.setBaseParam('pEmpresa_id',index.get('id') );
			 stPasantias.load({params: {start: 0, limit: 5}}); 
        	 frmEmpresa.show();
        	}
     },
     eliminarEmpresa:function(){
    	var grid = Ext.getCmp('gridEmpresas');
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
         
         		Ext.Msg.confirm('Confirmaci&oacute;n','&iquest; Est&aacute; seguro de eliminar la empresa seleccionada ?<BR> '+
         					'Esta operaci&oacute;n eliminar&aacute; TODOS los tutores empresariales asociados a la empresa y sus usuarios en el sistema.<BR>'+
         					'De igual forma eliminar&aacute; el usuario de la empresa.',function(btn){  
         	        if(btn === 'yes'){  
         	        	Ext.Ajax.request({
                 			url: '/SIGP/empresa/eliminarEmpresa',
                 			method: 'POST',
                 			params: 'pIdEmpresa=' + index.get('id'),
                 			success: function(respuesta, request) {
                 				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
                 				if (jsonData.success == true){
                 					Ext.Msg.alert('Operaci&oacute;n Exitosa','Se ha eliminado la Empresa: <BR>'+index.get('razonSocial'));
                 					stEmpresasFull.reload();
                 				}else{
                 					Ext.Msg.alert('Operaci&oacute;n no completada','No se ha eliminado la Empresa.');
                 				}         				
                 			},
                 			failure: function ( respuesta, request) {
                 				Ext.MessageBox.show({
            	        		     title: "Operaci&oacute;n no realizada.",
            	        		     msg: "La empresa: "+index.get('razonSocial')+" <BR> no se ha eliminado. <BR> Intente de nuevo.",
            	        		     width:400,
            	        		     buttons: Ext.MessageBox.OK,
            	        		     icon: Ext.MessageBox.ERROR
            	        		    });
                 			}
                 		});  
         	        }
         	    });  
         		
         	
         }
     },
      agregarEmpresa:function(){
    	 var frmEmpresa = new frmEmpresaCoord({
         	     renderTo: Ext.getBody()
         	        });
       	 frmEmpresa.show();
     },
     modificar:function(){
    	var grid = Ext.getCmp('gridEmpresas');
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
        	  var frmEmpresa = new frmEmpresaCoord({
         	     renderTo: Ext.getBody()
         		 
         	 });
        	  
        	  Ext.Ajax.request({
      			url: '/SIGP/empresa/getEmpresa',
      			method: 'POST',
      			params: 'pEmpresaId=' + index.get('id'),
      			success: function(respuesta, request) {
      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
      				if (jsonData.success == true){
      					var resultado= jsonData.resultado;
      					//Mostramos los valores obtenidos
      					Ext.getCmp('txtRif').setValue(resultado.rif);
      					Ext.getCmp('txtRif').disable(true);
      					Ext.getCmp('txtRazonSocial').setValue(resultado.razonSocial);
      					Ext.getCmp('txtTelefono').setValue(resultado.telefono);
      					Ext.getCmp('txtTelefono2').setValue(resultado.telefono2);
      					Ext.getCmp('txtDescripcion').setValue(resultado.descripcion);
      					Ext.getCmp('txtDireccion').setValue(resultado.direccion);
      					Ext.getCmp('txtRepresentante').setValue(resultado.representante);
      					Ext.getCmp('txtCargo').setValue(resultado.cargo);
      					Ext.getCmp('txtCorreo').setValue(resultado.correo);
      					Ext.getCmp('txtCorreo').disable();
      					Ext.getCmp('txtCorreoRepetir').setValue(resultado.correo);
      					Ext.getCmp('txtCorreoRepetir').disable();
      					Ext.getCmp('txtWeb').setValue(resultado.web);
      					var comboEst = Ext.getCmp('cmbEstado');      					
      					var storeEst = comboEst.getStore();
      					storeEst.load({
      					   callback: function() {
      					      comboEst.setValue(resultado.estadoId);
      					   }
      					});
      					var comboC = Ext.getCmp('cmbCiudad');      					
      					var store = comboC.getStore();
      					store.load({
      					   params: {idEstado: resultado.estadoId},
      					   callback: function() {
      					      comboC.setValue(resultado.ciudadId);
      					   }
      					});
      					Ext.getCmp('txtIdEmpresa').setValue(index.get('id'));
      				}else{
      					Ext.Msg.alert('Operaci&oacute;n no completada','No se han obtenido los datos.');
      				}         				
      			},
      			failure: function ( respuesta, request) {
      				Ext.MessageBox.show({
 	        		     title: "Operaci&oacute;n no realizada.",
 	        		     msg: "No se pueden obtener los datos. Intente de nuevo.",
 	        		     width:400,
 	        		     buttons: Ext.MessageBox.OK,
 	        		     icon: Ext.MessageBox.ERROR
 	        		    });
      			}
      		});
        	  Ext.getCmp('btnActualizar').show();
        	  Ext.getCmp('btnRegistrar').hide();
        	  
        	  	 
        	  frmEmpresa.show();
          }
    
    	 
    	
     }
});