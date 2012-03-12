frmCoordinacionUi = Ext.extend(Ext.Window, {
	title : 'Coordinacion',
	width : 500,
	height : 280,
	layout : 'form',
	id : 'frmCoordinacionWin',
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
			url : '/SIGP/configuracion/registrarCoordinacion',
			id : 'frmCoordinacionForm',
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
									id : 'txtNombre'
									
								},{
									xtype : 'combo',
//									x : 130,
//									y : 65,
									name : 'cmbDecanato',
									fieldLabel : 'Decanato*',
									id : 'cmbDecanato',
									store : 'stDecanato',
									editable : false,
									displayField : 'nombre',
									valueField : 'id',
									emptyText : '-Seleccione-',
									triggerAction : 'all',
									allowBlank : false,
									forceSelection : true,
									submitValue : false,
									loadingText : 'Cargando...',
									blankText : 'Seleccione un Decanato'
								},{
									xtype : 'textfield',
									name : 'txtDireccion',
									anchor : '100%',
									fieldLabel : 'Ubicaci&oacute;n*',
									allowBlank : false,
									maxLength : 250,
									id : 'txtDireccion'
								},
								{
									xtype : 'textfield',
									name : 'txtTelefono',
									anchor : '50%',
									fieldLabel : 'Tel&eacute;fono',
									maxLength : 45,
									id : 'txtTelefono'
								},
								 {
									xtype : 'textfield',
									fieldLabel : 'Email',
									anchor : '100%',
									name : 'txtEmail',
									maxLength : 40,
									id : 'txtEmail'
								},
								{
									xtype : 'combo',
									name : 'cmbEmpleado',
									fieldLabel : 'Coordinador*',
									id : 'cmbEmpleado',
									editable : false,
									store : 'stEmpleadosDec',
									displayField : 'nombre',
									valueField : 'id',
									triggerAction : 'all',
									queryParam : 'idDecanato',
									allowBlank : false,
									loadingText : 'Cargando...',
									forceSelection : true,
									emptyText : '-Seleccione-',
									blankText : 'Seleccione un Coordinador',
									mode : 'local',
									submitValue : false
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
		frmCoordinacionUi.superclass.initComponent.call(this);
	}
});
