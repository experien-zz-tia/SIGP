Ext.QuickTips.init();
frmSolicitudCarta = Ext.extend(frmSolicitudCartaUi, {
	initComponent : function() {
		frmSolicitudCarta.superclass.initComponent.call(this);
		Ext.getCmp('btnEnviar').on('click', this.guardar);
		Ext.getCmp('btnReset').on('click', this.limpiar);
	},
	
	guardar : function() {
		window.open('/SIGP/reporte/solCartaPostulacion');
	},
	
	limpiar : function() {
		Ext.getCmp('txtClaveActual').reset();
		Ext.getCmp('txtClave').reset();
		Ext.getCmp('txtClave2').reset();
	}

});
