<script>
	var custom_css_cd = 0;
	var custom_js_cd = 0;
	jQuery(document).ready(function() {
		jQuery('.w3_custom_code').click(function() {
			console.log("custom code click");
			if (!custom_css_cd) {
				custom_css_cd = 1;
				setTimeout(function() {
					var editor = CodeMirror.fromTextArea(document.querySelector('[name="custom_css"]'), {
						mode: "css",
						theme: "default",
						lineNumbers: true,
						matchBrackets: true,
						indentUnit: 2,
						indentWithTabs: false,
						autoCloseBrackets: true
					});
				}, 300);
			}
			if (!custom_js_cd) {
				custom_js_cd = 1;
				setTimeout(function() {
					var editor = CodeMirror.fromTextArea(document.querySelector('[name="custom_javascript"]'), {
						mode: "javascript",
						theme: "default",
						lineNumbers: true,
						matchBrackets: true
					});
					var editor = CodeMirror.fromTextArea(document.querySelector('[name="custom_js"]'), {
						mode: "javascript",
						theme: "default",
						lineNumbers: true,
						matchBrackets: true
					});
				}, 300);
			}
		});
		jQuery('.w3_hooks').click(function() { 
			if(codeEditor.length > 0) return ;
			var $textareas = jQuery('.hook_before_start');
			$textareas.each(function(i) {
				var textareaId = jQuery(this).attr('id');
				var editor = CodeMirror.fromTextArea(document.getElementById(textareaId), {
					mode: "application/x-httpd-php",
					theme: "default",
					lineNumbers: true,
					matchBrackets: true,
					indentUnit: 2,
					indentWithTabs: false,
					autoCloseBrackets: true
				});
				codeEditor[i] = editor;
			});
		});
	});
</script>