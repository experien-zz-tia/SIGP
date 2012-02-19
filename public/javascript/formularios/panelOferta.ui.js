panelOfertaUi = Ext.extend(Ext.Panel, {
    width: 741,
    height: 420,
    id: 'panelOferta',
    initComponent: function() {
        this.items = [
            {
                xtype: 'grid',
                title: 'Ofertas',
                store: 'stOfertasPasante',
                height: 418,
                titleCollapse: true,
                stripeRows: true,
                width: 739,
				loadMask: true,
                maskDisabled: false,
                id: 'gridOfertas',
                columns: [
                new Ext.grid.RowNumberer(),
                {
                        xtype: 'datecolumn',
                        header: 'Fecha Publicaci&oacute;n',
                        sortable: true,
                        width: 100,
                        editable: false,
                        dataIndex: 'fchPublicacion',
                        tooltip: 'Fecha de Publicaci&oacute;n de la oferta',
                        format:'d/m/Y'
                    },
                    {
                        xtype: 'datecolumn',
                        dataIndex: 'fchCierre',
                        header: 'Fecha Cierre',
                        sortable: true,
                        width: 100,
                        editable: false,
                        tooltip: 'Fecha de cierre de la oferta',
                        format:'d/m/Y'
                    },
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
                        header: 'Id Empresa',
                        sortable: true,
                        width: 50,
                        align: 'right',
                        editable: false,
                        dataIndex: 'empresaId',
                        hidden: true,
                        hideable: false,
                        format: '0'
                    },
                      {
                        xtype: 'gridcolumn',
                        header: 'Empresa',
                        sortable: true,
                        width: 100,
                        editable: false,
                        dataIndex: 'razonSocial',
                        tooltip: 'Raz&oacute;n Social de la empresa'
                    },
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'titulo',
                        header: 'T&iacute;tulo',
                        sortable: true,
                        width: 200,
                        editable: false,
                        tooltip: 'T&iacute;tulo de la oferta'
                    },
                    {
                        xtype: 'gridcolumn',
                        header: '&Aacute;rea',
                        sortable: true,
                        width: 180,
                        dataIndex: 'area',
                        editable: false,
                        tooltip: '&Aacute;rea de la oferta'
                    },
                    {
                        xtype: 'numbercolumn',
                        dataIndex: 'vacantes',
                        header: 'Vacantes',
                        sortable: true,
                        width: 75,
                        align: 'right',
                        editable: false,
                        tooltip: 'N&uacute;mero de puestos disponibles para ocupar dentro de la empresa.',
                        format: '0'
                    }
                    ,
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'disponible',
                        header: 'Disponibles',
                        sortable: true,
                        width: 75,
                        editable: false,
                        tooltip: 'N&uacute;mero de puestos disponibles para postularse a la oferta.',
                        renderer: function(value, cell){     
    								var str = '';  
    								if (value==0) {      
        								str = "Sin L&iacute;mite." ;    
            						}  
								    else {        
								        str =value;       
								    }  
								    return str;          
								}  	
                    },
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'tipoOferta',
                        header: 'Tipo Oferta',
                        sortable: true,
                        width: 75,
                        editable: false,
                        tooltip: 'Tipo de oferta.',
                        renderer: function(value, cell){     
    								var str = '';  
    								if (value=='A') {      
        								str = "Abierta" ;    
            						}  
								    else {        
								        str ="Cerrada";       
								    }  
								    return str;          
								}  	
                    }                                
                ],
                tbar: {
                    xtype: 'toolbar',
                    id: 'tbOpciones',
                    items: [
                        {
                            xtype: 'button',
                            text: 'Ver Oferta',
                            iconCls: 'sigp-ofertas',
                            id: 'btnVerOferta'
                        },
                        {
                            xtype: 'button',
                            text: 'Postularse',
                            iconCls: 'sigp-postular',
                            id: 'btnPostular'
                        }                  
                    ]
                },
                bbar: {
                    xtype: 'paging',
                    store: 'stOfertasPasante',
                    displayInfo: true,
                    pageSize: 10
                }
            }
        ];
        panelOfertaUi.superclass.initComponent.call(this);
    }
});
