panelGestionTiposPasantiaUi = Ext.extend(Ext.Panel, {
			width : 350,
			height : 420,
			id : 'panelGestionTiposPasantia',
			initComponent : function() {
				this.items = [{
					xtype : 'grid',
					title : 'Gestor de Tipos de Pasant&iacute;a',
					store : 'stTipoPasantia',
					height : 418,
					titleCollapse : true,
					stripeRows : true,
					width : 348,
					loadMask : true,
					maskDisabled : false,
					id : 'gridGestionTiposPasantia',
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
								width : 320,
								editable : false,
								dataIndex : 'descripcion',
								tooltip : 'Nombre del Tipo de Pasant&iacute;a.'
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
					},

					bbar : [{
								xtype : 'paging',
								store : 'stTipoPasantia',
								displayInfo : true,
								pageSize : 10,
								id : 'ptbTiposPasantia'
							}]
				}];
				panelGestionTiposPasantiaUi.superclass.initComponent.call(this);
			}
		});
