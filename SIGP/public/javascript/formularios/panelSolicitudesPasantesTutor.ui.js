panelSolicitudesPasantesTutorUi = Ext.extend(Ext.Panel, {
	width: 741,
	height: 300,
	id: 'panelSolicitudesPasantesTutor',
	initComponent: function() {
		this.items = [{
			xtype: 'grid',
			title: 'Solicitudes',
			store: 'stSolicitudesPasanteTutor',
			height: 300,
			titleCollapse: true,
			stripeRows: true,
			width: 739,
			loadMask: true,
			maskDisabled: false,
			id: 'gridSolicitudes',
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
			},
			{
				xtype: 'numbercolumn',
				header: 'Id Pasante',
				sortable: true,
				width: 50,
				align: 'right',
				editable: false,
				dataIndex: 'idPasante',
				hidden: true,
				hideable: false,
				format: '0'
			},{
				xtype: 'datecolumn',
				dataIndex: 'fchSolicitud',
				header: 'Fecha solicitud',
				sortable: true,
				width: 100,
				editable: false,
				tooltip: 'Fecha de solicitud',
				format:'d/m/Y'
			},{
				xtype: 'gridcolumn',
				header: 'C&eacute;dula',
				sortable: true,
				width: 100,
				editable: false,
				dataIndex: 'cedula',
				tooltip: 'C&eacute;dula del pasante.'
			}
			,{
				xtype: 'gridcolumn',
				header: 'Nombre',
				sortable: true,
				width: 170,
				editable: false,
				dataIndex: 'nombre',
				tooltip: 'Nombres del pasante.'
			},{
				xtype: 'gridcolumn',
				header: 'Apellido',
				sortable: true,
				width: 170,
				editable: false,
				dataIndex: 'apellido',
				tooltip: 'Apellidos del pasante.'
			},{
				xtype: 'gridcolumn',
				header: 'Carrera',
				sortable: true,
				width: 180,
				editable: false,
				dataIndex: 'carrera',
				tooltip: 'Carerra que cursa el pasante.'
			} 
			],
				tbar: {
				xtype: 'toolbar',
				id: 'tbOpciones',
				items: [{
					xtype: 'button',
					text: 'Ver perfil pasante',
					iconCls: 'sigp-perfil',
					id: 'btnVerPerfil'
				},
				{
					xtype: 'button',
					text: 'Aceptar',
					iconCls: 'sigp-aceptar',
					id: 'btnAceptar'
				},
				{
					xtype: 'button',
					text: 'Rechazar',
					iconCls: 'sigp-cancelar',
					id: 'btnRechazar'
				}
				]
			},

			bbar: {
				xtype: 'paging',
				store: 'stSolicitudesPasanteTutor',
				displayInfo: true,
				pageSize: 10,
				id: 'ptbSolicitudes'
			}
		}
		];
		panelSolicitudesPasantesTutorUi.superclass.initComponent.call(this);
	}
});