frmReporteMaestrosUi = Ext.extend(Ext.Panel, {
			width : 400,
			height : 320,
			title : 'Configuraci&oacute;n Reportes Maestros',
			id : 'panelReporteMaestros',
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
						}, {
							xtype : 'panel',
							layout : 'form',
							labelWidth : 200,
							title : 'Empresas',
							iconCls : 'sigp-user',
							collapsible : true,
							items : [

							{
										xtype : 'container',
										layout : 'hbox',
										id : 'contenedorBtnEmpresa',
										layoutConfig : {
											pack : 'end'
										},
										items : [{
													xtype : 'button',
													text : 'PDF',
													type : 'submit',
													iconCls : 'sigp-pdf',
													id : 'btnEmpresa'
												},
												{
						xtype : 'button',
						text : 'Limpiar',
						iconCls : 'sigp-limpiar',
						id : 'btnLimpiarEmpresa'
					}]
									}]
						}, {
							xtype : 'panel',
							layout : 'form',
							labelWidth : 200,
							title : 'Ofertas',
							iconCls : 'sigp-user',
							collapsible : true,
							items : [
							 {
					xtype : 'datefield',
					fieldLabel : 'Fecha de Inicio',
					anchor : '100%',
					editable : false,
					allowBlank : false,
					id : 'dateFechaInicioEst',
					vtype : 'dateRange',
					endDateField : 'dateFechaCulminacionEst',
					format : 'd/m/Y'
				}, {
					xtype : 'datefield',
					fieldLabel : 'Fecha de culminaci&oacute;n',
					anchor : '100%',
					editable : false,
					allowBlank : false,
					vtype : 'dateRange',
					startDateField : 'dateFechaInicioEst',
					id : 'dateFechaCulminacionEst',
					format : 'd/m/Y'
				},

							{
										xtype : 'container',
										layout : 'hbox',
										id : 'contenedorBtnOfertas',
										layoutConfig : {
											pack : 'end'
										},
										items : [{
													xtype : 'button',
													text : 'PDF',
													type : 'submit',
													iconCls : 'sigp-pdf',
													id : 'btnOferta'
												},
												{
						xtype : 'button',
						text : 'Limpiar',
						iconCls : 'sigp-limpiar',
						id : 'btnLimpiarOferta'
					}]
									}]
						}, {
							xtype : 'panel',
							layout : 'form',
							labelWidth : 200,
							title : 'Tutores ',
							iconCls : 'sigp-user',
							collapsible : true,
							items : [
							 {
					xtype : 'combo',
					fieldLabel : 'Tipo',
					anchor : '100%',
					editable : false,
					store : 'stTipoTutor',
					displayField : 'nombre',
					valueField : 'id',
					triggerAction : 'all',
					allowBlank : false,
					forceSelection : true,
					submitValue : false,
					emptyText : '-Seleccione-',
					mode : 'local',
					id : 'cmbTipo'
				},

							{
										xtype : 'container',
										layout : 'hbox',
										id : 'contenedorBtnTutor',
										layoutConfig : {
											pack : 'end'
										},
										items : [{
													xtype : 'button',
													text : 'PDF',
													type : 'submit',
													iconCls : 'sigp-pdf',
													id : 'btnTutor'
												},
												{
						xtype : 'button',
						text : 'Limpiar',
						iconCls : 'sigp-limpiar',
						id : 'btnLimpiarTutor'
					}]
									}]
						}

				];
				frmReporteMaestrosUi.superclass.initComponent.call(this);
			}
		});
