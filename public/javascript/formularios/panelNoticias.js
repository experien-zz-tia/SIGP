panelNoticias = Ext.extend(panelNoticiasUi, {
    initComponent: function() {
        panelNoticias.superclass.initComponent.call(this);
        Ext.getCmp('btnAgregar').on('click',this.agregar);
        Ext.getCmp('btnEliminar').on('click',this.eliminar);
        Ext.getCmp('btnModificar').on('click',this.modificar);

    },
     eliminar:function(){
    	var grid = Ext.getCmp('gridNoticias');
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
          		Ext.Msg.confirm('Confirmaci&oacute;n','&iquest; Est&aacute; seguro de eliminar la noticia seleccionada ?',function(btn){  
         	        if(btn === 'yes'){  
         	        	Ext.Ajax.request({
                 			url: '/SIGP/noticia/eliminar',
                 			method: 'POST',
                 			params: 'pNoticiaId=' + index.get('id'),
                 			success: function(respuesta, request) {
                 				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
                 				if (jsonData.success == true){
                 					Ext.Msg.alert('Operaci&oacute;n Exitosa','Se ha eliminado la noticia: <BR>'+index.get('titulo'));
                 					stNoticias.reload();
                 				}else{
                 					Ext.Msg.alert('Operaci&oacute;n no completada',jsonData.errorMsj);
                 				}         				
                 			},
                 			failure: function ( respuesta, request) {
                 				Ext.MessageBox.show({
            	        		     title: "Operaci&oacute;n no realizada.",
            	        		     msg: "La noticia: "+index.get('titulo')+" <BR> no se ha eliminado. <BR> Intente de nuevo.",
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
     agregar:function(){
    	 var fNoticia = new frmNoticia({
    	     renderTo: Ext.getBody()
    		 
    	 });
    	 fNoticia.show();
    	
     },
     modificar:function(){
    	var grid = Ext.getCmp('gridNoticias');
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
        	  var fNoticia = new frmNoticia({
    	   	  		renderTo: Ext.getBody()
    	 		});
        	  
        	  Ext.Ajax.request({
      			url: '/SIGP/noticia/getNoticia',
      			method: 'POST',
      			params: 'pNoticiaId=' + index.get('id'),
      			success: function(respuesta, request) {
      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
      				if (jsonData.success == true){
      					var resultado= jsonData.resultado;
      					//Mostramos los valores obtenidos
      					Ext.getCmp('txtTitulo').setValue(resultado.titulo);
      					Ext.getCmp('txtContenido').setValue(resultado.contenido);
      					Ext.getCmp('txtIdNoticia').setValue(resultado.id);
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
        	  fNoticia.show();
          }
    
    	 
    	
     }
     
});




