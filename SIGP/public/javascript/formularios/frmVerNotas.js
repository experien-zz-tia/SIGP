frmVerNotas = Ext.extend(frmVerNotasUi, {
	initComponent : function() {
		frmVerNotas.superclass.initComponent.call(this);
		this.cargarDatos();
		Ext.getCmp('btnPDF').on('click', this.generarPDF);
	},
	cargarDatos : function() {
		Ext.Ajax.request({
			url : '/SIGP/pasante/getDatosPasante',
			method : 'POST',
			success : function(respuesta, request) {
				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
				if ((jsonData.success == true)) {
					jsonData = jsonData.datos;
					Ext.getCmp('txtCedula')
							.setValue(jsonData.cedula);
					Ext.getCmp('txtNombreCompleto')
							.setValue(jsonData.nombre+' '+jsonData.apellido);
					Ext.getCmp('txtCarrera')
							.setValue(jsonData.carrera);
					Ext.getCmp('txtNotaFinal')
							.setValue(jsonData.acumulado);
				} else {
					Ext.MessageBox.show({
								title : 'Error.',
								msg : 'No se pueden obtener los datos.',
								buttons : Ext.MessageBox.OK,
								icon : Ext.MessageBox.ERROR
							});
				}
			},
			failure : function(respuesta, request) {
				Ext.MessageBox.show({
							title : "Operaci&oacute;n no realizada.",
							msg : "No se puden obtener los datos. Intente de nuevo.",
							width : 400,
							buttons : Ext.MessageBox.OK,
							icon : Ext.MessageBox.ERROR
						});
			}
		});
	},
		generarPDF : function() {
			window.open('/SIGP/reporte/constanciaNotasPasante','Constancia');
	}
});
