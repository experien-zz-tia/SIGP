Ext.QuickTips.init();
frmReporteCalificaciones = Ext.extend(frmReporteCalificacionesUi, {
			initComponent : function() {
				frmReporteCalificaciones.superclass.initComponent.call(this);		
				Ext.getCmp('btnPasante').on('click', this.reportePasante);
				Ext.getCmp('btnLimpiarPasante')
						.on('click', this.limpiarPasante);
			},
			reportePasante : function() {
				window.open('/SIGP/reporte/mostrarCalificaciones?pCarrera='
						+ Ext.getCmp('cmbCarrera').getValue());
			},
			limpiarPasante : function() {
				Ext.getCmp('cmbCarrera').reset();
			}
		});