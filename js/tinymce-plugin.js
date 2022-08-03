(function(){
    tinymce.PluginManager.add( 'callout_boxes', function(editor, url){

        editor.addButton( 'callout_box_button_key', {

            tooltip: "Insert Callout Box",
            icon: 'icon dashicons-align-wide',
            onclick: function(){
                var selection = tinymce.activeEditor.selection.getContent();
                // Open window
                editor.windowManager.open({
                    title: 'Insert Callout Box',
                    body: [
                        {
                            type: 'textbox',
                            name: 'text',
                            label: 'Text',
                            value: selection,
                            multiline: true,
                            minWidth: 300,
                            minHeight: 100
                        },
                        {
                            type: 'listbox',
                            name: 'callout_type',
                            label: 'Callout Type',
                            'values': [
                                {text: 'Info', value: 'info'},
                                {text: 'Tips', value: 'tips'},
                                {text: 'Notice', value: 'note'},
                                {text: 'Warnings', value: 'warn'}
                            ]
                        },
                        {
                            type: 'listbox',
                            name: 'icon_size',
                            label: 'Icon Size',
                            'values': [
                                {text: 'Hide Icon', value: 'hide'},
                                {text: 'Normal', value: 'normal'},
                                {text: 'Small', value: 'small'},
                                {text: 'Big', value: 'big'}
                            ]
                        }
                    ],
                    onsubmit: function(e){
                        // Insert content when the window form is submitted
                        editor.insertContent( '[callout  type="'+ e.data.callout_type +'" size="'+ e.data.icon_size +'"]' + e.data.text + '[/callout]');
                    }
                });
            }
        });
    });
})();
