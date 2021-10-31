;(function(window, document, undefined) {

    "use strict";

    console.log('Initializing liveeditor');

    const toQueryString = (initialObj) => {
        const reducer = (obj, parentPrefix = null) => (prev, key) => {
          const val = obj[key];
          key = encodeURIComponent(key);
          const prefix = parentPrefix ? `${parentPrefix}[${key}]` : key;
      
          if (val == null || typeof val === 'function') {
            prev.push(`${prefix}=`);
            return prev;
          }
      
          if (['number', 'boolean', 'string'].includes(typeof val)) {
            prev.push(`${prefix}=${encodeURIComponent(val)}`);
            return prev;
          }
      
          prev.push(Object.keys(val).reduce(reducer(val, prefix), []).join('&'));
          return prev;
        };
      
        return Object.keys(initialObj).reduce(reducer(initialObj), []).join('&');
    };

    window.BlockEditor = function(id) {
        this.quill = null;
        
        this.element = document.querySelector('#' + id);
        this.contentElement = this.element.querySelector('.flattery-block--content');
        
        // create editor element
        this.editorElement = document.createElement("div");
        this.editorElement.classList.add('flattery-block--editor');
        this.editorElement.id = "#" + id + "_editor";
        this.element.append(this.editorElement);
        
        this.editorActions = document.createElement("div");
        this.editorActions.classList.add('flattery-block--actions');
        this.element.append(this.editorActions);

        let saveButton = document.createElement("button");
        saveButton.innerHTML = "Save";
        saveButton.classList.add('flattery-block--save');

        
        let cancelButton = document.createElement("button");
        cancelButton.innerHTML = "Cancel";
        cancelButton.classList.add('flattery-block--cancel');

        this.editorActions.append(saveButton);
        this.editorActions.append(cancelButton);
        
        // toggle edit when clicking the content element
        this.contentElement.addEventListener('click', () => {
            this.editMode();
        });

        // save and close editing when clicking save
        saveButton.addEventListener('click', () => {
            this.save();
        });

        // cancel and hide editor
        cancelButton.addEventListener('click', () => {
            this.cancel();
        });
    };

    window.BlockEditor.QuillDefaults = {
        //debug: 'info',
        //modules: {
        //},
        placeholder: 'Lorem ipsum...',
        readOnly: false,
        theme: 'snow'
    };

    window.BlockEditor.prototype.editMode = function() {
        console.log(this);
        console.log('toggling edit mode');

        if (this.quill == null) {
            console.log('creating quill instance');
            this.editorElement.innerHTML = this.contentElement.innerHTML;
            this.quill = new Quill(this.editorElement, BlockEditor.QuillDefaults);
        }

        this.element.classList.add('is-editing');
    };
    
    window.BlockEditor.prototype.getEditorHtml = function() {
        return this.editorElement.querySelector('.ql-editor').innerHTML;
    };

    window.BlockEditor.prototype.save = function() {
        let blockName = this.element.dataset.blockName;
        let content = this.getEditorHtml();

        console.log('saving ' + blockName);

        let request = new XMLHttpRequest();
        request.open('POST', 'api/liveeditor/blocks/save', true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');

        request.onload = function() {
            if (this.status >= 200 && this.status < 400) {
                // Success!
                var resp = this.response;
                console.log('Save response: ' + resp);
            } else {
                console.log('response status ' + this.status);
            }
        };
        
        request.onerror = function() {
            console.log('Failed to save block');
        };

        console.log(content);

        request.send(toQueryString({
            block: blockName,
            content: content
        }));

        this.contentElement.innerHTML = content;

        this.element.classList.remove('is-editing');
    };

    window.BlockEditor.prototype.cancel = function() {
        console.log('canceling...');
        this.element.classList.remove('is-editing');
    };

    // get the blocks
    let $blocks = document.querySelectorAll(".flattery-block");

    $blocks.forEach(($block) => {
        $block.flatteryBlockEditor = new BlockEditor($block.id);
    });

})(window, document);