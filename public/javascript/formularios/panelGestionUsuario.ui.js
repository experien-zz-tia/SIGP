panelGestionUsuarioUi = Ext.extend(Ext.Panel, {
			width : 741,
			height : 420,
			id : 'panelGestionUsuario',
			initComponent : function() {
				this.items = [{
					xtype : 'grid',
					title : 'Gestor de Usuarios',
					store : 'stUsuarios',
					height : 418,
					titleCollapse : true,
					stripeRows : true,
					width : 739,
					loadMask : true,
					maskDisabled : false,
					id : 'gridGestionUsuario',
					columns : [new Ext.grid.RowNumberer(), {
								xtype : 'numbercolumn',
								header : 'Id',
								sortable : true,
								width : 50,
								align : 'right',
								editable : false,
								dataIndex : 'usuarioId',
								hidden : true,
								hideable : false,
								format : '0',
								id : 'id'
							},
							 {
								xtype : 'gridcolumn',
								header : 'Nombre de usuario',
								sortable : true,
								width : 160,
								editable : false,
								dataIndex : 'nombreUsuario',
								tooltip : 'Nombre de usuario.'
							}, {
								xtype : 'gridcolumn',
								header : 'Categoria',
								sortable : true,
								width : 160,
								editable : false,
								dataIndex : 'categoria',
								tooltip : 'Categoria del usuario.'
							}
							, {
								xtype : 'gridcolumn',
								header : 'Estatus.',
								sortable : true,
								width : 155,
								editable : false,
								dataIndex : 'estatus',
								tooltip : 'Estatus'
							}],
					tbar : {
						xtype : 'toolbar',
						id : 'tbOpciones',
						items : [
						{
						    xtype: 'button',
						    text: 'Reasignar Clave',
						    iconCls: 'sigp-modificar',
						    id: 'btnReset'
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
								store : 'stUsuarios',
								displayInfo : true,
								pageSize : 10,
								id : 'ptbUsuarios'
							}]
				}];
				panelGestionUsuarioUi.superclass.initComponent.call(this);
			}
		});
