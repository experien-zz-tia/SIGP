panelGestionEstadosUi = Ext.extend(Ext.Panel, {
	width: 400,
			height : 420,
			id : 'panelGestionEstados',
			initComponent : function() {
				this.items = [{
					xtype : 'grid',
					title : 'Gestor de Tipos de Pasant&iacute;a',
					store : 'stEstado',
					height : 418,
					titleCollapse : true,
					stripeRows : true,
					width: 398,
					loadMask : true,
					maskDisabled : false,
					id : 'gridGestionEstados',
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
								dataIndex : 'nombre',
								tooltip : 'Nombre del Estado de Venezuela.'
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
						}]
					}
				,
	                bbar: {
	                    xtype: 'paging',
	                    store: 'stEstado',
	                    displayInfo: true,
	                    pageSize: 10,
	                    id: 'ptbEstado'
	                }
				}];
				panelGestionEstadosUi.superclass.initComponent.call(this);
			}
		});
