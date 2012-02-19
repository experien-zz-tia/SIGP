function columnWrap(val){
    return '<div style="white-space:normal !important;"><p align="justify">'+ val +'</p></div>';
}

frmDetalleNotasUi = Ext.extend(Ext.Window, {
	title: 'Evaluaci&oacute;n del pasante',
	width: 674,
	height: 510,
	modal: true,
	resizable : false,
	id: 'frmDetalleNotasWin',
	initComponent: function() {
		this.items = [{
				xtype: 'fieldset',
				title: 'Informaci&oacute;n acad&eacute;mica',
				width: 654,
				items: [{
					xtype: 'textfield',
					fieldLabel: 'C&eacute;dula',
					anchor: '50%',
					maxLength: 40,
					allowBlank: false,
					disabled:true,
					id: 'txtCedula'
				},{
					xtype: 'textfield',
					fieldLabel: 'Nombres',
					anchor: '100%',
					maxLength: 40,
					allowBlank: false,
					disabled:true,
					id: 'txtNombre'
				},{
					xtype: 'textfield',
					fieldLabel: 'Apellidos',
					anchor: '100%',
					maxLength: 40,
					allowBlank: false,
					disabled:true,
					id: 'txtApellido'
				},
				 {
                   xtype: 'textfield',
                   id: 'txtIdPasante',
                   hidden: true
                }                 
			 ,{
				xtype: 'editorgrid',
				title: 'Calificaciones',
				store: 'stgDetalleNotas',
				height: 350,
				titleCollapse: true,
				stripeRows: true,
				width: 635,
				loadMask: true,
				maskDisabled: false,
				id: 'gridDetalleNotas',
				columns: [
				{
					header     : "Evaluaci&oacute;n",  
        			dataIndex   : "evalDescripcion",  
        			hideable    : false,  
        			groupable   : true,  
        			width       : 100  
        			
       			},
				{
					xtype: 'numbercolumn',
					header: 'Id',
					sortable: true,
					width: 50,
					align: 'right',
					editable: false,
					dataIndex: 'aspectoId',
					hidden: true,
					hideable: false,
					format: '0'

				},{
					xtype: 'gridcolumn',
					dataIndex: 'item',
					header: '&Iacute;tem',
					sortable: true,
					width: 160,
					editable: false,
					renderer: columnWrap,
					tooltip: '&Iacute;tem de evaluaci&oacute;n'
				},{
					xtype: 'gridcolumn',
					header: 'Descripci&oacute;n',
					sortable: true,
					width: 260,
					dataIndex: 'descripcion',
					editable: false,
					renderer: columnWrap,
					tooltip: 'Descripci&oacute;n del item.'
				},{
					xtype: 'numbercolumn',
					dataIndex: 'nota',
					header: 'Nota',
					sortable: true,
					width: 80,
					align: 'right',
					tooltip: 'Nota del item',
					editor: new Ext.form.NumberField({
							allowBlank: false, 
							allowNegative: false,
							maxValue: 10,
                            minValue: 0
							})  ,
					format: '0'
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
                    id: 'tbOpcionesNotas',
                    items: [
                       {
                            xtype: 'button',
                            text: 'Guardar',
                            tooltip: 'Guardar las calificaiones.',
                            iconCls: 'sigp-guardar',
                            id: 'btnGuardar'
                       },
                        {
                            xtype: 'button',
                            text: 'Cancelar',
                            iconCls: 'sigp-cancelar',
                            id: 'btnCancelar'
                        }                     
                    ]
                }  
		
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
                        text: 'Salir',
                        iconCls: 'sigp-salir',
                        id: 'btnSalir'
                    }
        
                ]
                 
            },
					{
                xtype: 'label',
                text: 'Doble clic en la celda "nota" de cada item para editarla.'
            }
			
			]
		} ];
		frmDetalleNotasUi.superclass.initComponent.call(this);
	}
});