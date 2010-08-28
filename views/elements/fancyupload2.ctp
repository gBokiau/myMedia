<?php
$this->Html->script(array("/mootools/js/1.2.4.4-more.js","/mymedia/js/Swiff.Uploader.js", "/mymedia/js/FancyUpload2.js", "/mymedia/js/Fx.ProgressBar.js"), false);
$this->Html->css('/mymedia/css/fancy.css', null, array('inline'=>false));

if (!isset($previewVersion)) {
	$previewVersion = 'xxs';
}

/* Set $assocAlias and $model if you're using this element multiple times in one form */

if (!isset($assocAlias)) {
	$assocAlias = 'Attachment';
} else {
	$assocAlias = Inflector::singularize($assocAlias);
}

if (!isset($model)) {
	$model = $this->Form->model();
}
$modelId = $this->Form->value($model.'.id');
$this->Html->scriptBlock("
window.addEvent('domready', function() { 

		var gallery = $$('.existing');
		gallery = gallery[0];
		
		var sortable = new Sortables (gallery, {
			'clone':true, 'opacity':'0', snap:6, handle:'img', revert:true, constrain:true
		});
		
		sortable.addEvent('complete', function(){
			this.serialize(false, function(element, index){
				$(element.getProperty('id')+'Sort').value = index+1;
				element.highlight();
				return element.getProperty('id');
			});			
		});
		
		
		
		// our uploader instance 

		var up = new FancyUpload2($('demo-status'), $('demo-list'), { // options object
			timelimit:120,

			// url is read from the form, so you just have to change one place
			url: '".$this->Html->url(array('action'=>'fileupload', 'group'=> $assocAlias, $this->Session->id()))."',

			// path to the SWF file
			path: '".$this->Html->url("/mymedia/Swiff.Uploader.swf")."',
			appendCookieData : true,
			// remove that line to select all files, or edit it, add more items
			typeFilter: {
				'Images (*.jpg, *.jpeg, *.gif, *.png, *.pdf)': '*.jpg; *.jpeg; *.gif; *.png; *.pdf'
			},
			data : 	{
				'data[$model][id]' : '$modelId',
				'data[$assocAlias][0][model]' : '$model',
				'data[$assocAlias][0][group]' : '".strtolower($assocAlias)."',
			},
			
			// this is our browse button, *target* is overlayed with the Flash movie
			target: 'demo-browse',

			// graceful degradation, onLoad is only called if all went well with Flash
			onLoad: function() {
				$('demo-status').removeClass('hide'); // we show the actual UI
				$$('.new').destroy(); // ... and hide the plain form

				// We relay the interactions with the overlayed flash to the link
				this.target.addEvents({
					click: function() {
						return false;
					},
					mouseenter: function() {
						this.addClass('hover');
					},
					mouseleave: function() {
						this.removeClass('hover');
						this.blur();
					},
					mousedown: function() {
						this.focus();
					}
				});

				// Interactions for the 2 other buttons

				$('demo-clear').addEvent('click', function() {
					up.remove(); // remove all files
					return false;
				});

				$('demo-upload').addEvent('click', function() {
					up.start(); // start upload
					return false;
				});
			},

			// Edit the following lines, it is your custom event handling

			/**
			 * Is called when files were not added, files is an array of invalid File classes.
			 * 
			 * This example creates a list of error elements directly in the file list, which
			 * hide on click.
			 */ 
			onSelectFail: function(files) {
				files.each(function(file) {
					new Element('li', {
						'class': 'validation-error',
						html: file.validationErrorMessage || file.validationError,
						title: MooTools.lang.get('FancyUpload', 'removeTitle'),
						events: {
							click: function() {
								this.destroy();
							}
						}
					}).inject(this.list, 'top');
				}, this);
			},

			/**
			 * This one was directly in FancyUpload2 before, the event makes it
			 * easier for you, to add your own response handling (you probably want
			 * to send something else than JSON or different items).
			 */
			onFileSuccess: function(file, response) {
				var json = new Hash(JSON.decode(response, true) || {});

				if (json.get('status') == '1') {
					file.element.addClass('file-success');
					var count = sortable.elements.length + 1;
					var html = json.get('html').substitute({'i':count});
					var newImage = new Element('div', {html:html, id:'Image'+count }).inject(gallery);
					sortable.addItems(newImage);
					newImage.fade('hide').fade('in');
					file.element.fade('hide');//.chain(function(el){el.destroy()});
				} else {
					file.element.addClass('file-failed');
					file.info.set('html', '<strong>An error occured:</strong> ' + (json.get('error') ? (json.get('error') + ' #' + json.get('code')) : response));
				}
			},

			/**
			 * onFail is called when the Flash movie got bashed by some browser plugin
			 * like Adblock or Flashblock.
			 */
			onFail: function(error) {
				switch (error) {
					case 'hidden': // works after enabling the movie and clicking refresh
						alert('To enable the embedded uploader, unblock it in your browser and refresh (see Adblock).');
						break;
					case 'blocked': // This no *full* fail, it works after the user clicks the button
						alert('To enable the embedded uploader, enable the blocked Flash movie (see Flashblock).');
						break;
					case 'empty': // Oh oh, wrong path
						alert('A required file was not found, please be patient and we fix this.');
						break;
					case 'flash': // no flash 9+ :(
						alert('To enable the embedded uploader, install the latest Adobe Flash plugin.')
				}
			}

		});

	});", array('inline'=>false));
?>
<?php
	echo $this->element('attachments', array('assocAlias'=>$assocAlias, 'previewVersion'=>$previewVersion, 'model'=>$model));
?>