panelOferta = Ext.extend(panelOfertaUi, {
    initComponent: function() {
        panelOferta.superclass.initComponent.call(this);
        Ext.getCmp('btnVerOferta').on('click',this.verOferta);
        Ext.getCmp('btnPostular').on('click',this.postular);

    },
      verOferta:function(){
    	var grid = Ext.getCmp('gridOfertas');
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
         	 var frmOferta = new frmVerOferta({
         	     		renderTo: Ext.getBody()
         	 		});
         	 		//resto de los valores
			 Ext.getCmp('txtTitulo').setValue(index.get('titulo'));
       		 Ext.getCmp('txtVacantes').setValue(index.get('vacantes'));
      		 Ext.getCmp('txtEmpresa').setValue(index.get('razonSocial'));
      		 Ext.getCmp('txtArea').setValue(index.get('area'));
      		 Ext.getCmp('txtTipoOferta').setValue((index.get('tipoOferta')=='A')?'Abierta':'Cerrada');
      		 Ext.getCmp('txtDisponible').setValue((index.get('disponible')==0)?'Sin Limite':index.get('disponible'));
      		 Ext.getCmp('dateFechaCierre').setValue(index.get('fchCierre'));
      		
         	 Ext.getCmp('txtIdOferta').setValue(index.get('id'));
     		Ext.Ajax.request({
  			url: '/SIGP/oferta/getOfertaById',
  			method: 'POST',
  			params: 'pOfertaId=' + index.get('id'),
  			success: function(respuesta, request) {
  				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
  				if (jsonData.success == true){
	         		var resultado= jsonData.resultado;
					//Mostramos los valores obtenidos
					Ext.getCmp('txtDescripcion').update(resultado.descripcion);
					Ext.getCmp('dateFechaInicioEst').setValue(resultado.fchInicio);
      		 		Ext.getCmp('dateFechaCulminacionEst').setValue(resultado.fchCulminacion);
  				}else{
  					Ext.Msg.alert('Operaci&oacute;n no completada','No se ha obtenido la descripci&oacute;n.');
  				}         				
  			},
  			failure: function ( respuesta, request) {
  				Ext.MessageBox.show({
        		     title: "Operaci&oacute;n no realizada.",
        		     msg: "No se puede obtener la descripci&oacute;n. Intente de nuevo.",
        		     width:400,
        		     buttons: Ext.MessageBox.OK,
        		     icon: Ext.MessageBox.ERROR
        		    });
  			}
  		});
  	  frmOferta.show();
         }
     },
     postular:function(){
    	var grid = Ext.getCmp('gridOfertas');
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
        	  Ext.Ajax.request({
	  			url: '/SIGP/postulacion/registrar',
	  			method: 'POST',
	  			params: 'pOfertaId=' + id,
	  			success: function(respuesta, request) {
	  				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
	  				if (jsonData.success == true){
		         		Ext.Msg.alert('Operaci&oacute;n exitosa','Ud. se ha postulado a la oferta: '+index.get('titulo'));
	  				}else{
	  					Ext.Msg.alert('Operaci&oacute;n no completada','Se ha(n) presentado el(los) siguiente(s) error(es): <BR>' +jsonData.errorMsj);
	  				}         				
	  			},
	  			failure: function ( respuesta, request) {
	  				Ext.MessageBox.show({
	        		     title: "Operaci&oacute;n no realizada.",
	        		     msg: "No se realizar la operaci&oacute;n. Intente de nuevo.",
	        		     width:400,
	        		     buttons: Ext.MessageBox.OK,
	        		     icon: Ext.MessageBox.ERROR
	        		    });
	  			}
  			});
        	  
        	  
          }
     }
     
});




