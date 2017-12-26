<?php
if ( ! defined('ABSPATH') ) { exit; } // Защита от прямого вызова скрипта
/* Класс упрощающий добавление доп.полей. За основу взяты функции с сайтов:
* http://truemisha.ru/blog/wordpress/meta-boxes.html
* http://gnufree.ru/%D0%B4%D0%BE%D0%B1%D0%B0%D0%B2%D0%BB%D0%B5%D0%BD%D0%B8%D0%B5-%D0%B7%D0%B0%D0%B3%D1%80%D1%83%D0%B7%D1%87%D0%B8%D0%BA%D0%B0-%D0%B8%D0%B7%D0%BE%D0%B1%D1%80%D0%B0%D0%B6%D0%B5%D0%BD%D0%B8%D0%B9-%D0%B2/
*	Пример использования:
*	get_post_meta($post_id, $key, $single);
*
*	$post_id (целое) (обязательное) - ID поста или страницы,
*	$key (строка) (обязательное) - значение произвольного поля,
*	$single (логическое)
*		если true – возвращает строку, false – массив, по умолчанию – false;
*
*	Пример использования – выведем значение произвольного поля meta1_field_1:
*	
*	echo get_post_meta($post->ID, 'meta1_field_1', true);
*/

function mfwp_true_include_myuploadscript() {
	// у вас в админке уже должен быть подключен jQuery, если нет - раскомментируйте следующую строку:
	wp_enqueue_script('jquery');
	// дальше у нас идут скрипты и стили загрузчика изображений WordPress
	if ( ! did_action( 'wp_enqueue_media' ) ) {
		wp_enqueue_media();
	}
	// само собой - меняем class-add-custom-fields.js на название своего файла
 	wp_enqueue_script( 'myuploadscript', plugins_url('/class-add-custom-fields.js', __FILE__), array('jquery'), null, false );
}
add_action( 'admin_enqueue_scripts', 'mfwp_true_include_myuploadscript' );

