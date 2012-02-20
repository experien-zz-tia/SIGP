
frmLapsoAcademicoUi = Ext.extend(Ext.Window, {
    title: 'Lapso Acad&eacute;mico',
    width: 374,
    height: 200,
    modal: true,
    resizable : false,
    id: 'frmLapsoAcademicoWin',
    initComponent: function() {
        this.items = [
            {
                xtype: 'form',
                id: 'formLapsoAcademico',
                method: 'POST',
                waitTitle: 'Por favor espere...',
                url: '/SIGP/lapsoAcademico/registrar',
                items: [
                    {
                        xtype: 'fieldset',
                        title: 'Informaci&oacute;n Lapso Acad&eacute;mico',
                        items: [
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Lapso*',
                                anchor: '100%',
                                maxLength: 10,
                                allowBlank: false,
                                readOnly:true,
                                id: 'txtLapso'
                            },
                            {
                                xtype: 'datefield',
                                fieldLabel: 'Fecha de Inicio*',
                                anchor: '100%',
                                editable: false,
                                allowBlank: false,
                                id: 'dateFechaInicio',
                                vtype: 'dateRange',
    							endDateField: 'dateFechaFin',
                                format:'d/m/Y'
                            },
                             {
                                xtype: 'datefield',
                                fieldLabel: 'Fecha Finalizac&iacute;n*',
                                anchor: '100%',
                                editable: false,
                                allowBlank: false,
                                id: 'dateFechaFin',
                                vtype: 'dateRange',
    							startDateField: 'dateFechaInicio',
                                format:'d/m/Y'
                            } ,
                            {
                                xtype: 'textfield',
                                id: 'txtIdLapso',
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
                                text: 'Guardar',
                                type: 'submit',
                                iconCls: 'sigp-guardar',
                                id: 'btnGuardar'
                            },
                            {
                                xtype: 'button',
                                text: 'Actualizar',
                                iconCls: 'sigp-actualizar',
                                hidden: true,
                                id: 'btnActualizar'
                            },
                            {
                                xtype: 'button',
                                text: 'Limpiar',
                                iconCls: 'sigp-limpiar',
                                id: 'btnLimpiar'
                            },
                             {
                                xtype: 'button',
                                text: 'Salir',
                                iconCls: 'sigp-salir',
                                id: 'btnSalir'
                            }
                        ]
                    },
                     {
                        xtype: 'label',
                        text: 'Campos marcados con * son obligatorios.'
                    }
                ]
            }
        ];
        frmLapsoAcademicoUi.superclass.initComponent.call(this);
    }
});