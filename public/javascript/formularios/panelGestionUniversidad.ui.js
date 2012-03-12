panelGestionUniversidadUi = Ext.extend(Ext.Panel, {
			width : 741,
			height : 200,
			id : 'panelGestionUniversidad',
			initComponent : function() {
				this.items = [{
					xtype : 'grid',
					title : 'Gestor de Universidad',
					store : 'stUniversidadFull',
					height : 198,
					titleCollapse : true,
					stripeRows : true,
					width : 739,
					loadMask : true,
					maskDisabled : false,
					id : 'gridGestionUniversidad',
					columns : [new Ext.grid.RowNumberer(), 
					           {
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
								width : 160,
								editable : false,
								dataIndex : 'nombre',
								tooltip : 'Nombres del empleado.'
							}, {
								xtype : 'gridcolumn',
								header : 'Tel&eacute;fono',
								sortable : true,
								width : 100,
								editable : false,
								dataIndex : 'telefono',
								tooltip : 'Tel&eacute;fono actual'
							}, {
								xtype : 'gridcolumn',
								header : 'Direcci&oacute;n',
								sortable : true,
								width : 180,
								editable : false,
								dataIndex : 'direccion',
								tooltip : 'Direcci&oacute;n donde se encuentra'
							}, {
								xtype : 'gridcolumn',
								header : 'Ciudad',
								sortable : true,
								width : 100,
								editable : false,
								dataIndex : 'ciudad',
								tooltip : 'Ciudad'
							}, {
								xtype : 'gridcolumn',
								header : 'Estado',
								sortable : true,
								width : 100,
								editable : false,
								dataIndex : 'estado',
								tooltip : 'Estado'
							},{
								xtype : 'gridcolumn',
								header : 'Logo',
								sortable : true,
								width : 150,
								editable : false,
								dataIndex : 'logo',
								tooltip : 'Logo actual'
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
						}]
					},

					bbar : [{
								xtype : 'paging',
								store : 'stUniversidadFull',
								displayInfo : true,
								pageSize : 10,
								id : 'ptbUniversidad'
							}]
				}];
				panelGestionUniversidadUi.superclass.initComponent.call(this);
			}
		});
