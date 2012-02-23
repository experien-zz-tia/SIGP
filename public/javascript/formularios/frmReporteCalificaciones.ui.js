frmReporteCalificacionesUi = Ext.extend(Ext.Panel, {
			width : 400,
			height : 320,
			title : 'Configuraci&oacute;n Reporte de Calificaciones',
			id : 'panelReporte',
			layout : 'form',
			autoScroll : true,
			initComponent : function() {
				this.items = [{
							xtype : 'panel',
							layout : 'form',
							labelWidth : 200,
							title : 'Pasantes',
							iconCls : 'sigp-user',
							collapsible : true,
							items : [{
										xtype : 'combo',
										editable : false,
										fieldLabel : 'Carrera',
										store : 'stCarrera',
										displayField : 'nombre',
										valueField : 'id',
										emptyText : '-TODAS-',
										triggerAction : 'all',
										allowBlank : true,
										forceSelection : true,
										loadingText : 'Cargando...',
										blankText : 'Seleccione una carrera.',
										submitValue : false,
										id : 'cmbCarrera'
									}, {
										xtype : 'container',
										layout : 'hbox',
										id : 'contenedorBtnsPasante',
										layoutConfig : {
											pack : 'end'
										},
										items : [{
													xtype : 'button',
													text : 'PDF',
													type : 'submit',
													iconCls : 'sigp-pdf',
													id : 'btnPasante'
												},
												{
						xtype : 'button',
						text : 'Limpiar',
						iconCls : 'sigp-limpiar',
						id : 'btnLimpiarPasante'
					}]
									}]
						}]
						
				frmReporteCalificacionesUi.superclass.initComponent.call(this);
			}
		});
