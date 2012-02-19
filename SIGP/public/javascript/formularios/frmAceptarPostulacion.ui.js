frmAceptarPostulacionUi = Ext.extend(Ext.Window, {
    title: 'Postulaci&oacute;n',
    width: 674,
    height: 515,
    modal: true,
    resizable : false,
    id: 'frmAceptarPostulacionWin',
    initComponent: function() {
        this.items = [
            {
                xtype: 'form',
                width: 660,
                id: 'formAceptarPostulacion',
                method: 'POST',
                waitTitle: 'Por favor espere...',
                url: '/SIGP/postulacion/aceptar',
                items: [
                    {
                        xtype: 'fieldset',
                        title: 'Informaci&oacute;n del pasante',
                        width: 654,
                        items: [
                            {
                                xtype: 'textfield',
                                fieldLabel: 'C&eacute;dula',
                                anchor: '50%',
                                maxLength: 40,
                                readOnly:true,
                                submitValue: false,
                                allowBlank: false,
                                id: 'txtCedula'
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Nombre y Apellido',
                                anchor: '100%',
                                maxLength: 40,
                                allowBlank: false,
                                readOnly:true,
                                submitValue: false,
                                id: 'txtNombreApellido'
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Carrera',
                                anchor: '100%',
                                maxLength: 40,
                                submitValue: false,
                                readOnly:true,
                                allowBlank: false,
                                id: 'txtCarrera'
                            },
                            {
                                xtype: 'textfield',
                                id: 'txtIdPostulacion',
                                hidden: true
                            }
                        ]
                    },
                     {
                        xtype: 'fieldset',
                        title: 'Informaci&oacute;n de la oferta',
                        width: 654,
                        items: [
                            {
                                xtype: 'textfield',
                                fieldLabel: 'T&iacute;tulo',
                                anchor: '100%',
                                readOnly:true,
                                submitValue: false,
                                allowBlank: false,
                                id: 'txtTitulo'
                            },
                            {
                                xtype: 'datefield',
                                fieldLabel: 'Fecha de cierre',
                                anchor: '50%',
                                editable: false,
                                allowBlank: false,
                                readOnly:true,
                                submitValue: false,
                                id: 'dateFechaCierre',
                                format:'d/m/Y'
                            }
                        ]
                    },
                     {
                        xtype: 'fieldset',
                        title: 'Informaci&oacute;n de la pasant&iacute;a',
                        width: 654,
                        items: [
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Tipo',
                                anchor: '50%',
                                readOnly:true,
                                submitValue: false,
                                allowBlank: false,
                                id: 'txtTipo'
                            },
                             {
                                xtype: 'textfield',
                                fieldLabel: 'Modalidad',
                                anchor: '50%',
                                readOnly:true,
                                submitValue: false,
                                allowBlank: false,
                                id: 'txtModalidad'
                            },
                            {
                                xtype: 'datefield',
                                fieldLabel: 'Fecha de Inicio*',
                                anchor: '50%',
                                editable: false,
                                allowBlank: false,
                                id: 'dateFechaInicioEst',
                                vtype: 'dateRange',
    							endDateField: 'dateFechaCulminacionEst',
                                format:'d/m/Y'
                            } ,
                            {
                                xtype: 'datefield',
                                fieldLabel: 'Fecha de culminaci&oacute;n*',
                                anchor: '50%',
                                editable: false,
                                allowBlank: false,
                                vtype: 'dateRange',
    							startDateField: 'dateFechaInicioEst',
                                id: 'dateFechaCulminacionEst',
                                format:'d/m/Y'
                            },
              				{
                                xtype: 'combo',
                                name: 'cmbTutorEmp',
                                anchor: '100%',
                                fieldLabel: 'Tutor Empresarial*',
                                editable: false,
                                store: 'stTutorEmpLight',
                                displayField: 'nombre',
                                valueField: 'id',
                                emptyText: '-Seleccione-',
                                triggerAction: 'all',
                                allowBlank: false,
                                forceSelection: true,
                                loadingText: 'Cargando...',
                                blankText: 'Seleccione un tutor.',
                                submitValue: false,
                                id: 'cmbTutorEmp'
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
                                text: 'Aceptar',
                                type: 'submit',
                                iconCls: 'sigp-aceptar',
                                id: 'btnAceptarPost'
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
        frmAceptarPostulacionUi.superclass.initComponent.call(this);
    }
});
