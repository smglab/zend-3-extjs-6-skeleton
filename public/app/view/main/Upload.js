Ext.define('TestProj.view.main.Upload', {
    extend: 'Ext.window.Window',
    xtype: 'upload-window',
    title: 'Upload',
    layout: 'form',
    requires: [
        'TestProj.form.field.ZipFiles'
    ],
    constructor: function (config) {
        config = config || {};
        config.id = config.id || 'test-job-window-upload';
        var formConfig = this.getFormFields(config);
        config.form = this.createForm(formConfig);

        Ext.applyIf(config,
            {
                buttons: [{
                    text: 'Отправить файлы',
                    cls: 'primary-button',
                    scope: this,
                    handler: this.submiteFiles
                }],
                items: [
                    config.form,
                    {
                        text: 'Добавить файл',
                        xtype: 'button',
                        width: 150,
                        margin: '0 5px',
                        scope: this,
                        handler: function () {
                            this.addFileField(config);
                        }
                    }
                ]
            });
        if (config.records) {
            var data = {};
            if (config.records.zip) {
                data.name = config.records.zip.data.name;
                data.id = config.records.zip.data.id;
                config.zipId = data.id;
            }
            if (config.records.files) {
                data.filedelete = config.records.files;
            }
            config.form.getForm().setValues(data);
        } else {
            this.addFileField(config);
        }

        this.callParent(arguments);
    },
    getFormFields: function (config) {

        return {
            items: [{
                xtype: 'fieldcontainer',
                scrollable: 'y',
                height: 400,
                items: [{
                    xtype: 'textfield',
                    labelAlign: 'top',
                    width: '99%',
                    allowBlank: false,
                    msgTarget: 'under',
                    fieldLabel: 'Название архива',
                    id: config.id + '-name',
                    name: 'name',
                    blankText: 'Поле не должно быть пустым',
                    validator: function (value) {
                        if (value.length > 250) {
                            return 'Слишком длинное имя файла!';
                        }

                        return true;
                    },
                },
                    {
                        xtype: 'fieldcontainer',
                        layout: 'hbox',
                        items: [
                            {
                                xtype: config.records ? 'zip-files' : 'hidden',
                                labelAlign: 'top',
                                width: 400,
                                allowBlank: false,

                                msgTarget: 'under',
                                fieldLabel: 'Файлы уже в архиве',
                                name: 'filedelete'
                            },
                            {
                                xtype: 'fieldcontainer',
                                id: 'new-files-' + config.id,
                            },

                        ]
                    },
                ]
            },
            ]
        };
    },

    getFileFields: function (config) {

        return {
            xtype: 'fieldcontainer',
            layout: 'column',
            maxHeight: 40,
            defaults: {
                allowBlank: false,
            },
            items: [{
                xtype: 'filefield',
                name: 'files[]',
                width: 360,
                msgTarget: 'under',
                anchor: '100%',
                blankText: 'Поле не должно быть пустым',
                validator: function (value) {
                    if (value.length > 250) {
                        return 'Слишком длинное имя файла!';
                    }

                    return true;
                },
                listeners: {
                    afterrender: function (cmp) {
                        cmp.fileInputEl.set({
                            accept: '.docx,.doc,.jpg,.jpeg,.pdf,.xls,.xlsx',
                        });
                    },
                    change: function(fld, value) {
                             var newValue = value.replace(/^.*(\\|\/|\:)/, '');
                            fld.setRawValue(newValue);

                        }
                },
                buttonText: 'Загрузить файл!'
            },
                {
                    text: 'Удалить',
                    xtype: 'button',
                    width: 100,
                    margin: '0 5px',
                    handler: function () {
                        this.up('fieldcontainer').destroy();
                    }
                }
            ]
        };
    },
    addFileField: function (config) {
        var container = Ext.getCmp('new-files-' + config.id);
        container.add(this.getFileFields(this.config));
    },
    createForm: function (config) {
        config = config || {};
        Ext.applyIf(config, {
            formFrame: true,
            border: false,
            bodyBorder: false,
            autoHeight: true,
        });
        return new Ext.FormPanel(config);
    },
    submiteFiles: function () {
        var window = this;
        var id = this.zipId ? '/' + this.zipId : '';

        if (this.form.isValid()) {
            this.form.submit({
                url: '/archive' + id,
                method: id ? 'put' : 'post',
                waitMsg: 'Загрузка...',
                success: function (form, action) {
                    Ext.Msg.alert('Success',
                        action.result.message,
                        function () {
                            window.close();
                            window.store.load();
                        }
                    );
                },
                failure: function (form, action) {
                    Ext.Msg.alert('Failed', action.result.message);
                }
            });
        }
    }
});