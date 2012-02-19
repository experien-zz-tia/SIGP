frmNotificacion = Ext.extend(frmNotificacionUi, {
	initComponent : function() {
		frmNotificacion.superclass.initComponent.call(this);
		Ext.getCmp('btnLimpiar').on('click', this.cancelar);
		Ext.getCmp('btnSalir').on('click', this.salir);
		Ext.getCmp('btnEnviar').on('click', this.grabar);
	},
	cancelar : function() {
		Ext.getCmp('txtMensaje').reset();
	},
	salir : function() {
		Ext.getCmp('formNotificacion').getForm().reset();
		Ext.getCmp('frmNotificacion').close();
	},
	grabar : function() {
		if (Ext.getCmp('txtMensaje').getValue() != '') {
			Ext.getCmp('formNotificacion').getForm().submit({
				waitMsg : 'Enviando datos...',
				params : {
					pEnviarCorreo : Ext.getCmp('checkEnviarC').getValue()
				},
				failure : function(form, action) {
					Ext.MessageBox.show({
								title : 'Error',
								msg : 'Error al registrar.',
								buttons : Ext.MessageBox.OK,
								icon : Ext.MessageBox.ERROR
							});
				},
				success : function(form, action) {
					Ext.MessageBox.show({
						title : 'Informaci&oacute;n',
						msg : 'La notificaci&oacute;n se ha enviado de manera exitosa.',
						buttons : Ext.MessageBox.OK,
						icon : Ext.MessageBox.INFO,
						fn : function() {
							Ext.getCmp('frmNotificacion').close();
						}
					});
				} // End Success
			});
		} else {
			Ext.MessageBox.show({
				title : "Error",
				msg : "Datos incompletos o no v&aacute;lidos, por favor verifique.",
				width : 400,
				buttons : Ext.MessageBox.OK,
				icon : Ext.MessageBox.ERROR
			});
		}
	}
});
