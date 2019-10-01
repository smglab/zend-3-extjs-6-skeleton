Ext.define('TestProj.view.main.ArchiveController', {
    extend: 'Ext.app.ViewController',

    alias: 'controller.archivecontroller',

    onRemoveClick: function (view, recIndex, cellIndex, item, e, record) {
        var store = view.getStore();
        store.remove(record);
        store.reload();
    },
    onEditClick: function(view, recIndex, cellIndex, item, e, record) {
        var store = view.getStore();
        Ext.Ajax.request({
            url: '/archive/' + record.data.id,
            success: function (response) {
                var responseDecode = Ext.decode(response.responseText);
                Ext.create('TestProj.view.main.Upload', {
                    title: 'Загрузить архив с данными',
                    test: 'test value',
                    records: {
                        zip: record,
                        files: responseDecode.item.files
                    },
                    store: store,
                    maxWidth: 1100
                }).show();

             }
         });


    },

    onUploadClick: function(view) {
        var store = view.up('grid').getStore('archives');
        Ext.create('TestProj.view.main.Upload', {
            title: 'Загрузить архив с данными',
            test: 'test value',
            store: store,
            width: 500
        }).show();
    },

    onItemSelected: function (sender, record) {
    },
});
