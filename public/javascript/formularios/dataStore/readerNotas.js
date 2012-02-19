var readerNotas = new Ext.data.JsonReader({   
        totalProperty   : 'total',  
        successProperty : 'success',  
        messageProperty : 'message',  
        idProperty  : 'id',  
        root        : 'resultado'  
        },[  
            {
                    name: 'evalDescripcion',
                    type: 'string'
                },
                {
                    name: 'evaluacionId',
                    type: 'int'
                },
                {
                    name: 'aspectoId',
                    type: 'int'
                },
                {
                    name: 'nota',
                    type: 'float'
                },
                {
                    name: 'item',
                    type: 'string'
                },
                {
                    name: 'descripcion',
                    type: 'string'
                }
        ]  
    );  
