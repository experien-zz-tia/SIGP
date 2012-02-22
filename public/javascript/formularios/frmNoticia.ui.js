frmNoticiaUi = Ext.extend(Ext.Window, {
    title: 'Noticia',
    width: 674,
    height: 450,
    modal: true,
    resizable : false,
    id: 'frmNoticia',
    initComponent: function() {
        this.items = [
            {
                xtype: 'form',
                width: 660,
                id: 'formNoticia',
                method: 'POST',
                waitTitle: 'Por favor espere...',
                url: '/SIGP/noticia/registrar',
                items: [
                    {
                        xtype: 'fieldset',
                        title: 'Informaci&oacute;n de la noticia',
                        width: 654,
                        items: [
                            {
                                xtype: 'textfield',
                                fieldLabel: 'T&iacute;tulo*',
                                anchor: '100%',
                                maxLength: 60,
                                allowBlank: false,
                                id: 'txtTitulo'
                            },
                            {
                                xtype: 'htmleditor',
                                anchor: '100%',
                                height: 300,
                                fieldLabel: 'Descripci&oacute;n*',
                                width: 572,
                                enableSourceEdit: false,
                                enableFont: false,
                                id: 'txtContenido'
                            },

                            {
                                xtype: 'textfield',
                                id: 'txtIdNoticia',
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
                                width: 90,
                                height: 30,
                                type: 'submit',
                                iconCls: 'sigp-guardar',
                                id: 'btnGuardar'
                            },
                             {
                                xtype: 'button',
                                text: 'Actualizar',
                                width: 90,
                                height: 30,
                                iconCls: 'sigp-publicar',
                                hidden: true,
                                id: 'btnActualizar'
                            },
                            {
                                xtype: 'button',
                                text: 'Limpiar',
                                width: 90,
                                height: 30,
                                type: 'reset',
                                iconCls: 'sigp-limpiar',
                                id: 'btnLimpiar'
                            }
                            ,
                            {
                                xtype: 'button',
                                text: 'Salir',
                                width: 90,
                                height: 30,
                                type: 'reset',
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
        frmNoticiaUi.superclass.initComponent.call(this);
    }
});
