function columnWrap(val){
    return '<div style="white-space:normal !important;"><p align="justify">'+ val +'</p></div>';
}

frmVerNotasUi = Ext.extend(Ext.Panel, {
	width : 660,
	height : 460,
	title : 'Calificaciones',
	id : 'panleNotas',
	layout : 'form',
	initComponent : function() {
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
					fieldLabel: 'Nombres y apellidos',
					anchor: '100%',
					maxLength: 40,
					allowBlank: false,
					disabled:true,
					id: 'txtNombreCompleto'
				},{
					xtype: 'textfield',
					fieldLabel: 'Carrera',
					anchor: '50%',
					maxLength: 40,
					allowBlank: false,
					disabled:true,
					id: 'txtCarrera'
				},{
					xtype: 'textfield',
					fieldLabel: 'Nota Final',
					anchor: '50%',
					maxLength: 40,
					allowBlank: false,
					disabled:true,
					id: 'txtNotaFinal'
				}       
				
			 ,{
				xtype: 'editorgrid',
				title: 'Detalle Calificaciones',
				store: 'stgDetalleNotas',
				height: 280,
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
					editable: false,
					width: 80,
					align: 'right',
					tooltip: 'Nota del item',
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
                            text: 'Generar PDF',
                            tooltip: 'Generar constancia de notas en PDF.',
                            iconCls: 'sigp-pdf',
                            id: 'btnPDF'
                       }               
                    ]
                }  
			}			
			]
		}];
		frmVerNotasUi.superclass.initComponent.call(this);
	}
});
