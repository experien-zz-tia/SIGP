frmVerPasanteUi = Ext.extend(Ext.Window, {
	title : 'Detalle del Pasante',
	width : 563,
	height : 340,
	layout : 'form',
	id : 'frmPasanteWin',
	modal : true,
	resizable : false,
	activeItem : 1,
	initComponent : function() {
		this.items = [{
			xtype : 'tabpanel',
			activeTab : 0,
			enableTabScroll : true,
			items : [{
						xtype : 'panel',
						title : 'B&aacute;sico',
						items : [{
									xtype : 'fieldset',
									title : 'Informaci&oacute;n b&aacute;sica',
									autoHeight : true,
									width : 545,
									items : [{
												xtype : 'textfield',
												fieldLabel : 'C&eacute;dula',
												anchor : '50%',
												allowBlank : false,
												readOnly: true,
												id : 'txtCedula'
											}, {
												xtype : 'textfield',
												width : 320,
												anchor : '100%',
												readOnly: true,
												fieldLabel : 'Nombre y Apellido',
												allowBlank : false,
												id : 'txtNombreApellido'
											}, {
												xtype : 'textfield',
												width : 284,
												anchor : '100%',
												readOnly: true,
												fieldLabel : 'Carrera',
												allowBlank : false,
												id : 'txtCarrera'
											}, {
												xtype : 'textfield',
												fieldLabel : 'Semestre',
												anchor : '50%',
												readOnly: true,
												allowBlank : false,
												id : 'txtSemestre'
											}, {
												xtype : 'textfield',
												fieldLabel : 'Tel&eacute;fono',
												anchor : '50%',
												readOnly: true,
												id : 'txtTelefono'
											}, {
												xtype : 'textfield',
												fieldLabel : 'Correo',
												anchor : '100%',
												readOnly: true,
												id : 'txtCorreo'
											}]
								}]
					}, {
						xtype : 'panel',
						title : 'Perfil',
						items : [{
									xtype : 'fieldset',
									title : 'Informaci&oacute;n adicional',
									items : [{
												xtype : 'textarea',
												width : 284,
												anchor : '100%',
												readOnly: true,
												fieldLabel : 'Descripci&oacute;n',
												allowBlank : false,
												id : 'txtDescripcion'
											}, {
												xtype : 'textarea',
												width : 284,
												anchor : '100%',
												readOnly: true,
												fieldLabel : 'Experiencia',
												allowBlank : false,
												id : 'txtExperiencia'
											}, {
												xtype : 'textarea',
												width : 284,
												anchor : '100%',
												readOnly: true,
												fieldLabel : 'Cursos',
												allowBlank : false,
												id : 'txtCursos'
											}]
								}]
					}

			]
		}, {
			xtype : 'container',
			layout : 'hbox',
			id : 'contenedorBtns',
			layoutConfig : {
				pack : 'end'
			},
			items : [{
						xtype : 'button',
						text : 'Salir',
						iconCls : 'sigp-salir',
						id : 'btnSalir'
					}]
		}];
		frmVerPasanteUi.superclass.initComponent.call(this);
	}
});
