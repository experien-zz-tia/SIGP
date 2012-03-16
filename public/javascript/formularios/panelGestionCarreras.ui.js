panelGestionCarrerasUi = Ext.extend(Ext.Panel, {
			width : 741,
			height : 420,
			id : 'panelGestionCarreras',
			initComponent : function() {
				this.items = [{
					xtype : 'grid',
					title : 'Gestor de Carreras',
					store : 'stCarreras',
					height : 418,
					titleCollapse : true,
					stripeRows : true,
					width : 739,
					loadMask : true,
					maskDisabled : false,
					id : 'gridGestionCarreras',
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
								width : 200,
								editable : false,
								dataIndex : 'nombre',
								tooltip : 'Nombre de la carrera.'
							},{
								xtype : 'gridcolumn',
								header : 'Decanato',
								sortable : true,
								width : 200,
								editable : false,
								dataIndex : 'decanato',
								tooltip : 'Decanato al que se asocia'
							}, 
							{
								xtype : 'gridcolumn',
								header : 'Regimen',
								sortable : true,
								width : 100,
								editable : false,
								dataIndex : 'regimen',
								tooltip : 'Regimen asociado'
							},{
								xtype : 'gridcolumn',
								header : 'Plan',
								sortable : true,
								width : 100,
								editable : false,
								dataIndex : 'plan',
								tooltip : 'Plan asociado'
							}, {
								xtype : 'gridcolumn',
								header : 'Duraci&oacute;n',
								sortable : true,
								width : 90,
								editable : false,
								dataIndex : 'duracion',
								tooltip : 'Duraci&oacute;n'
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
							name : 'cmbDecanatoCarr',
							fieldLabel : 'Decanato*',
							id : 'cmbDecanatoCarr',
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
								store : 'stCarreras',
								displayInfo : true,
								pageSize : 10,
								id : 'ptbCarreras'
							}, {
								xtype : 'tbfill'
							}, 'B&uacute;squeda: ', ' ',
							new Ext.ux.form.SearchField({
										store : stCarreras,
										width : 150,
										emptyText : 'Ingrese una Carrera...'
									})]
				}];
				panelGestionCarrerasUi.superclass.initComponent.call(this);
			}
		});