class mfwp_trueMetaBox {
 function __construct($options) {
	$this->options = $options;
	$this->prefix = $this->options['id'] .'_';
	add_action( 'add_meta_boxes', array( &$this, 'create' ) );
	add_action( 'save_post', array( &$this, 'save' ), 1, 2 );
 }
 function create() {
	foreach ($this->options['post'] as $post_type) {
		if (current_user_can( $this->options['cap'])) {
			add_meta_box($this->options['id'], $this->options['name'], array(&$this, 'fill'), $post_type, $this->options['pos'], $this->options['pri']);
		}
	}
 }
 function fill(){
	global $post; $p_i_d = $post->ID;
	wp_nonce_field( $this->options['id'], $this->options['id'].'_wpnonce', false, true );
	?>
	<table class="form-table"><tbody><?php
	foreach ( $this->options['args'] as $param ) {
		if (current_user_can( $param['cap'])) {
			if (!isset($param['default_val'])) { $param['default_val'] = ''; } // default_val - значение по умолчанию			
			if ((!isset($param['required'])) || ($param['required'] !== true)) {$required = '';} else {$required = 'required';} // если мы не указали, обязательно ли поле
			if (!isset($param['class'])) { $param['class'] = ''; }		
		?><tr class="<?php echo $param['class']; ?>"><?php
			if(!$value = get_post_meta($post->ID, $this->prefix .$param['id'] , true)) $value = $param['default_val'];
			switch ( $param['type'] ) {
				case 'text':{ ?>
					<th scope="row"><label for="<?php echo $this->prefix .$param['id'] ?>"><?php echo $param['title'] ?></label></th>
					<td>
						<input <?php echo $required; ?> name="<?php echo $this->prefix .$param['id'] ?>" type="<?php echo $param['type'] ?>" id="<?php echo $this->prefix .$param['id'] ?>" value="<?php echo $value ?>" placeholder="<?php echo $param['placeholder'] ?>" class="form-required" /><br />
						<span class="description"><?php echo $param['desc'] ?></span>
					</td>
					<?php
					break;							
				}
				case 'textarea':{ ?>
					<th scope="row"><label for="<?php echo $this->prefix .$param['id'] ?>"><?php echo $param['title'] ?></label></th>
					<td>
						<textarea <?php echo $required; ?> name="<?php echo $this->prefix .$param['id'] ?>" type="<?php echo $param['type'] ?>" id="<?php echo $this->prefix .$param['id'] ?>" value="<?php echo $value ?>" placeholder="<?php echo $param['placeholder'] ?>" class="large-text" /><?php echo $value ?></textarea><br />
						<span class="description"><?php echo $param['desc'] ?></span>
					</td>
					<?php
					break;							
				}		
				case 'checkbox':{ 
					/* Вся магия в 2-х чекбоксах. Второй скрыт, но у него все те же параметры.
					*  За счет этого получается, что если чекбок не отмечен, данные все равно
					*  попадают в произвольное поле. Т.е. всегда либо true либо false
					*/
					?>
					<th scope="row"><label for="<?php echo $this->prefix .$param['id'] ?>"><?php echo $param['title'] ?></label></th>
					<td>
						<label for="<?php echo $this->prefix .$param['id'] ?>"><input <?php echo $required; ?> onchange="var vv = this.id; if(!document.getElementById(vv+'_2').checked) { document.getElementById(vv+'_2').checked='checked'}else{ document.getElementById(vv+'_2').checked='';}" name="<?php echo $this->prefix .$param['id'] ?>" type="<?php echo $param['type'] ?>" id="<?php echo $this->prefix .$param['id'] ?>"<?php echo ($value=='true') ? ' checked="checked"' : '' ?> value="true" />							
						<input style="display: none;" name="<?php echo $this->prefix .$param['id'] ?>"  type="<?php echo $param['type'] ?>" id="<?php echo $this->prefix .$param['id'].'_2'; ?>" <?php if (($value=='false') || ($value!=='true')) {echo ' checked="checked"';} ?> value="false" />
						<?php echo $param['desc'] ?></label>
					</td>
					<?php
					break;							
				}
				case 'images':{ ?>
					<th scope="row"><label><?php echo $param['title'] ?></label>
					<br><span class="description"><?php echo $param['desc'] ?></span></th>
					<td>
					<?php
					$default = plugin_dir_url(__FILE__).'img/no-img.png'; // если картинки нет
					if (( $value ) && ($value !=='a:1:{i:0;s:0:"";}')){ // если допполе уже существует и массив не пуст
						$arr_id_img = unserialize($value); // преобразуем строку из допполя в массив
						foreach ($arr_id_img as $one_img_id) { //стартуем перебор
							$image_attributes = wp_get_attachment_image_src( $one_img_id, array(130, 130) );
							$src = $image_attributes[0]; // урл картинки
							?>
							<div class="cacf">
								<img data-src="<?php echo $default; ?>" src="<?php echo $src; ?>" width="130px" height="130px" />
								<div>
								<input type="hidden" name="<?php echo $this->prefix .$param['id'] ?>[]" class="<?php echo $this->prefix .$param['id']; ?>" value="<?php echo $one_img_id; ?>" />
								<button style="padding-top: 3px;" type="button" class="upload_image_button button"><span class="dashicons dashicons-upload"></span></button>
								<button style="padding-top: 3px;" type="button" class="remove_image_button button"><span class="dashicons dashicons-no"></span></button>
								<button style="padding-top: 3px;" type="button" class="add_image_button button"><span class="dashicons dashicons-format-gallery"></span></button>			
								</div>
							</div>
							<?php
						}
					} else {
					$src = $default; ?>				
						<div class="cacf">
							<img data-src="<?php echo $default; ?>" src="<?php echo $src; ?>" width="130px" height="130px" />
							<div>
								<input type="hidden" name="<?php echo $this->prefix .$param['id'] ?>[]" class="<?php echo $this->prefix .$param['id']; ?>" value="" />
								<button style="padding-top: 3px;" type="button" class="upload_image_button button"><span class="dashicons dashicons-upload"></span></button>
								<button style="padding-top: 3px;" type="button" class="remove_image_button button"><span class="dashicons dashicons-no"></span></button>
								<button style="padding-top: 3px;" type="button" class="add_image_button button"><span class="dashicons dashicons-format-gallery"></span></button>			
							</div>
						</div>
						<?php 
					}
					?>					
					</td>
					<?php
					break;							
				}									
				case 'yadiv':{ ?>
					<div id="yamapCreate" style="float: left; width: 100%; max-width: 760px; height: 250px; border: 1px solid #DFDFDF;"></div>
					<?php
					break;							
				}					
				case 'select':{ ?>
					<th scope="row"><label for="<?php echo $this->prefix .$param['id'] ?>"><?php echo $param['title'] ?></label></th>
					<td>
						<label for="<?php echo $this->prefix .$param['id'] ?>">
						<select <?php echo $required; ?> name="<?php echo $this->prefix .$param['id'] ?>" id="<?php echo $this->prefix .$param['id'] ?>"><option></option><?php
							foreach($param['args'] as $val=>$name){
								?><option class="<?php echo $val ?>" value="<?php echo $val ?>"<?php echo ( $value == $val ) ? ' selected="selected"' : '' ?>><?php echo $name ?></option><?php
							}
						?></select></label><br />
						<span class="description"><?php echo $param['desc'] ?></span>
					</td>
					<?php
					break;							
				}
			} 
		?></tr><?php
		}
	}
 ?></tbody></table><?php
 }
 function save($post_id, $post){
	if ( !in_array($post->post_type, $this->options['post'])) return; // если не указано для каких страниц создавать доп.поле	
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE  ) return false;
	//if ( !wp_verify_nonce($_POST[ $this->options['id'].'_wpnonce' ], __FILE__) ) return false;
	//if ( !wp_verify_nonce( $_POST[ $this->options['id'].'_wpnonce' ], $this->options['id'] ) ) return; // если страница поддельна
	if ( !current_user_can( 'edit_post', $post_id ) ) return; // если у юзера нет прав

	foreach ( $this->options['args'] as $param ) {
		if ( current_user_can( $param['cap'] ) ) { // если юзер обладает правами, которые мы задали в настройках произвольного поля		
			/*
			* Далее логика такая:
			* Если какой-то из параметров $_POST - массив, значит у нас массив с картинками
			* все другие поля - обычные строки
			* правда пока убрал проверку на && trim( $_POST[ $this->prefix . $param['id'] ] )
			*
			*/
			if (isset($_POST[$this->prefix . $param['id']])) { // если данные по допполю передали при сохранении поста
				// проверим, массив ли это с картинками
				// если массив - 1, если строка - 2
				is_array($_POST[$this->prefix . $param['id']]) ? $isarr_of_img=1 : $isarr_of_img=2;			
				if ($isarr_of_img == 1) { // у нас в массиве лежат id картинок
					// преобразуем массив в строку и записываем в доп.поле (в базу)
					update_post_meta( $post_id, $this->prefix . $param['id'], serialize($_POST[ $this->prefix . $param['id']]));					
				} else { // если у нас не массив, то это не картинки. Вместо serialize применям trim и пишем в базу
					update_post_meta( $post_id, $this->prefix . $param['id'], trim($_POST[ $this->prefix . $param['id'] ]) );
				}
			} else {
				// если допполе не заполнили, то удаляем его
				delete_post_meta( $post_id, $this->prefix . $param['id'] );
			}
		}
	}
 }
}
?>