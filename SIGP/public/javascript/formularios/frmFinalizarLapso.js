frmFinalizarLapso = Ext.extend(frmFinalizarLapsoUi, {
	initComponent : function() {
		frmFinalizarLapso.superclass.initComponent.call(this);
		Ext.getCmp('btnSalir').on('click', this.salir);
		Ext.getCmp('btnAceptar').on('click', this.aceptar);
	},
	salir : function() {
		Ext.getCmp('frmFinalizarLapsoWin').close();
	},
	aceptar : function() {
			Ext.Ajax.request({
				url : '/SIGP/lapsoAcademico/finalizar',
				method : 'POST',
      			params: {pLapsoId:Ext.getCmp('txtLapsoId').getValue(),
	  					pOmitirSE:Ext.getCmp('checkOmitirSE').getValue(),
	  					pEnviarNotif:Ext.getCmp('checkEnviarNotif').getValue()
	      				},
				success : function(respuesta, request) {
					var jsonData = Ext.util.JSON.decode(respuesta.responseText);
					if((jsonData.success == true)) {
						Ext.MessageBox.show({
							title : 'Informaci&oacute;n',
							msg : 'Operaci&oacute;n exitosa.',
							buttons : Ext.MessageBox.OK,
							icon : Ext.MessageBox.INFO,
							fn : function() {
								Ext.getCmp('frmFinalizarLapsoWin').close();
								stLapsosAcademicos.reload();
							}
						});
					} else {
						Ext.MessageBox.show({
							title : 'Error',
							msg : 'Operaci&oacute;n incompleta. Se ha(n) presentado el(los) siguiente(s) error(es):<BR> ' + jsonData.errorMsj,
							buttons : Ext.MessageBox.OK,
							icon : Ext.MessageBox.ERROR
						});
					}
				}//End Success
				,
				failure : function(form, action) {
					Ext.MessageBox.show({
						title : 'Error',
						msg : 'No se  pudo finalizar el lapso.',
						buttons : Ext.MessageBox.OK,
						icon : Ext.MessageBox.ERROR
					});
				}
			});
	}
});
