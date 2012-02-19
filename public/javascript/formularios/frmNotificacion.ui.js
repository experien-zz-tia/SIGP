frmNotificacionUi = Ext.extend(Ext.Window, {
	title : 'Notificaciones',
	width : 674,
	height : 450,
	modal : true,
	resizable : false,
	id : 'frmNotificacion',
	initComponent : function() {
		this.items = [{
			xtype : 'form',
			width : 660,
			id : 'formNotificacion',
			method : 'POST',
			waitTitle : 'Por favor espere...',
			url : '/SIGP/notificacion/enviar',
			items : [{
				xtype : 'fieldset',
				title : 'Notificaciones',
				items : [{
							xtype : 'textfield',
							fieldLabel : 'Para',
							anchor : '100%',
							maxLength : 60,
							allowBlank : false,
							readOnly : true,
							id : 'txtPara'
						}, {
							xtype : 'htmleditor',
							anchor : '100%',
							height : 270,
							fieldLabel : 'Mensaje*',
							width : 572,
							enableSourceEdit : false,
							enableFont : false,
							enableAlignments : false,
							enableColors : false,
							enableFontSize : false,
							enableFormat : false,
							enableLinks : false,
							enableLists : false,
							id : 'txtMensaje'
						}, new Ext.form.CheckboxGroup({
							fieldLabel : 'Opciones',
							columns : 1,
							items : [{
								boxLabel : 'Enviar correo electr&oacute;nioco.',
								id : 'checkEnviarC'
							}]
						}),
						{
							xtype : 'textfield',
							id : 'txtIdPasante',
							hidden : true
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
							text : 'Enviar',
							type : 'submit',
							iconCls : 'sigp-correo',
							id : 'btnEnviar'
						}, {
							xtype : 'button',
							text : 'Limpiar',
							type : 'reset',
							iconCls : 'sigp-limpiar',
							id : 'btnLimpiar'
						}, {
							xtype : 'button',
							text : 'Salir',
							type : 'reset',
							iconCls : 'sigp-salir',
							id : 'btnSalir'
						}]
			}, {
				xtype : 'label',
				text : 'Campos marcados con * son obligatorios.'
			}]
		}];
		frmNotificacionUi.superclass.initComponent.call(this);
	}
});
