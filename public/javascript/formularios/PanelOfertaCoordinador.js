
PanelOfertaCoordinador = Ext.extend(PanelOfertaCoordinadorUi, {
    initComponent: function() {
        PanelOfertaCoordinador.superclass.initComponent.call(this);
        Ext.getCmp('btnAgregar').on('click',this.agregarOferta);
        Ext.getCmp('btnPublicar').on('click',this.publicarOferta);
        Ext.getCmp('btnEliminar').on('click',this.eliminarOferta);
        Ext.getCmp('btnModificar').on('click',this.modificarOferta);

    },
    publicarOferta:function(){
    	var grid = Ext.getCmp('gridOfertasEmpresa');
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
        	if (index.get('fchPublicacion')==null){
        		Ext.Ajax.request({
        			url: '/SIGP/oferta/publicarOferta',
        			method: 'POST',
        			params: 'pIdOferta=' + index.get('id'),
        			success: function(respuesta, request) {
        				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
        				if (jsonData.success == true){
        					Ext.Msg.alert('Operaci&oacute;n Exitosa','Se ha publicado la oferta: <BR>'+index.get('titulo'));
        					//Se actualiza el store del grid
        					stOfertas.reload();
        				}else{
        					Ext.Msg.alert('Operaci&oacute;n no completada','No se ha publicado la oferta.');
        				}         				
        			},
        			failure: function ( respuesta, request) {
        				Ext.MessageBox.show({
   	        		     title: "Operaci&oacute;n no realizada.",
   	        		     msg: "La oferta: "+index.get('titulo')+" <BR> no se ha publicado. <BR> Intente de nuevo",
   	        		     width:400,
   	        		     buttons: Ext.MessageBox.OK,
   	        		     icon: Ext.MessageBox.ERROR
   	        		    });
        			}
        		});
        	}else{
        		Ext.MessageBox.show({
        			title: "Oferta ya publicada.",
        			msg: "Esta oferta ya ha sido publicada.",
        			width:400,
        			buttons: Ext.MessageBox.OK,
        			icon: Ext.MessageBox.INFO
        	    });
        	}	
        }
     },
     eliminarOferta:function(){
    	var grid = Ext.getCmp('gridOfertasEmpresa');
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
         	if (index.get('postulados')==0){
         		Ext.Msg.confirm('Confirmaci&oacute;n','&iquest; Est&aacute; seguro de eliminar la oferta seleccionada ?',function(btn){  
         	        if(btn === 'yes'){  
         	        	Ext.Ajax.request({
                 			url: '/SIGP/oferta/eliminarOferta',
                 			method: 'POST',
                 			params: 'pIdOferta=' + index.get('id'),
                 			success: function(respuesta, request) {
                 				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
                 				if (jsonData.success == true){
                 					Ext.Msg.alert('Operaci&oacute;n Exitosa','Se ha eliminado la oferta: <BR>'+index.get('titulo'));
                 					stOfertas.reload();
                 				}else{
                 					Ext.Msg.alert('Operaci&oacute;n no completada','No se ha eliminado la oferta.');
                 				}         				
                 			},
                 			failure: function ( respuesta, request) {
                 				Ext.MessageBox.show({
            	        		     title: "Operaci&oacute;n no realizada.",
            	        		     msg: "La oferta: "+index.get('titulo')+" <BR> no se ha eliminado. <BR> Intente de nuevo.",
            	        		     width:400,
            	        		     buttons: Ext.MessageBox.OK,
            	        		     icon: Ext.MessageBox.ERROR
            	        		    });
                 			}
                 		});  
         	        }
         	    });  
         		
         	}else{
         		Ext.MessageBox.show({
         			title: "Operaci&oacute;n no v&aacute;lida.",
         			msg: "La oferta: <i>"+index.get('titulo') +"</i>, <BR>" +
         				 "no se puede eliminar, tiene: "+index.get('postulados') +" postulantes.<BR> " +
         				 "Debe despostular primero a los candidatos.",
         			width:400,
         			buttons: Ext.MessageBox.OK,
         			icon: Ext.MessageBox.WARNING
         	    });
         	}	
         }
     },
     agregarOferta:function(){
    	 var frmAgregarOferta = new frmNuevaOfertaCoordinador({
    	     renderTo: Ext.getBody()
    		 
    	 });
    	 frmAgregarOferta.show();
    	
     },
     modificarOferta:function(){
    	var grid = Ext.getCmp('gridOfertasEmpresa');
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
        	  var frmAgregarOferta = new frmNuevaOfertaCoordinador({
         	     renderTo: Ext.getBody()
         		 
         	 });
        	  
        	  Ext.Ajax.request({
      			url: '/SIGP/oferta/getOfertaById',
      			method: 'POST',
      			params: 'pOfertaId=' + index.get('id'),
      			success: function(respuesta, request) {
      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
      				if (jsonData.success == true){
      					var resultado= jsonData.resultado;
      					//Mostramos los valores obtenidos
      					Ext.getCmp('txtTitulo').setValue(resultado.titulo);
      					Ext.getCmp('txtDescripcion').setValue(resultado.descripcion);
      					Ext.getCmp('txtVacantes').setValue(resultado.vacantes);
      					Ext.getCmp('txtCupos').setValue(resultado.cupos);
      					Ext.getCmp('dateFechaCierre').setValue(resultado.fchCierre);
      					Ext.getCmp('dateFechaInicioEst').setValue(resultado.fchInicio);
      					Ext.getCmp('dateFechaCulminacionEst').setValue(resultado.fchCulminacion);
      					var comboEmp = Ext.getCmp('cmbEmpresa');      					
      					var storeEmp = comboEmp.getStore();
      					storeEmp.load({
      					   callback: function() {
      					      comboEmp.setValue(resultado.empresaId);
      					      comboEmp.disable();
      					   }
      					});
      					var comboArea = Ext.getCmp('cmbArea');      					
      					var store = comboArea.getStore();
      					store.load({
      					   callback: function() {
      					      comboArea.setValue(resultado.areaId);
      					   }
      					});
      					Ext.getCmp('cmbTipo').setValue(resultado.tipoOferta);
      					Ext.getCmp('txtIdOfertaHidden').setValue(resultado.id);
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
        	  Ext.getCmp('btnGuardar').hide();
        	  Ext.getCmp('btnGuardarPublicar').hide();
        	  	 
        	  frmAgregarOferta.show();
          }
    
    	 
    	
     }
     
});




