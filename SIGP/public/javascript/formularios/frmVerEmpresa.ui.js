frmVerEmpresaUi = Ext.extend(Ext.Window, {
	title : 'Empresa',
	width : 563,
	height : 429,
	layout : 'form',
	id : 'frmEmpresaWin',
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
						width : 571,
						height : 367,
						items : [{
									xtype : 'fieldset',
									title : 'Información básica',
									autoHeight : true,
									width : 545,
									items : [{
												xtype : 'textfield',
												name : 'txtRazonSocial',
												fieldLabel : 'Razón social',
												anchor : '100%',
												maxLength : 45,
												allowBlank : false,
												readOnly : true,
												id : 'txtRazonSocial'
											}, {
												xtype : 'textfield',
												name : 'txtRif',
												width : 320,
												maxLength : 12,
												anchor : '100%',
												readOnly : true,
												fieldLabel : 'R.I.F.',
												allowBlank : false,
												id : 'txtRif'
											}, {
												xtype : 'textarea',
												name : 'txtDireccion',
												width : 284,
												anchor : '100%',
												readOnly : true,
												fieldLabel : 'Dirección',
												maxLength : 45,
												allowBlank : false,
												id : 'txtDireccion'
											}, {
												xtype : 'combo',
												name : 'cmbEstado',
												anchor : '50%',
												readOnly : true,
												fieldLabel : 'Estado',
												editable : false,
												store : 'stEstado',
												displayField : 'nombre',
												valueField : 'id',
												emptyText : '-Seleccione-',
												triggerAction : 'all',
												allowBlank : false,
												forceSelection : true,
												loadingText : 'Cargando...',
												blankText : 'Seleccione un estado.',
												id : 'cmbEstado'
											}, {
												xtype : 'combo',
												name : 'cmbCiudad',
												anchor : '50%',
												readOnly : true,
												fieldLabel : 'Ciudad',
												editable : false,
												store : 'stCiudades',
												displayField : 'nombre',
												valueField : 'id',
												triggerAction : 'all',
												allowBlank : false,
												loadingText : 'Cargando...',
												forceSelection : true,
												emptyText : '-Seleccione-',
												blankText : 'Seleccione una ciudad.',
												id : 'cmbCiudad'
											}, {
												xtype : 'textfield',
												fieldLabel : 'Teléfono',
												name : 'txtTelefono',
												anchor : '50%',
												readOnly : true,
												allowBlank : false,
												maxLength : 12,
												id : 'txtTelefono'
											}, {
												xtype : 'textfield',
												name : 'txtTelefono2',
												fieldLabel : 'Teléfono secundario',
												anchor : '50%',
												maxLength : 12,
												readOnly : true,
												id : 'txtTelefono2'
											}, {
												xtype : 'textarea',
												anchor : '100%',
												fieldLabel : 'Descripción',
												maxLength : 140,
												readOnly : true,
												allowBlank : false,
												id : 'txtDescripcion'
											}, {
												xtype : 'textfield',
												fieldLabel : 'Sitio web',
												anchor : '100%',
												readOnly : true,
												name : 'txtWeb',
												maxLength : 45,
												vtype : 'url',
												id : 'txtWeb'
											}]
								}]
					}, {
						xtype : 'panel',
						title : 'Contacto',
						items : [{
									xtype : 'fieldset',
									title : 'Información de contacto',
									items : [{
												xtype : 'textfield',
												fieldLabel : 'Representante',
												anchor : '100%',
												readOnly : true,
												width : 377,
												name : 'txtRepresentante',
												maxLength : 45,
												allowBlank : false,
												id : 'txtRepresentante'
											}, {
												xtype : 'textfield',
												fieldLabel : 'Cargo',
												anchor : '100%',
												readOnly : true,
												maxLength : 45,
												name : 'txtCargo',
												allowBlank : false,
												id : 'txtCargo'
											}, {
												xtype : 'textfield',
												fieldLabel : 'Correo electrónico',
												anchor : '100%',
												name : 'txtCorreo',
												readOnly : true,
												maxLength : 40,
												allowBlank : false,
												vtype : 'email',
												id : 'txtCorreo'
											}]
								}]
					}

			]
		}];
		frmVerEmpresaUi.superclass.initComponent.call(this);
	}
});
