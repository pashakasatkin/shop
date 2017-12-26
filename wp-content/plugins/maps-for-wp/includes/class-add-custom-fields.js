jQuery(function($){$(document).ready( function() {
 /*
 * Для работы класса требуется подключение class-add-custom-fields.php
 * В нем подключается wp_enqueue_media() и данный файл. Также возможно нужно будет
 * подключить в php файле class-add-custom-fields.php библиотеку jquery
 */
 console.log("Подключен скрип настроек плагина class-add-custom-fields.js!");
	/*
	 * действие при нажатии на кнопку загрузки изображения
	 * вы также можете привязать это действие к клику по самому изображению
	 */
	//$('.upload_image_button').click(function(){
	$('body').on('click', '.upload_image_button', function(){
		var send_attachment_bkp = wp.media.editor.send.attachment;
		var button = $(this);
		wp.media.editor.send.attachment = function(props, attachment) {
			$(button).parent().prev().attr('src', attachment.url);
			$(button).prev().val(attachment.id);
			wp.media.editor.send.attachment = send_attachment_bkp;
		}
		wp.media.editor.open(button);
		return false;
	});
	/*
	 * удаляем значение произвольного поля
	 * если быть точным, то мы просто удаляем value у input type="hidden"
	 */
	$('body').on('click', '.remove_image_button', function(){
		var r = confirm("Уверены?");
		if (r == true) {
			// получим класс блока для добавления картинки
			var cc = $(this).parent().parent().attr('class');
			if ($('.'+cc).length > 1 ) { // если есть другие блоки добавления картинки
				$(this).parent().parent().remove(); // удаляем блок
			} else {
				// если это единственный блок добавления картинки - очистим значения
				var src = $(this).parent().prev().attr('data-src');
				$(this).parent().prev().attr('src', src);
				$(this).prev().prev().val('');
			}
		}
		return false;
	});
	
	$('body').on('click', '.add_image_button', function(){
		console.log("Клик по добавить class-add-custom-fields.js!");
		var cc = $(this).parent().parent().clone();
		$(this).parent().parent().after(cc);
	});
})});