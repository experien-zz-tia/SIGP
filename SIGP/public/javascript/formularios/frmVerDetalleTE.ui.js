frmVerDetalleTEUi = Ext.extend(Ext.Window, {
	title : 'Detalle Tutor Empresarial',
	width : 500,
	height : 240,
	layout : 'form',
	id : 'frmVerDetalleTEWin',
	modal : true,
	resizable : false,
	initComponent : function() {
		this.items = [{

					xtype : 'fieldset',
					title : 'Informaci&oacute;n b&aacute;sica',
					autoHeight : true,
					items : [{
								xtype : 'textfield',
								fieldLabel : 'Empresa',
								anchor : '100%',
								allowBlank : false,
								readOnly : true,
								id : 'txtRazonSocial'
							}, {
								xtype : 'textfield',
								width : 320,
								anchor : '100%',
								readOnly : true,
								fieldLabel : 'Nombre y Apellido',
								allowBlank : false,
								id : 'txtNombreApellido'
							}, {
								xtype : 'textfield',
								width : 284,
								anchor : '100%',
								readOnly : true,
								fieldLabel : 'Correo electronico',
								allowBlank : false,
								id : 'txtCorreo'
							}, {
								xtype : 'textfield',
								fieldLabel : 'Tel&eacute;fono',
								anchor : '50%',
								readOnly : true,
								allowBlank : false,
								id : 'txtTelefono'
							}]

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
		frmVerDetalleTEUi.superclass.initComponent.call(this);
	}
});
