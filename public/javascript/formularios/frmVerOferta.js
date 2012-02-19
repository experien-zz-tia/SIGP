frmVerOferta = Ext.extend(frmVerOfertaUi, {
    initComponent: function() {
        frmVerOferta.superclass.initComponent.call(this);
        Ext.getCmp('btnSalir').on('click',this.salir);
        Ext.getCmp('btnPostularOferta').on('click',this.postularOferta);
    },
     salir:function(){
    	 Ext.getCmp('frmVerOfertaWin').close();    
    },
     postularOferta:function(){
     	 var id = Ext.getCmp('txtIdOferta').getValue();
        	  Ext.Ajax.request({
	  			url: '/SIGP/postulacion/registrar',
	  			method: 'POST',
	  			params: 'pOfertaId=' + id,
	  			success: function(respuesta, request) {
	  				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
	  				if (jsonData.success == true){
		         		Ext.Msg.alert('Operaci&oacute;n exitosa','Ud. se ha postulado a la oferta: '+iExt.getCmp('txtTitulo').getValue());
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
});
