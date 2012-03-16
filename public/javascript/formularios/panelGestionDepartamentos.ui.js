panelGestionDepartamentosUi = Ext.extend(Ext.Panel, {
			width : 741,
			height : 420,
			id : 'panelGestionDepartamentos',
			initComponent : function() {
				this.items = [{
					xtype : 'grid',
					title : 'Gestor de Departamentos',
					store : 'stDepartamentos',
					height : 418,
					titleCollapse : true,
					stripeRows : true,
					width : 739,
					loadMask : true,
					maskDisabled : false,
					id : 'gridGestionDepartamentos',
					columns : [new Ext.grid.RowNumberer(), {
								xtype : 'numbercolumn',
								header : 'Id',
								sortable : true,
								width : 50,
								align : 'right',
								editable : false,
								dataIndex : 'id',
								hidden : true,
								hideable : false,
								format : '0',
								id : 'id'
							}, {
								xtype : 'gridcolumn',
								header : 'Nombre',
								sortable : true,
								width : 350,
								editable : false,
								dataIndex : 'descripcion',
								tooltip : 'Nombre del Departamento.'
							},{
								xtype : 'gridcolumn',
								header : 'Decanato',
								sortable : true,
								width : 350,
								editable : false,
								dataIndex : 'decanato',
								tooltip : 'Decanato al que se asocia'
							}],
					tbar : {
						xtype : 'toolbar',
						id : 'tbOpciones',
						items : [ {
						    xtype: 'button',
						    text: 'Agregar',
						    iconCls: 'sigp-agregar',
						    id: 'btnAgregar'
						},
						{
						    xtype: 'button',
						    text: 'Modificar',
						    iconCls: 'sigp-modificar',
						    id: 'btnModificar'
						},
						{
						    xtype: 'button',
						    text: 'Eliminar',
						    iconCls: 'sigp-eliminar',
						    id: 'btnEliminar'
						}, {
							xtype : 'button',
							text : 'Limpiar Filtro',
							iconCls : 'sigp-limpiar',
							id : 'btnLimpiarFiltro'
						},'Decanato: ',{
							xtype : 'combo',
//							x : 130,
//							y : 65,
							name : 'cmbDecanatoDep',
							fieldLabel : 'Decanato*',
							id : 'cmbDecanatoDep',
							store : 'stDecanato',
							editable : false,
							displayField : 'nombre',
							valueField : 'id',
							emptyText : '-Todos-',
							triggerAction : 'all',
							allowBlank : false,
							forceSelection : true,
							submitValue : false,
							loadingText : 'Cargando...',
							blankText : 'Seleccione un Decanato'
						}]
					},

					bbar : [{
								xtype : 'paging',
								store : 'stDepartamentos',
								displayInfo : true,
								pageSize : 10,
								id : 'ptbDepartamentos'
							}, {
								xtype : 'tbfill'
							}, 'B&uacute;squeda: ', ' ',
							new Ext.ux.form.SearchField({
										store : stDepartamentos,
										width : 150,
										emptyText : 'Ingrese un Departamento...'
									})]
				}];
				panelGestionDepartamentosUi.superclass.initComponent.call(this);
			}
		});
