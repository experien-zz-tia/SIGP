frmFinalizarLapsoUi = Ext.extend(Ext.Window, {
	title : 'Finalizar Lapso',
	width : 459,
	height : 180,
	modal : true,
	resizable : false,
	id : 'frmFinalizarLapsoWin',
	initComponent : function() {
		this.items = [{
				xtype : 'fieldset',
				title : 'Informaci&oacute;n Lapso Acad&eacute;mico',
				items : [{
					xtype : 'textfield',
					fieldLabel : 'Lapso',
					anchor : '50%',
					allowBlank : false,
					id : 'txtLapso',
					disabled:true
				}, new Ext.form.CheckboxGroup({
					    fieldLabel:'Opciones',  
    					columns:1,  
					items : [{
						boxLabel : 'Omitir pasant&iacute;as sin evaluar.',
						id : 'checkOmitirSE'
					}, 
					{
						boxLabel : 'Enviar a tutores acad&eacute;micos historial de asesror&iacute;as.',
						id : 'checkEnviarNotif'
					}
					]
				}), {
					xtype : 'textfield',
					id : 'txtLapsoId',
					hidden : true
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
					text : 'Aceptar',
					type : 'submit',
					iconCls : 'sigp-aceptar',
					id : 'btnAceptar'
				}, {
					xtype : 'button',
					text : 'Salir',
					iconCls : 'sigp-salir',
					id : 'btnSalir'
				}]
			
		}];
		frmFinalizarLapsoUi.superclass.initComponent.call(this);
	}
});
