<?php
/*
Plugin Name: 3Whale
Plugin URI: 
Description: 3Whale
Author: hacpatb
Version: 0.2 beta
Author URI: 
*/
class plugin_class 
{
	var $page_title;
	var $menu_title;
	var $access_level; // 10 = admin 0 = подписчик 7 = редактор 
	var $add_page_to; // 1=add_menu_page 2=add_options_page 3=add_theme_page 4=add_management_page 5=plugin
	var $short_description;

	var $url_youtube;	//ссылка на видео 
	var $is_active;		//активноа или нет 

	function plugin_class() 
	{
		$this->get_options();
	}
	
	function get_options()
	{
		$options = get_option('3Whale_options');
		
		$this->url_youtube = $options['url_youtube'];
		$this->is_active = $options['is_active'];

	}

	function add_admin_menu()
	{
		if ( $this->add_page_to == 1 )
			add_menu_page($this->page_title, $this->menu_title, $this->access_level, __FILE__, array($this, 'admin_page'));
		elseif ( $this->add_page_to == 2 )
			add_options_page($this->page_title, $this->menu_title, $this->access_level, __FILE__, array($this, 'admin_page'));
		elseif ( $this->add_page_to == 3 )
			add_management_page($this->page_title, $this->menu_title, $this->access_level, __FILE__, array($this, 'admin_page'));			
		elseif ( $this->add_page_to == 4 )
			add_theme_page($this->page_title, $this->menu_title, $this->access_level, __FILE__, array($this, 'admin_page'));
		elseif ( $this->add_page_to == 5 )
			add_submenu_page('plugins.php', $this->page_title, $this->menu_title, $this->access_level, __FILE__, array($this, 'admin_page'));
	}

	function admin_page()
	{
		if(!wp_script_is('jquery')) {wp_enqueue_script('jquery');}

		echo <<<EOF
		<div class="wrap"> 
		<h2>{$this->page_title}</h2>
EOF;
		if (isset($_POST['UPDATE'])) ### обновление
		{
			echo '<div id="message" class="updated fade"><p><strong>Обновлено!</strong></p></div>';
			$url_temp = str_replace('\"',' ',$_POST['url_youtube']);
			$this->url_youtube = trim($url_temp,' ');
			$this->is_active = trim($_POST['is_active']);

			$options = array (
				'url_youtube' => $this->url_youtube,
				'is_active' => $this->is_active
			);
			update_option('3Whale_options', $options, 'Настройки плагина 3х китов', 'yes');
			//Обновление страницы для сброса кеша
			//$id_home_page = (int)get_option( 'page_on_front' );
			$my_post = array(
				'ID' => 46213
			);
			wp_update_post( $my_post );

		}
		$this->view_options_page();
		echo '</div>';
	}

	function view_options_page() 
	{
		echo <<<EOF
		<h3>Опции</h3>
		<form action="" method="POST">
			<table class="form-table">
				<tr><th>URL Youtube:</label></th>
					<td><input type="text" id="url_youtube" name="url_youtube" class="regular-text code" size="40" value="{$this->url_youtube}"></td>
				</tr>
EOF;
echo '<tr>
		<th>Показывать плеер:</th>
		<td><label for="is_active">
		<input type="radio" id="is_active_n" name="is_active" value="0"'.((!$this->is_active)?' checked':'').'> Нет</label> 
		<label for="description_y">
		<input type="radio" id="is_active_y" name="is_active" value="1"'.(($this->is_active)?' checked':'').'> Да</label>
		</td>
	</tr>';
echo '</table>';
		echo '
			<div id="err" class="error notice" style="display:none;"><p><strong>А ссылочка то кривая</strong></p></div>
			<p class="submit">
				<input type="submit" name="UPDATE" class="button-primary" value="Готово" />
			</p>
		</form>';
		echo '
	<p><h3>Инструкция для самых маленьких</h3></p>
	<ol>
		<li>Жмем на кнопку поделиться;</li>
		<li>Выбираем "HTML - код";</li>
		<li>Копируем кусок кода выделенный красным маркером на изображении.</li>
	</ol>
	<img src="'.plugins_url('Manual.jpg', __FILE__).'" alt="Инструкция для самых умных">';

	echo '<script src="'.plugins_url('script.js', __FILE__).'"></script>';
	}
	function activate()
	{
		$options = array (
			'url_youtube' => 'https://www.youtube.com/embed/OWFBqiUgspg?rel=0',
			'is_active' => false
		);
		add_option('3Whale_options', $options, 'Настройки плагина 3х китов', 'yes');
		$this->get_options();
	}

	function deactivate()
	{
		delete_option('3Whale_options');
	}

	function whale_player( $atts ) 
	{
		$options = get_option('3Whale_options');
		if($options['is_active'] == true)
		{
			return'<center><iframe width="850" height="480" src="'.$options['url_youtube'].'" frameborder="0" allowfullscreen></iframe></center>';
		}
		return;
	}

}

$my_plugin = new plugin_class();

$path_to_php_file_plugin = basename(__FILE__); //'3Whale.php'

$my_plugin->page_title = '3 Кита'; // название плагина (заголовок)
$my_plugin->menu_title = '3 Кита'; // название в меню
$my_plugin->access_level = 7; // уровень доступа
$my_plugin->add_page_to = 2; // куда добавлять страницу: 1=главное меню 2=настройки 3=управление 4=шаблоны 5=плагины

// короткое описание плагина
$my_plugin->short_description = 'Плагин добавляет шорткод для плеера 3х китов';


##################################################################

add_action('admin_menu', array($my_plugin, 'add_admin_menu'));
add_action('deactivate_' . $path_to_php_file_plugin, array($my_plugin, 'deactivate')); 
add_action('activate_' . $path_to_php_file_plugin, array($my_plugin, 'activate')); 
add_shortcode('3WhalePlayer', array($my_plugin, 'whale_player'));

?>