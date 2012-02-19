Ext.QuickTips.init();
frmCambiarClave = Ext.extend(frmCambiarClaveUi, {
	initComponent : function() {
		frmCambiarClave.superclass.initComponent.call(this);
		Ext.getCmp('btnGuardar').on('click', this.guardar);
		Ext.getCmp('btnReset').on('click', this.limpiar);
	},
	guardar : function() {
		if (Ext.getCmp('txtClaveActual').isValid()
				&& Ext.getCmp('txtClave').isValid()
				&& Ext.getCmp('txtClave2').isValid()) {

			Ext.Ajax.request({
				url : '/SIGP/usuario/modificarClave',
				method : 'POST',
				params : {
					pClaveActual : hex_md5(Ext.getCmp('txtClaveActual').getValue()),
					pClaveNueva : hex_md5(Ext.getCmp('txtClave').getValue())
				},
				success : function(respuesta, request) {
					var jsonData = Ext.util.JSON.decode(respuesta.responseText);
					if ((jsonData.success == true)) {
						Ext.MessageBox.show({
									title : 'Informaci&oacute;n',
									msg : 'Operaci&oacute;n exitosa.',
									buttons : Ext.MessageBox.OK,
									icon : Ext.MessageBox.INFO
								});
					} else {
						Ext.MessageBox.show({
							title : 'Error.',
							width : 400,
							msg : 'Se ha(n) presentado(n) el(los) siguiente(s) error(es):<BR>'
									+ jsonData.errorMsj,
							buttons : Ext.MessageBox.OK,
							icon : Ext.MessageBox.ERROR
						});
					}
					
				},
				failure : function(respuesta, request) {
					Ext.MessageBox.show({
						title : "Operaci&oacute;n no realizada.",
						msg : "No se han registrado los cambios. Intente de nuevo.",
						width : 400,
						buttons : Ext.MessageBox.OK,
						icon : Ext.MessageBox.ERROR
					});
				}
			});

		} else {
			Ext.MessageBox.show({
				title : "Error.",
				msg : "Datos incompletos o no v&aacute;lidos. Por favor verifique.",
				width : 400,
				buttons : Ext.MessageBox.OK,
				icon : Ext.MessageBox.ERROR
			});
		}
	}
	,
	limpiar:function(){
		 Ext.getCmp('txtClaveActual').reset();
		 Ext.getCmp('txtClave').reset();
		 Ext.getCmp('txtClave2').reset();
	}
	
});
