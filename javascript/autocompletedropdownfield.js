(function($){
	$.entwine('ss', function($){

		var selector = 'select.autocompletedropdown';

		var completedTest = 1;

		$(selector).entwine({
			onmatch: function() {
				//get original value
				var initValue = this.getInput().val();
				//remove text input
				this.getInput().remove();
				//hide select dropdown
				this.hide();
				//add empty option
				this.append('<option value=""></option>');
				//add text input
				this.before('<input type="text" class="text" />');
				//add / remove dropdown and text class
				this.closest('.field').toggleClass('dropdown text');
				//set value
				if(initValue != null) {
					this.getInput().val(initValue);
				}
				else {
					var selectedOption = this.find('option[selected]');
					if($(selectedOption).length) {
						this.getInput().val($(selectedOption).text());
					}
				}
				//add automcomplete... to text.input.
				this.getInput().autocomplete({
					source: this.find('option').map(
							function(){
								return $(this).text();
							}
						).get(),
					change: function (event, ui) {
						if(!ui.item){
							//http://api.jqueryui.com/autocomplete/#event-change -
							// The item selected from the menu, if any. Otherwise the property is null
							//so clear the item for force selection
							$(this).val("").focus();
						}
						else {
							completedTest = 1;
						}
					}
				});
			},

			getInput: function() {
				return this.siblings('input.text[type=text]');
			},

			setValue: function() {
				id = 0;
				var val = this.getInput().val();
				if(val) {
					text = val.toLowerCase();
					var id = null;
					this.find('option').each(function() {
						if($(this).text().toLowerCase() == text) {
							id = $(this).attr('value');
						}
					});
					this.val(id);
				}
				if(!id) {
					this.getInput().css("border", "1px solid red");
				}
				else {
					this.getInput().css("border", "1px solid green");
				}
				completedTest = completedTest * id;
			}
		});

		//set value before submitting....
		$('.Actions input').entwine({
			onclick: function(event) {
				$(selector).setValue();
				return completedTest ? true: false;
			}
		});
	});
})(jQuery);
