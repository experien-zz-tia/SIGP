/**
 * Formulario para el resumen informativo de las empresas
 */

frmEmpresaResumenUi = Ext.extend(Ext.Window, {
	title: 'Resumen Empresa',
	width: 674,
	height: 545,
	modal: true,
	resizable : false,
	id: 'frmEmpresaResumenWin',
	initComponent: function() {
		this.items = [{
				xtype: 'fieldset',
				title: 'Informaci&oacute;n Empresa',
				width: 654,
				items: [{
					xtype: 'textfield',
					fieldLabel: 'R.I.F.',
					anchor: '100%',
					maxLength: 40,
					allowBlank: false,
					readOnly:true,
					id: 'txtRif'
				},{
					xtype: 'textfield',
					fieldLabel: 'Raz&oacute;n Social',
					anchor: '100%',
					maxLength: 40,
					allowBlank: false,
					readOnly:true,
					id: 'txtRazonSocial'
				},{
					xtype: 'textfield',
					fieldLabel: 'Contacto',
					anchor: '100%',
					maxLength: 40,
					allowBlank: false,
					readOnly:true,
					id: 'txtContacto'
				}
			 ,{
				xtype: 'grid',
				title: 'Ofertas',
				store: 'stOfertasEmpresa',
				height: 200,
				titleCollapse: true,
				stripeRows: true,
				width: 635,
				loadMask: true,
				maskDisabled: false,
				id: 'gridOfertasEmpresa',
				columns: [
				new Ext.grid.RowNumberer(),
				{
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
					header: 'Fecha Creaci&oacute;n',
					sortable: true,
					width: 100,
					editable: false,
					dataIndex: 'fchCreacion',
					tooltip: 'Fecha de creaci&oacute;n de la oferta',
					format:'d/m/Y'
				},{
					xtype: 'gridcolumn',
					dataIndex: 'titulo',
					header: 'T&iacute;tulo',
					sortable: true,
					width: 200,
					editable: false,
					tooltip: 'T&iacute;tulo de la oferta'
				},{
					xtype: 'datecolumn',
					header: 'Fecha Publicaci&oacute;n',
					sortable: true,
					width: 100,
					editable: false,
					dataIndex: 'fchPublicacion',
					tooltip: 'Fecha de Publicaci&oacute;n de la oferta',
					format:'d/m/Y'
				},{
					xtype: 'datecolumn',
					dataIndex: 'fchCierre',
					header: 'Fecha Cierre',
					sortable: true,
					width: 100,
					editable: false,
					tooltip: 'Fecha de cierre de la oferta',
					format:'d/m/Y'
				},{
					xtype: 'numbercolumn',
					dataIndex: 'vacantes',
					header: 'Vacantes',
					sortable: true,
					width: 75,
					align: 'right',
					editable: false,
					tooltip: 'N&uacute;mero de puestos disponibles para la oferta',
					format: '0',
					id: ''
				},{
					xtype: 'numbercolumn',
					dataIndex: 'postulados',
					header: 'Postulados',
					sortable: true,
					width: 80,
					align: 'right',
					editable: false,
					tooltip: 'N&uacute;mero de postulados a la oferta',
					format: '0'
				},{
					xtype: 'gridcolumn',
					header: '&Aacute;rea',
					sortable: true,
					width: 180,
					dataIndex: 'area',
					editable: false,
					tooltip: '&Aacute;rea de la oferta'
				}
				],
			bbar: {
					xtype: 'paging',
					store: 'stOfertasEmpresa',
					displayInfo: true,
					pageSize: 5,
					id: 'ptbOfertas'
				}
			},
			{
				xtype: 'grid',
				title: 'Pasantias activas',
				store: 'stPasantias',
				height: 200,
				titleCollapse: true,
				stripeRows: true,
				width: 635,
				loadMask: true,
				maskDisabled: false,
				id: 'gridPasantias',
				columns: [
				new Ext.grid.RowNumberer(),
				{
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
					xtype: 'gridcolumn',
					header: 'T&iacute;tulo',
					sortable: true,
					width: 200,
					editable: false,
					dataIndex: 'titulo',
					tooltip: 'T&iacute;tulo de la oferta asociada a la pasant&iacute;a.'
				},{
					xtype: 'gridcolumn',
					header: 'C&eacute;dula',
					sortable: true,
					width: 80,
					editable: false,
					dataIndex: 'cedula',
					tooltip: 'C&eacute;dula del pasante.'
				},{
					xtype: 'gridcolumn',
					header: 'Nombres',
					sortable: true,
					width: 160,
					editable: false,
					dataIndex: 'nombre',
					tooltip:'Nombres del pasante.'
				},{
					xtype: 'gridcolumn',
					dataIndex: 'apellido',
					header: 'Apellidos',
					sortable: true,
					width: 160,
					editable: false,
					tooltip: 'Apellidos del pasante.'
				},{
					xtype: 'datecolumn',
					header: 'Fecha Inicio',
					sortable: true,
					width: 100,
					editable: false,
					dataIndex: 'fchInicioEst',
					tooltip: 'Fecha de Inicio estimada.',
					format:'d/m/Y'
				},{
					xtype: 'datecolumn',
					header: 'Fecha Fin',
					sortable: true,
					width: 100,
					editable: false,
					dataIndex: 'fchFinEst',
					tooltip: 'Fecha de Finalizaci&oacute;n estimada.',
					format:'d/m/Y'
				},{
					xtype: 'gridcolumn',
					dataIndex: 'modalidad',
					header: 'Modalidad',
					sortable: true,
					width: 80,
					editable: false,
					tooltip: 'Modalidad de la pasantia.'
				},{
					xtype: 'gridcolumn',
					dataIndex: 'tipoPasantia',
					header: 'Tipo',
					sortable: true,
					width: 80,
					editable: false,
					tooltip: 'Tipo de la pasantia.'
				}
				],
				bbar: {
					xtype: 'paging',
					store: 'stPasantias',
					displayInfo: true,
					pageSize: 10,
					id: 'ptbPasantias'
				}
			}
			]
		} ];
		frmEmpresaResumenUi.superclass.initComponent.call(this);
	}
});