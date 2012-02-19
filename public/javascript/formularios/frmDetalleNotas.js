

Ext.QuickTips.init(); 
frmDetalleNotas = Ext.extend(frmDetalleNotasUi, {
    initComponent: function() {
        frmDetalleNotas.superclass.initComponent.call(this);
        Ext.getCmp('btnCancelar').on('click',this.cancelar);
        Ext.getCmp('btnGuardar').on('click',this.guardar);
        Ext.getCmp('btnSalir').on('click',this.salir);
    },
    salir:function(){
    	 Ext.getCmp('frmDetalleNotasWin').close();  
    },
    cancelar: function(){  
      Ext.Msg.confirm('Confirmaci&oacute;n','&iquest; Est&aacute; seguro de cancelar?<BR> '+
         					'Las notas sin guardar se perder&aacute;n.',function(btn){  
         	        if(btn === 'yes'){  
				    	var grid = Ext.getCmp('gridDetalleNotas');
				        grid.getStore().rejectChanges();  
         	        }
         	    });  
         		
    }, 
    guardar: function(){  
    	var grid = Ext.getCmp('gridDetalleNotas');
	    var datosModificados = grid.getStore().getModifiedRecords();
	    if(!Ext.isEmpty(datosModificados)){  
	        var registrosEnviar = [];
	        var pasanteId= Ext.getCmp('txtIdPasante').getValue();  
	        Ext.each(datosModificados, function(registro) {  
	        	 registrosEnviar.push(Ext.apply({id:pasanteId,
	        	 	 aspectoId: registro.data.aspectoId, 
	        	 	 item: registro.data.item,
	        	 	 nota: registro.data.nota
	        	 	}));
	        });  
	        grid.el.mask('Guardando...', 'x-mask-loading'); 
	        grid.stopEditing();  
	        registrosEnviar = Ext.encode(registrosEnviar);  
	      
	        Ext.Ajax.request({      
	            url : '/SIGP/pasante/registrarNota',  
	            params :{datos : registrosEnviar},  
	            scope:this,  
	            success : function(respuesta, request) { 
	                grid.el.unmask();  
	                var jsonData = Ext.util.JSON.decode(respuesta.responseText);
	                if (jsonData.success == true){
                 		Ext.Msg.alert('Operaci&oacute;n Exitosa','Se han guardado los cambios.');
                 		grid.getStore().commitChanges();
                 	}else{
                 		Ext.Msg.alert('Operaci&oacute;n no completada','Se han presentado los siguientes problemas:<BR>'+jsonData.errorMsj);
                	}    
                	grid.getStore().reload();
	            }  ,
     			failure: function ( respuesta, request) {
     				Ext.MessageBox.show({
	        		     title: "Operaci&oacute;n no realizada.",
	        		     msg: "No se han guardado los cambios. <BR> Intente de nuevo.",
	        		     width:400,
	        		     buttons: Ext.MessageBox.OK,
	        		     icon: Ext.MessageBox.ERROR
	        		    });
     			}
	        });  
	    } 
	    
	    
	     }
	});

  
    
    
    

    