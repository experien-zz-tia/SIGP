panelHistorialSolicitudesUi = Ext.extend(Ext.Panel, {
	width: 741,
	height: 300,
	id: 'panelHistorialSolicitudes',
	initComponent: function() {
		this.items = [{
			xtype: 'grid',
			title: 'Historial Solicitudes',
			store: 'stSolicitudes',
			height: 300,
			titleCollapse: true,
			stripeRows: true,
			width: 739,
			loadMask: true,
			maskDisabled: false,
			id: 'gridHistorialSolicitudes',
			columns: [
			new Ext.grid.RowNumberer(),{
				xtype: 'numbercolumn',
				header: 'Id',
				sortable: true,
				width: 50,
				align: 'right',
				editable: false,
				dataIndex: 'id',
				hidden: true,
				hideable: false,
				format: '0',
				id: 'id'
			},{
				xtype: 'datecolumn',
				dataIndex: 'fchSolicitud',
				header: 'Fecha solicitud',
				sortable: true,
				width: 90,
				editable: false,
				tooltip: 'Fecha de solicitud',
				format:'d/m/Y'
			}
			,{
				xtype: 'datecolumn',
				dataIndex: 'fchRespuesta',
				header: 'Fecha respuesta',
				sortable: true,
				width: 90,
				editable: false,
				tooltip: 'Fecha de respuesta de la solicitud',
				format:'d/m/Y'
				
			}
			,{
				xtype: 'gridcolumn',
				header: 'Nombre',
				sortable: true,
				width: 160,
				editable: false,
				dataIndex: 'nombre',
				tooltip: 'Nombres del tutor.'
			},{
				xtype: 'gridcolumn',
				header: 'Apellido',
				sortable: true,
				width: 160,
				editable: false,
				dataIndex: 'apellido',
				tooltip: 'Apellidos del tutor.'
			},{
				xtype: 'gridcolumn',
				header: 'Cargo',
				sortable: true,
				width: 140,
				editable: false,
				dataIndex: 'cargo',
				tooltip: 'Cargo del tutor.'
			} ,{
				xtype: 'gridcolumn',
				header: 'Departamento',
				sortable: true,
				width: 200,
				editable: false,
				dataIndex: 'departamento',
				tooltip: 'Departamento al que est&aacute; adscrito el tutor.'
			},{
				xtype: 'gridcolumn',
				header: 'Estatus',
				sortable: true,
				width: 70,
				editable: false,
				dataIndex: 'estatus',
				tooltip: 'Estatus de la solicitud.'
			}
			],
				tbar: {
				xtype: 'toolbar',
				id: 'tbOpciones',
				items: [{
					xtype: 'button',
					text: 'Cancelar solicitud',
					iconCls: 'sigp-cancelar',
					id: 'btnCancelar'
				}
				]
			},
			bbar: {
				xtype: 'paging',
				store: 'stSolicitudes',
				displayInfo: true,
				pageSize: 10,
				id: 'ptbSolicitudes'
			}
		}
		];
		panelHistorialSolicitudesUi.superclass.initComponent.call(this);
	}
});