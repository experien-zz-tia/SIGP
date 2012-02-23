Ext.QuickTips.init();
frmReporteMaestros = Ext.extend(frmReporteMaestrosUi, {
			initComponent : function() {
				frmReporteMaestros.superclass.initComponent.call(this);
				Ext.getCmp('btnTutor').on('click', this.reporteTutor);
				Ext.getCmp('btnOferta').on('click', this.reporteOferta);
				Ext.getCmp('btnEmpresa').on('click', this.reporteEmpresa);
				Ext.getCmp('btnPasante').on('click', this.reportePasante);
				Ext.getCmp('btnLimpiarTutor').on('click', this.limpiarTutor);
				Ext.getCmp('btnLimpiarOferta').on('click', this.limpiarOferta);
				Ext.getCmp('btnLimpiarEmpresa')
						.on('click', this.limpiarEmpresa);
				Ext.getCmp('btnLimpiarPasante')
						.on('click', this.limpiarPasante);
				Ext.getCmp('cmbEstado').on('select', this.actualizarCiudades);
			},
			actualizarCiudades : function() {
				Ext.getCmp('cmbCiudad').clearValue();
				Ext.getCmp('cmbCiudad').store.reload({
							params : {
								idEstado : Ext.getCmp('cmbEstado').getValue()
							}
						});
			},
			reportePasante : function() {
				window.open('/SIGP/reporte/mostrarPasantes?pCarrera='
						+ Ext.getCmp('cmbCarrera').getValue());
			},
			reporteOferta : function() {
				window.open('/SIGP/reporte/mostrarOfertas?pInicio='
						+ Ext.getCmp('dateFechaInicioEst').getRawValue()
						+ '&pFin='
						+ Ext.getCmp('dateFechaCulminacionEst').getRawValue());

			},
			reporteTutor : function() {
				if (Ext.getCmp('cmbTipo').getValue() != '') {
					window.open('/SIGP/reporte/mostrarTutores?pTipo='
							+ Ext.getCmp('cmbTipo').getValue());
				} else {
					alert("Por favor, seleccione un tipo de tutor.")
				}
			},
			reporteEmpresa : function() {
				window.open('/SIGP/reporte/mostrarEmpresas?pCiudad='
						+ Ext.getCmp('cmbCiudad').getValue());
			},
			limpiarTutor : function() {
				Ext.getCmp('cmbTipo').reset();
			},
			limpiarOferta : function() {
				Ext.getCmp('dateFechaInicioEst').reset();
				Ext.getCmp('dateFechaCulminacionEst').reset();
			},
			limpiarPasante : function() {
				Ext.getCmp('cmbCarrera').reset();
			},
			limpiarEmpresa : function() {
				Ext.getCmp('cmbCiudad').reset();
				Ext.getCmp('cmbEstado').reset();

			}
		});
