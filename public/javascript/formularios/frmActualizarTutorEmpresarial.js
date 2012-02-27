frmActualizarTutorEmpresarial = Ext
		.extend(
				frmActualizarTutorEmpresarialUi,
				{
					initComponent : function() {
						frmActualizarTutorEmpresarial.superclass.initComponent
								.call(this);
						Ext.getCmp('btnSalir').on('click', this.salir);
						Ext.getCmp('btnGuardar').on('click', this.guardarTutor);
						this.buscarTutor();

					},
					salir : function() {
						Ext.getCmp('formActTutorEmpresarial').getForm().reset();
						Ext.getCmp('frmActualizarTutorEmpresarial').hide();
					},
					guardarTutor : function() {
						// Se verifica que los campos marcados como obligatorios
						// (allowBlank:false) esten llenos
						if (Ext.getCmp('txtCedula').disabled) {
							if (Ext.getCmp('formActTutorEmpresarial').getForm()
									.isValid()) {
								Ext
										.getCmp('formActTutorEmpresarial')
										.getForm()
										.submit(
												{
													waitMsg : 'Enviando datos...',
													params : {
														txtCedula : "-"
													},
													failure : function(form,
															action) {
														Ext.MessageBox
																.show( {
																	title : 'Error',
																	msg : 'Error al registrar.',
																	buttons : Ext.MessageBox.OK,
																	icon : Ext.MessageBox.ERROR
																});
													},
													success : function(form,
															action) {
														Ext.MessageBox
																.show( {
																	title : 'Informaci&oacute;n',
																	msg : 'Registro exitoso.',
																	buttons : Ext.MessageBox.OK,
																	icon : Ext.MessageBox.INFO,
																	fn : function() {
																		Ext
																				.getCmp(
																						'formActTutorEmpresarial')
																				.getForm()
																				.reset();
																		Ext
																				.getCmp(
																						'frmActualizarTutorEmpresarial')
																				.hide();
																		stTutoresEmpresariales
																				.reload();
																	}
																});
													} // End Success
												});

							} else {
								Ext.MessageBox
										.show( {
											title : "Error",
											msg : "Datos incompletos o no v&aacute;lidos, por favor verifique.",
											width : 400,
											buttons : Ext.MessageBox.OK,
											icon : Ext.MessageBox.ERROR
										});
							}
						}
					},
					buscarTutor : function() {
						/*
						 * Se busca la cedula ingresada, en caso de encontrarla
						 * se terminan de cargar los datos y se deshabiltan las
						 * cajas de texto
						 */
						var cedula = Ext.getCmp('txtCedula');
						Ext.Ajax
								.request( {
									url : '/SIGP/tutorEmpresarial/buscarTutorEmpresarial',
									method : 'POST',
									params : 'cedula = ' + '-',
									success : function(respuesta, request) {
										var jsonData = Ext.util.JSON
												.decode(respuesta.responseText);
										if (jsonData.success == true) {
											Ext.getCmp('txtCedula').disable();
											habilitarCampos(true);
											var datos = jsonData.datos;
											Ext.getCmp('txtNombre').setValue(
													datos.nombre);
											Ext.getCmp('txtApellido').setValue(
													datos.apellido);
											Ext.getCmp('txtTelefono').setValue(
													datos.telefono);
											Ext.getCmp('txtCorreo').setValue(
													datos.email);
											Ext.getCmp('txtCorreoRepetir')
													.setValue(datos.email);
											Ext.getCmp('txtCargo').setValue(
													datos.cargo);
											Ext.getCmp('txtCedula').disable();

										}
									}
								});

					}

				});

function habilitarCampos(flag) {
	if (flag == true) {
		Ext.getCmp('txtNombre').enable();
		Ext.getCmp('txtApellido').enable();
		Ext.getCmp('txtTelefono').enable();
		Ext.getCmp('txtCorreo').enable();
		Ext.getCmp('txtCorreoRepetir').enable();
		Ext.getCmp('txtCargo').enable();
	} else {
		Ext.getCmp('txtNombre').disable();
		Ext.getCmp('txtApellido').disable();
		Ext.getCmp('txtTelefono').disable();
		Ext.getCmp('txtCorreo').disable();
		Ext.getCmp('txtCorreoRepetir').disable();
		Ext.getCmp('txtCargo').disable();

	}

}