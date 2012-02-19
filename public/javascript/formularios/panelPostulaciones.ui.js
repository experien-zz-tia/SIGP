panelPostulacionesUi = Ext.extend(Ext.Panel, {
    width: 741,
    height: 420,
    id: 'panelPostulaciones',
    initComponent: function() {
        this.items = [
            {
                xtype: 'grid',
                title: 'Postulaciones',
                store: 'stgPostulaciones',
                height: 418,
                titleCollapse: true,
                stripeRows: true,
                titleCollapse: true,
                width: 739,
				loadMask: true,
                maskDisabled: false,
                id: 'gridPostulaciones',
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
                    },
                    {
                        xtype: 'numbercolumn',
                        header: 'IdPasante',
                        sortable: true,
                        width: 50,
                        align: 'right',
                        editable: false,
                        dataIndex: 'idPasante',
                        hidden: true,
                        hideable: false,
                        format: '0'                        
                    },
                      {
                        dataIndex: 'titulo',
                        header: 'T&iacute;tulo',
                        groupable   : true,  
                        sortable: true,
                        width: 200,
                        editable: false,
                        tooltip: 'T&iacute;tulo de la oferta.'
                    },
                     {
                        xtype: 'gridcolumn',
                        header: 'C&eacute;dula',
                        sortable: true,
                        width: 100,
                        dataIndex: 'cedula',
                        editable: false,
                        tooltip: 'C&eacute;dula del postulante.'
                    },
                        {
                        xtype: 'gridcolumn',
                        header: 'Nombre',
                        sortable: true,
                        width: 180,
                        dataIndex: 'nombre',
                        editable: false,
                        tooltip: 'Nombres del postulante.'
                    },
                        {
                        xtype: 'gridcolumn',
                        header: 'Apellido',
                        sortable: true,
                        width: 180,
                        dataIndex: 'apellido',
                        editable: false,
                        tooltip: 'Apellidos del postulante.'
                    },
                     {
                        xtype: 'gridcolumn',
                        header: 'Carrera',
                        sortable: true,
                        gruopable: true,
                        width: 180,
                        dataIndex: 'carrera',
                        editable: false,
                        tooltip: 'Carrera que cursa el postulante.'
                    },
               	 	{
                        xtype: 'datecolumn',
                        header: 'Fecha Postulaci&oacute;n',
                        sortable: true,
                        width: 120,
                        editable: false,
                        dataIndex: 'fchPostulacion',
                        tooltip: 'Fecha de Postulac&oacute;n  en la oferta.',
                        format:'d/m/Y'
                    }                          
                ],
				view : new Ext.grid.GroupingView({  
     		        forceFit            : true,  
    	    		ShowGroupName       : true,  
	        		enableNoGroup       : false,  
        			enableGropingMenu   : false,  
        			hideGroupedColumn   : true  
    		}),
                tbar: {
                    xtype: 'toolbar',
                    id: 'tbOpciones',
                    items: [
                   	 	{
                            xtype: 'button',
                            text: 'Ver detalle postulante',
                            iconCls: 'sigp-perfil',
                            id: 'btnVerDetallePostulante'
                        },
                        {
                            xtype: 'button',
                            text: 'Aceptar',
                            iconCls: 'sigp-aceptar',
                            id: 'btnAceptarP'
                        },
                        {
                            xtype: 'button',
                            text: 'Rechazar',
                            iconCls: 'sigp-eliminar',
                            id: 'btnRechazar'
                        },
                        {
                            xtype: 'button',
                            text: 'Refrescar',
                            iconCls: 'sigp-refrescar',
                            id: 'btnRefrescar'
                        }                            
                    ]
                }
            }
        ];
        panelPostulacionesUi.superclass.initComponent.call(this);
    }
});
