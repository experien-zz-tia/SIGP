frmActualizarTutorEmpresarialUi = Ext.extend(Ext.Panel, {
	title : 'Tutor Empresarial',
	width : 448,
	height : 361,
	layout : 'absolute',
	resizable : false,
	activeItem : 1,
	id : 'frmActualizarTutorEmpresarial',

	initComponent : function() {
		this.items = [ {
			xtype : 'form',
			layout : 'absolute',
			width : 440,
			height : 330,

			title : 'Formulario de Registro',
			headerAsText : false,
			unstyled : true,
			method : 'POST',
			waitTitle : 'Por favor espere...',
			url : '/SIGP/tutorEmpresarial/registrar',
			fieldLabel : '',
			id : 'formActTutorEmpresarial',

			items : [ {
				xtype : 'fieldset',
				title : 'Información del Tutor',
				layout : 'absolute',
				height : 310,
				x : 2,
				y : 10,
				width : 438,
				items : [ {
					xtype : 'label',
					text : 'Cédula*:',
					x : 5,
					y : 10,
					width : 75
				},
				{
					xtype : 'label',
					text : 'Nombre(s)*:',
					x : 5,
					y : 40,
					width : 75
				},
				{
					xtype : 'label',
					text : 'Apellido(s)*:',
					x : 5,
					y : 70,
					width : 75
				},
				{
					xtype : 'label',
					text : 'Cargo*:',
					x : 5,
					y : 100,
					width : 75
				},
				{
					xtype : 'label',
					text : 'Correo eletrónico*:',
					x : 5,
					y : 130,
					width : 75
				},
				{
					xtype : 'label',
					text : 'Repetir correo*:',
					x : 5,
					y : 160,
					width : 95
				},
				{
					xtype : 'label',
					text : 'Teléfono:',
					x : 5,
					y : 190,
					width : 75
				},{
					xtype : 'textfield',
					x : 105,
					y : 10,
					width : 145,
					allowBlank : false,
					id : 'txtCedula',
					vtype : 'soloNumero'
				}, {
					xtype : 'textfield',
					x : 105,
					width : 255,
					y : 40,

					maxLength : 45,
					allowBlank : false,
					id : 'txtNombre'
				}, {
					xtype : 'textfield',
					width : 255,
					x : 105,
					y : 70,

					maxLength : 45,
					allowBlank : false,
					id : 'txtApellido'
				}, {
					xtype : 'textfield',
					maxLength : 45,
					x : 105,
					y : 100,
					width : 305,
					allowBlank : false,
					id : 'txtCargo'
				}, {
					xtype : 'textfield',
					x : 105,
					y : 130,
					width : 305,
					maxLength : 40,
					allowBlank : false,
					vtype : 'email',
					id : 'txtCorreo'
				}, {
					xtype : 'textfield',
					x : 105,
					y : 160,
					width : 305,
					maxLength : 40,
					allowBlank : false,
					vtype : 'emailIguales',
					campoInicial : 'txtCorreo',
					id : 'txtCorreoRepetir'
				}, {
					xtype : 'textfield',
					name : 'txtTelefono',
					x : 105,
					y : 190,
					width : 145,
					maxLength : 12,
					vtype : 'soloNumero',
					id : 'txtTelefono'
				}, {
					xtype : 'textfield',
					id : 'txtIdTutorEHidden',
					hidden : true
				}, {
					xtype : 'button',
					text : 'Guardar',
					x : 220,
					y : 230,
					width : 90,
					height : 30,
					iconCls : 'sigp-guardar',
					id : 'btnGuardar'
				}, {
					xtype : 'button',
					text : 'Salir',
					x : 310,
					y : 230,
					width : 90,
					height : 30,
					iconCls : 'sigp-salir',
					id : 'btnSalir'
				} ]
			} ]
		} ];
		frmActualizarTutorEmpresarialUi.superclass.initComponent.call(this);
	}
});
