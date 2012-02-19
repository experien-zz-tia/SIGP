panelNoticiasUi = Ext.extend(Ext.Panel, {
    width: 741,
    height: 420,
    id: 'panelNoticias',
    initComponent: function() {
        this.items = [
            {
                xtype: 'grid',
                title: 'Noticias',
                store: 'stNoticias',
                height: 418,
                titleCollapse: true,
                stripeRows: true,
                width: 739,
				loadMask: true,
                maskDisabled: false,
                id: 'gridNoticias',
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
                        header: 'Id',
                        sortable: true,
                        width: 50,
                        align: 'right',
                        editable: false,
                        dataIndex: 'empleado_id',
                        hidden: true,
                        hideable: false,
                        format: '0'
                    },
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'titulo',
                        header: 'T&iacute;tulo',
                        sortable: true,
                        width: 200,
                        editable: false,
                        tooltip: 'T&iacute;tulo de la noticia'
                    },
                       {
                        xtype: 'gridcolumn',
                        dataIndex: 'contenido',
                        header: 'Contenido',
                        sortable: true,
                        width: 300,
                        editable: false,
                        tooltip: 'Contenido de la noticia'
                    },
                     {
                        xtype: 'datecolumn',
                        header: 'Fecha Publicaci&oacute;n',
                        sortable: true,
                        width: 100,
                        editable: false,
                        dataIndex: 'fchPublicacion',
                        tooltip: 'Fecha de creaci&oacute;n de la oferta',
                        format:'d/m/Y'
                    },
                       {
                        xtype: 'gridcolumn',
                        header: 'Autor',
                        sortable: true,
                        width: 150,
                        editable: false,
                        dataIndex: 'autor',
                        tooltip: 'Autor de la noticia '
                    }

                ],
                tbar: {
                    xtype: 'toolbar',
                    id: 'tbOpciones',
                    items: [
                       {
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
                        }
                    ]
                },
                bbar: {
                    xtype: 'paging',
                    store: 'stNoticias',
                    displayInfo: true,
                    pageSize: 10,
                    id: 'ptbNoticias'
                }
            }
        ];
        panelNoticiasUi.superclass.initComponent.call(this);
    }
});
