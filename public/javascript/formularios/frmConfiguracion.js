Ext.QuickTips.init();
frmConfiguracion = Ext.extend(frmConfiguracionUi, {
	initComponent : function() {
		frmConfiguracion.superclass.initComponent.call(this);
		this.cargarDatosConfig();
		Ext.getCmp('btnReset').on('click', this.cargarDatosConfig);
		Ext.getCmp('btnGuardar').on('click', this.guardarConfig);
	},
	cargarDatosConfig : function() {
		Ext.Ajax.request({
			url : '/SIGP/configuracion/getConfiguracion',
			method : 'POST',
			success : function(respuesta, request) {
				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
				if ((jsonData.success == true)) {
					jsonData = jsonData.resultado;
					Ext.getCmp('txtMaxSolicTutor')
							.setValue(jsonData.maxSolicTutor);
					Ext.getCmp('txtMaxSolicOferta')
							.setValue(jsonData.maxSolicSimul);
					Ext.getCmp('txtMaxRecSolicTutor')
							.setValue(jsonData.maxSolicRecibidasTutor);
					Ext.getCmp('txtMaxMensajes')
							.setValue(jsonData.maxMensajesAlmacenados);
					if (jsonData.inscripciones == 'A') {
						Ext.getCmp('radioInscrip').setValue('radioS', true);
					} else {
						Ext.getCmp('radioInscrip').setValue('radioN', true);
					}
					if (jsonData.calificaciones == 'S') {
						Ext.getCmp('radioCalif').setValue('radioCS', true);
					} else {
						Ext.getCmp('radioCalif').setValue('radioCN', true);
					}
					if (jsonData.actCalif == 'S') {
						Ext.getCmp('radioActCalif').setValue('radioACS', true);
					} else {
						Ext.getCmp('radioActCalif').setValue('radioACN', true);
					}
				} else {
					Ext.MessageBox.show({
								title : 'Error.',
								msg : 'No se puede obtener los datos.',
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

		/** Datos del coordinador y decanato* */
		Ext.Ajax.request({
			url : '/SIGP/coordinacion/getDatosCoordinador',
			method : 'POST',
			success : function(respuesta, request) {
				var jsonData = Ext.util.JSON.decode(respuesta.responseText);
				if ((jsonData.success == true)) {
					jsonData = jsonData.resultado;
					Ext.getCmp('txtCoordinador').setValue(jsonData.nombre + ' '
							+ jsonData.apellido);
					Ext.getCmp('txtCedula').setValue(jsonData.cedula);
				} else {
					Ext.MessageBox.show({
						title : 'Error.',
						msg : 'No se puede obtener los datos de la coordinaci&oacute;n.',
						buttons : Ext.MessageBox.OK,
						icon : Ext.MessageBox.ERROR
					});
				}
			},
			failure : function(respuesta, request) {
				Ext.MessageBox.show({
					title : "Operaci&oacute;n no realizada.",
					msg : "No se puden obtener los datos de la coordinaci&oacute;n. Intente de nuevo.",
					width : 400,
					buttons : Ext.MessageBox.OK,
					icon : Ext.MessageBox.ERROR
				});
			}
		});
	},
	guardarConfig : function() {
		if (Ext.getCmp('txtMaxSolicTutor').isValid()
				&& Ext.getCmp('txtMaxSolicOferta').isValid()
				&& Ext.getCmp('txtMaxRecSolicTutor').isValid()
				&& Ext.getCmp('txtMaxMensajes').isValid()) {

			Ext.MessageBox.getDialog().body.child('input').dom.type = 'password';
			Ext.Msg.prompt('Confirmaci&oacute;n',
					'Por favor ingrese su contrase&ntilde;a:', function(btn,
							text) {
						if (btn == 'ok') {
							valorClave = hex_md5(text);
							Ext.Ajax.request({
								url : '/SIGP/configuracion/guardar',
								method : 'POST',
								params : {
									pClave : valorClave,
									pMaxSolicTutor : Ext
											.getCmp('txtMaxSolicTutor')
											.getValue(),
									pMaxSolicOferta : Ext
											.getCmp('txtMaxSolicOferta')
											.getValue(),
									pMaxRecSolicTutor : Ext
											.getCmp('txtMaxRecSolicTutor')
											.getValue(),
									pMaxMensajes : Ext
											.getCmp('txtMaxMensajes')
											.getValue(),
									pRadioInscrip : (Ext.getCmp('radioInscrip')
											.getValue().getId() == 'radioS')
											? 'A'
											: 'C'
											,
									pRadioCalif : (Ext.getCmp('radioCalif')
											.getValue().getId() == 'radioCS')
											? 'S'
											: 'N'
											,
									pRadioActCalif : (Ext.getCmp('radioActCalif')
											.getValue().getId() == 'radioACS')
											? 'S'
											: 'N'
								},
								success : function(respuesta, request) {
									var jsonData = Ext.util.JSON
											.decode(respuesta.responseText);
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
									Ext.getCmp('btnReset').fireEvent('click');
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
						}
						Ext.MessageBox.getDialog().body.child('input').dom.type = 'text';
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
});
