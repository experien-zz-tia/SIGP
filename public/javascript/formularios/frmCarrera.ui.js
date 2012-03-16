frmCarreraUi = Ext.extend(Ext.Window, {
	title : 'Carrera',
	width : 500,
	height : 280,
	layout : 'form',
	id : 'frmCarreraWin',
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
			url : '/SIGP/configuracion/registrarCarrera',
			id : 'frmCarreraForm',
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
									anchor : '100%',
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
									xtype : 'combo',
									anchor : '50%',
									name : 'cmbRegimen',
									fieldLabel : 'Regimen*',
									id : 'cmbRegimen',
									store : 'stRegimen',
									editable : false,
									displayField : 'nombre',
									valueField : 'id',
									emptyText : '-Seleccione-',
									triggerAction : 'all',
									allowBlank : false,
									forceSelection : true,
									submitValue : false,
									loadingText : 'Cargando...',
									blankText : 'Seleccione el Regimen'
								}, {
									xtype : 'combo',
									anchor : '75%',
									name : 'cmbPlan',
									fieldLabel : 'Plan*',
									id : 'cmbPlan',
									store : 'stPlan',
									editable : false,
									displayField : 'nombre',
									valueField : 'id',
									emptyText : '-Seleccione-',
									triggerAction : 'all',
									allowBlank : false,
									forceSelection : true,
									submitValue : false,
									loadingText : 'Cargando...',
									blankText : 'Seleccione el Plan de Regimen'
								}, {
									xtype : 'combo',
									anchor : '35%',
									name : 'cmbDuracion',
									fieldLabel : 'Duraci&oacute;n*',
									id : 'cmbDuracion',
									store : 'stDuracion',
									editable : false,
									displayField : 'nombre',
									valueField : 'id',
									emptyText : '--',
									triggerAction : 'all',
									allowBlank : false,
									forceSelection : true,
									submitValue : false,
									loadingText : 'Cargando...',
									blankText : 'Seleccione la Duraci&oacute;n'
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
		frmCarreraUi.superclass.initComponent.call(this);
	}
});
