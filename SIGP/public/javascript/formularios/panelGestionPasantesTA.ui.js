panelGestionPasantesTAUi = Ext.extend(Ext.Panel, {
			width : 741,
			height : 420,
			id : 'panelGestionPasantes',
			initComponent : function() {
				this.items = [{
					xtype : 'grid',
					title : 'Gestor de Pasantes',
					store : 'stPasantesTA',
					height : 418,
					titleCollapse : true,
					stripeRows : true,
					width : 739,
					loadMask : true,
					maskDisabled : false,
					id : 'gridGestionPasantes',
					columns : [new Ext.grid.RowNumberer(), {
								xtype : 'numbercolumn',
								header : 'Id',
								sortable : true,
								width : 50,
								align : 'right',
								editable : false,
								dataIndex : 'pasanteId',
								hidden : true,
								hideable : false,
								format : '0',
								id : 'idPasante'
							},{
								xtype : 'numbercolumn',
								header : 'Pasantia Id',
								sortable : true,
								width : 50,
								align : 'right',
								editable : false,
								dataIndex : 'pasantiaId',
								hidden : true,
								hideable : false,
								format : '0',
								id : 'pasantiaId'
							},
							{
								xtype : 'gridcolumn',
								header : 'C&eacute;dula',
								sortable : true,
								width : 70,
								editable : false,
								dataIndex : 'cedulaPasante',
								tooltip : 'C&eacute;dula del pasante.'
							}, {
								xtype : 'gridcolumn',
								header : 'Nombre',
								sortable : true,
								width : 160,
								editable : false,
								dataIndex : 'nombrePasante',
								tooltip : 'Nombres del pasante.'
							}, {
								xtype : 'gridcolumn',
								header : 'Apellido',
								sortable : true,
								width : 160,
								editable : false,
								dataIndex : 'apellidoPasante',
								tooltip : 'Apellidos del pasante.'
							}, {
								xtype : 'gridcolumn',
								header : 'Carrera',
								sortable : true,
								width : 160,
								editable : false,
								dataIndex : 'carrera',
								tooltip : 'Carrera que cursa el pasante.'
							}, {
								xtype : 'gridcolumn',
								header : 'Empresa',
								sortable : true,
								width : 155,
								editable : false,
								dataIndex : 'razonSocial',
								tooltip : 'Raz&oacute;n Social de la empresa.'
							}, {
								xtype : 'gridcolumn',
								header : 'Tutor Emp.',
								sortable : true,
								width : 155,
								editable : false,
								dataIndex : 'tutorEmp',
								tooltip : 'Tutor Empresarial.'
							}
							, {
								xtype : 'gridcolumn',
								header : 'Estatus.',
								sortable : true,
								width : 155,
								editable : false,
								dataIndex : 'estatusPasantia',
								tooltip : 'Estatus de la pasant&iacute;a.'
							}],
					tbar : {
						xtype : 'toolbar',
						id : 'tbOpciones',
						items : [ {
									xtype : 'button',
									text : 'Ver perfil',
									iconCls : 'sigp-perfil',
									id : 'btnVerPerfil'
								}, {
									xtype : 'button',
									text : 'Ver pasant&iacute;a',
									iconCls : 'sigp-empresa',
									id : 'btnVerPasantia'
								}, {
									xtype : 'button',
									text : 'Ver Detalle Tutor Empresarial',
									iconCls : 'sigp-user',
									id : 'btnDetalleTutor'
								}, {
									xtype : 'button',
									text : 'Limpiar Filtro',
									iconCls : 'sigp-limpiar',
									id : 'btnLimpiarFiltro'
								}, {
								xtype : 'tbfill'
							},
								'Carrera: ', {
										xtype : 'combo',
										editable : false,
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
						}]
					},

					bbar : [{
								xtype : 'paging',
								store : 'stPasantesTA',
								displayInfo : true,
								pageSize : 10,
								id : 'ptbPasantes'
							}, {
								xtype : 'tbfill'
							}, 'B&uacute;squeda: ', ' ',
							new Ext.ux.form.SearchField({
										store : stPasantesTA,
										width : 150,
										emptyText : 'Ingrese una cedula...'
									})]
				}];
				panelGestionPasantesTAUi.superclass.initComponent.call(this);
			}
		});
