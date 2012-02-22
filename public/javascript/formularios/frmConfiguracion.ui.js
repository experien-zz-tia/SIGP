frmConfiguracionUi = Ext.extend(Ext.Panel, {
	width : 600,
	height : 320,
	title : 'Configuraci&oacute;n General',
	id : 'panelConfiguracion',
	layout : 'form',
	autoScroll:true,
	initComponent : function() {
		this.items = [{
					xtype : 'panel',
					layout : 'form',
					labelWidth : 200,
					title : 'Informaci&oacute;n B&aacute;sica',
					iconCls:'sigp-user',
					collapsible : true,
					items : [{
								xtype : 'textfield',
								fieldLabel : 'Coordinador',
								anchor : '100%',
								allowBlank : false,
								readOnly:true,
								id : 'txtCoordinador'
							},
							{
								xtype : 'textfield',
								fieldLabel : 'C&eacute;dula',
								anchor : '100%',
								allowBlank : false,
								readOnly:true,
								id : 'txtCedula'
							}]
				}, {
					xtype : 'panel',
					layout : 'form',
					collapsible : true,
					labelWidth : 200,
					iconCls:'sigp-config',
					title : 'Configuraci&oacute;n',
					items : [new Ext.form.RadioGroup({
										fieldLabel : 'Inscripciones abiertas?',
										columns : 2,
										id:'radioInscrip',
										items : [{
													boxLabel : 'S&iacute;',
													name : 'inscrip',
													id: 'radioS'
												}, {
													boxLabel : 'No',
													name : 'inscrip',
													id: 'radioN'
												}]
									}),
								new Ext.form.RadioGroup({
										fieldLabel : 'Consulta de Calificaciones?',
										columns : 2,
										id:'radioCalif',
										items : [{
													boxLabel : 'S&iacute;',
													name : 'calif',
													id: 'radioCS'
												}, {
													boxLabel : 'No',
													name : 'calif',
													id: 'radioCN'
												}]
									}),
								new Ext.form.RadioGroup({
										fieldLabel : 'Actualizaci&oacute;n de Calificaciones?',
										columns : 2,
										id:'radioActCalif',
										items : [{
													boxLabel : 'S&iacute;',
													name : 'actCalif',
													id: 'radioACS'
												}, {
													boxLabel : 'No',
													name : 'actCalif',
													id: 'radioACN'
												}]
									}),
									
								{
								xtype : 'numberfield',
								fieldLabel : 'N&uacute;mero m&aacute;ximo de solicitudes a tutor por pasante*',
								anchor : '100%',
								maxValue : 5,
								minValue : 1,
								allowDecimals : false,
								decimalPrecision : 0,
								allowNegative : false,
								allowBlank : false,
								id : 'txtMaxSolicTutor'
							}, {
								xtype : 'numberfield',
								fieldLabel : 'N&uacute;mero m&aacute;ximo de postulaciones simultaneas por pasante*',
								anchor : '100%',
								maxValue : 10,
								minValue : 1,
								allowDecimals : false,
								decimalPrecision : 0,
								allowNegative : false,
								allowBlank : false,
								id : 'txtMaxSolicOferta'
							}, {
								xtype : 'numberfield',
								fieldLabel : 'N&uacute;mero m&aacute;ximo de solicitudes recibidas por tutor*',
								anchor : '100%',
								maxValue : 50,
								minValue : 1,
								allowDecimals : false,
								decimalPrecision : 0,
								allowNegative : false,
								allowBlank : false,
								id : 'txtMaxRecSolicTutor'
							}
							, {
								xtype : 'numberfield',
								fieldLabel : 'N&uacute;mero m&aacute;ximo de mensajes almacenados*',
								anchor : '100%',
								maxValue : 20,
								minValue : 1,
								allowDecimals : false,
								decimalPrecision : 0,
								allowNegative : false,
								allowBlank : false,
								id : 'txtMaxMensajes'
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
								text : 'Guardar',
								width: 90,
                                height: 30,
								type : 'submit',
								iconCls : 'sigp-guardar',
								id : 'btnGuardar'
							}, {
								xtype : 'button',
								width: 90,
                                height: 30,
								text : 'Restablecer',
								iconCls : 'sigp-limpiar',
								id : 'btnReset'
							}]
				}, {
					xtype : 'label',
					text : 'Campos marcados con * son obligatorios.'
				}];
		frmConfiguracionUi.superclass.initComponent.call(this);
	}
});
