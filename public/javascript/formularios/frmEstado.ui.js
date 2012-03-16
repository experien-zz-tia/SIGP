frmEstadoUi = Ext.extend(Ext.Window, {
	title : 'Carrera',
	width : 500,
	height : 150,
	layout : 'form',
	id : 'frmEstadoWin',
	modal : true,
	resizable : false,
	initComponent : function() {
		this.items = [{
			xtype : 'form',
			title : 'Formulario de registro',
			headerAsText : false,
			unstyled : true,
			method : 'POST',
			waitTitle : 'Por favor espere...',
			url : '/SIGP/configuracion/registrarEstado',
			id : 'frmEstadoForm',
			items : [{

						xtype : 'fieldset',
						title : 'Informaci&oacute;n b&aacute;sica',
						autoHeight : true,
						items : [
								{
									xtype : 'textfield',
									maxLength : 150,
									anchor : '100%',
									fieldLabel : 'Nombre*',
									allowBlank : false,
									id : 'txtDescripcion'
									
								},
								{
									xtype : 'textfield',
									id : 'txtId',
									hidden : true
								}]

					}]
		}, {
			xtype : 'label',
			text : 'Campos marcados con * son obligatorios.'
		}, {
			xtype : 'container',
			layout : 'hbox',
			id : 'contenedorBtns',
			layoutConfig : {
				pack : 'end'
			},
			items : [{
						xtype : 'button',
						text : 'Registrar',
						type : 'submit',
						width: 90,
                        height: 30,
						iconCls : 'sigp-guardar',
						id : 'btnRegistrar'
					}, {
						xtype : 'button',
						text : 'Actualizar',
						width: 90,
                        height: 30,
                        hidden : true,
						iconCls : 'sigp-publicar',
						id : 'btnActualizar'
					}, {
						xtype : 'button',
						text : 'Limpiar',
						width: 90,
                        height: 30,
						iconCls : 'sigp-limpiar',
						id : 'btnLimpiar'
					}, {
						xtype : 'button',
						text : 'Salir',
						width: 90,
                        height: 30,
						iconCls : 'sigp-salir',
						id : 'btnSalir'
					}]
		}];
		frmEstadoUi.superclass.initComponent.call(this);
	}
});
