panelGestionUniversidad = Ext.extend(panelGestionUniversidadUi, {
	initComponent : function() {
		panelGestionUniversidad.superclass.initComponent.call(this);
		Ext.getCmp('btnAgregar').on('click',this.agregar);
        Ext.getCmp('btnModificar').on('click',this.modificar);
        //Ext.getCmp('gridGestionUniversidad').store.reload();
	}
	,
    agregar:function(){
		Ext.Ajax.request({
			url: '/SIGP/universidad/contar',
			method: 'POST',
			params: { },
			success: function(respuesta, request) {
	      				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
	      				if((jsonData.success == true)){
	      					var frm = new frmUniversidad({
	      			        	renderTo: Ext.getBody()
	      			        });
	      			        
	      			    	 frm.show();
	      				} else {
	      					Ext.MessageBox.show({
	      		      		     title: " Error",
	      		      		     msg: "Actualmente ya se encuentra registrada una Universidad.",
	      		      		     width:400,
	      		      		     buttons: Ext.MessageBox.OK,
	      		      		     icon: Ext.MessageBox.WARNIRG
	      		      		    });
	      				}
				}
		});
		
    	
    },
     modificar:function(){
    	var grid = Ext.getCmp('gridGestionUniversidad');
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
        	 var frm = new frmActualizarUniversidad({
        		renderTo: Ext.getBody()
        	});
        	 Ext.getCmp('txtId').setValue(id);
        	 frm.buscar();
        	 frm.show();
          }
     }
});