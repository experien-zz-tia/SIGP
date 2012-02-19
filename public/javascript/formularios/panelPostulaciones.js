panelPostulaciones = Ext.extend(panelPostulacionesUi, {
    initComponent: function() {
        panelPostulaciones.superclass.initComponent.call(this);
        Ext.getCmp('btnRechazar').on('click',this.rechazar);
        Ext.getCmp('btnAceptarP').on('click',this.aceptarP);
        Ext.getCmp('btnVerDetallePostulante').on('click',this.verDetallePasante);
        Ext.getCmp('btnRefrescar').on('click',this.refrescar);

    },
      rechazar:function(){
    	var grid = Ext.getCmp('gridPostulaciones');
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
         	 var frmRechazo = new frmNotificarRechazo({
         	     		renderTo: Ext.getBody()
         	 	});
         	
         	 Ext.getCmp('txtTitulo').setValue(index.get('titulo'));
       		 Ext.getCmp('txtIdPostulacion').setValue(index.get('id'));
      		 Ext.getCmp('txtNombreApellido').setValue(index.get('nombre')+ ', ' +index.get('apellido'));
      		 Ext.getCmp('txtCarrera').setValue(index.get('carrera'));
  	 		 frmRechazo.show();
         }
     },
     refrescar:function(){
	 	stgPostulaciones.reload(); 
     },
     aceptarP:function(){
    	var grid = Ext.getCmp('gridPostulaciones');
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
        	  
        	  
        	   var frmAceptacion = new frmAceptarPostulacion({
         	     		renderTo: Ext.getBody()
         	 	});
         	
         	 Ext.getCmp('txtTitulo').setValue(index.get('titulo'));
         	 Ext.getCmp('txtCedula').setValue(index.get('cedula'));
         	 Ext.getCmp('txtNombreApellido').setValue(index.get('nombre')+ ', ' +index.get('apellido'));
      		 Ext.getCmp('txtCarrera').setValue(index.get('carrera'));
       		 Ext.getCmp('txtIdPostulacion').setValue(index.get('id'));
      		 
  	 	
          Ext.Ajax.request({
	  			url: '/SIGP/postulacion/getDatosPostulacion',
	  			method: 'POST',
	  			params: 'pIdPostulacion=' + id,
	  			success: function(respuesta, request) {
	  				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
	  				if (jsonData.success == true){
		         		Ext.getCmp('txtModalidad').setValue(jsonData.datos.modalidadPasantia);
		         		Ext.getCmp('txtTipo').setValue(jsonData.datos.tipoPasantia);
		         		Ext.getCmp('dateFechaCierre').setValue(jsonData.datos.fchCierre);
		         		Ext.getCmp('dateFechaInicioEst').setValue(jsonData.datos.fchInicioEst);
		         		Ext.getCmp('dateFechaCulminacionEst').setValue(jsonData.datos.fchFinEst);
	  				}else{
	  					Ext.Msg.alert('Operaci&oacute;n no completada','Se ha podido recuperar el resto de los datos. Intente de nuevo.');
	  				}         				
	  			},
	  			failure: function ( respuesta, request) {
	  				Ext.MessageBox.show({
	        		     title: "Operaci&oacute;n no realizada.",
	        		     msg: "No se puede realizar la operaci&oacute;n. Intente de nuevo.",
	        		     width:400,
	        		     buttons: Ext.MessageBox.OK,
	        		     icon: Ext.MessageBox.ERROR
	        		    });
	  			}
  			});
        	  
        	 frmAceptacion.show();
          }
     }
     ,
     verDetallePasante:function(){
    	var grid = Ext.getCmp('gridPostulaciones');
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
        	  var id = index.get('pasanteId');
        	  var frmVerP = new frmVerPasante({
         	     		renderTo: Ext.getBody()
         	 	});
         	
         	 Ext.getCmp('txtCedula').setValue(index.get('cedula'));
         	 Ext.getCmp('txtNombreApellido').setValue(index.get('nombre')+ ', ' +index.get('apellido'));
      		 Ext.getCmp('txtCarrera').setValue(index.get('carrera'));

          Ext.Ajax.request({
	  			url: '/SIGP/pasante/getDetallePasante',
	  			method: 'POST',
	  			params: 'pPasanteId=' + id,
	  			success: function(respuesta, request) {
	  				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
	  				if (jsonData.success == true){
		         		Ext.getCmp('txtSemestre').setValue(jsonData.datos.semestre);
		         		Ext.getCmp('txtTelefono').setValue(jsonData.datos.telefono);
		         		Ext.getCmp('txtCorreo').setValue(jsonData.datos.email);
		         		Ext.getCmp('txtDescripcion').setValue(jsonData.datos.descripcion==''?'Sin detalles.':jsonData.datos.descripcion);
		         		Ext.getCmp('txtExperiencia').setValue(jsonData.datos.experiencia==''?'Sin detalles.':jsonData.datos.experiencia);
		         		Ext.getCmp('txtCursos').setValue(jsonData.datos.cursos==''?'Sin detalles.':jsonData.datos.cursos);
	  				}else{
	  					Ext.Msg.alert('Operaci&oacute;n no completada','Se ha podido recuperar el resto de los datos. Intente de nuevo.');
	  				}         				
	  			},
	  			failure: function ( respuesta, request) {
	  				Ext.MessageBox.show({
	        		     title: "Operaci&oacute;n no realizada.",
	        		     msg: "No se puede realizar la operaci&oacute;n. Intente de nuevo.",
	        		     width:400,
	        		     buttons: Ext.MessageBox.OK,
	        		     icon: Ext.MessageBox.ERROR
	        		    });
	  			}
  			});
        	  
        	 frmVerP.show();
          }
     }
});





