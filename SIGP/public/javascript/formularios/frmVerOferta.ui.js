frmVerOfertaUi = Ext.extend(Ext.Window, {
    title: 'Oferta',
    width: 674,
    height: 530,
    modal: true,
    resizable : false,
    id: 'frmVerOfertaWin',
    initComponent: function() {
        this.items = [
            {
                xtype: 'fieldset',
                title: 'Informaci&oacute;n Oferta',
                 items: [
                 	 {
                        xtype: 'textfield',
                        fieldLabel: 'Empresa',
                        anchor: '100%',
                        maxLength: 40,
                        allowBlank: false,
                        readOnly: true,
                        id: 'txtEmpresa'
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'T&iacute;tulo',
                        anchor: '100%',
                        maxLength: 40,
                        allowBlank: false,
                        readOnly: true,
                        id: 'txtTitulo'
                    },
                     {
                        xtype: 'textfield',
                        fieldLabel: '&Aacute;rea',
                        anchor: '100%',
                        maxLength: 40,
                        allowBlank: false,
                        readOnly: true,
                        id: 'txtArea'
                    },
                    {
           		   	  xtype: 'label',
           		   	  text: 'Descripci\u00F3n'
         			   },
                    {
                        xtype: 'panel',
                        anchor: '100%',
                        height: 150,
                        html: 'cargando...',
                        width: 572,
                        id: 'txtDescripcion'
                    },
               
                   {
                        xtype: 'textfield',
                        fieldLabel: 'Tipo de oferta',
                        anchor: '50%',
                        readOnly: true,
                        maxLength: 40,
                        allowBlank: false,
                        id: 'txtTipoOferta',
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
                    },
                    {
                        xtype: 'numberfield',
                        fieldLabel: 'Vacantes en la empresa',
                        anchor: '50%',
                        maxValue: 100,
                        minValue: 1,
                        allowDecimals: false,
                        readOnly: true,
                        decimalPrecision: 0,
                        allowNegative: false,
                        allowBlank: false,
                        id: 'txtVacantes'
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Cupos disponibles',
                        readOnly: true,
                        anchor: '50%',
                        id: 'txtDisponible'
                    },  
                    {
                        xtype: 'datefield',
                        fieldLabel: 'Fecha de cierre',
                        anchor: '50%',
                        editable: false,
                        allowBlank: false,
                        id: 'dateFechaCierre',
                        format:'d/m/Y',
                        readOnly:true
                    },
           			 {
                        xtype: 'datefield',
                        fieldLabel: 'Fecha de Inicio',
                        anchor: '50%',
                        editable: false,
                        allowBlank: false,
                        id: 'dateFechaInicioEst',
                       format:'d/m/Y',
                       readOnly:true
                    } ,
                    {
                        xtype: 'datefield',
                        fieldLabel: 'Fecha de culminaci&oacute;n',
                        anchor: '50%',
                        editable: false,
                        allowBlank: false,
                        id: 'dateFechaCulminacionEst',
                        format:'d/m/Y',
                        readOnly:true
                    },
                    {
                        xtype: 'textfield',
                        id: 'txtIdOferta',
                        readOnly: true,
                        hidden: true
                    }
                ]
            },
            {
                xtype: 'container',
                layout: 'hbox',
                id: 'contenedorBtns',
                layoutConfig: {
                    pack: 'end'
                },
                items: [
                    {
                        xtype: 'button',
                        text: 'Postular',
                        type: 'submit',
                        iconCls: 'sigp-postular',
                        id: 'btnPostularOferta'
                    },
                    {
                        xtype: 'button',
                        text: 'Salir',
                        iconCls: 'sigp-salir',
                        id: 'btnSalir'
                    }
                ]
            }
        ];
        frmVerOfertaUi.superclass.initComponent.call(this);
    }
});
