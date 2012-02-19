function columnWrap(val){
    return '<div style="white-space:normal !important;"><p align="justify">'+ val +'</p></div>';
}


panelMensajesUi = Ext.extend(Ext.Panel, {
    width: 741,
    height: 420,
    id: 'panelMensajes',
    initComponent: function() {
        this.items = [
            {
                xtype: 'grid',
                title: 'Notificaciones',
                store: 'stNotificaciones',
                height: 418,
                titleCollapse: true,
                stripeRows: true,
                width: 739,
				loadMask: true,
                maskDisabled: false,
                id: 'gridNotificaciones',
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
                        xtype: 'datecolumn',
                        header: 'Fecha de Envio',
                        sortable: true,
                        width: 100,
                        editable: false,
                        dataIndex: 'fchEnvio',
                        tooltip: 'Fecha de envio.',
                        format:'d/m/Y'
                    },
                         {
                        xtype: 'gridcolumn',
                        header: 'Remitente',
                        sortable: true,
                        width: 150,
                        editable: false,
                        dataIndex: 'remitente',
                        tooltip: 'Remitente de la notificaci&oacute;n '
                    },
                     {
                        xtype: 'gridcolumn',
                        dataIndex: 'mensaje',
                        header: 'Mensaje',
                        sortable: true,
                        width: 465,
                        renderer: columnWrap,
                        editable: false,
                        tooltip: 'Contenido de la notificaci&oacute;n'
                    }
                ],
                tbar: {
                    xtype: 'toolbar',
                    id: 'tbOpciones',
                    items: [
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
                    store: 'stNotificaciones',
                    displayInfo: true,
                    pageSize: 10,
                    id: 'ptbNotificaciones'
                }
            }
        ];
        panelMensajesUi.superclass.initComponent.call(this);
    }
});
