<?php 
/*
	
	wp-curriculos - wordpress plugin to create resumes
	Copyright (C) 2011  Billy Blay

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

	Plugin Name: WP-curriculos
	Plugin URI: http://www.billyblay.com/wordpress/plugin/wpcurriculos
	Description: 
	Version: 0.1
	Author: Billy Blay
	Author URI: http://www.billyblay.com/

*/

$wp_plugin_url = trailingslashit(get_bloginfo('wpurl')).PLUGINDIR.'/'.dirname(plugin_basename(__FILE__));
add_action('save_post', 'wpcurriculos_save_postdata');

/* ================================= Variaveis =========== */

$estadocivil	= array( 'Viúvo','Casado','Solteiro','Divorciado');
$tipotel			= array( 'Celular','Residencial','Comercial','Referência');
$idioma 	 		= array( 'Inglês','Francês','Árabe','Espanhol');
$nivel 			= array( 'Básico','Intermediário','fluente');
$modelos 		= array( 'Intermediário');
$tipoensino		= array( 'Fundamental','Médio','Graduação','Especialização','Doutorado','Mestrado');
$anoatual		= array( '1','2','3','4','5','Formado');

/* ================================= custom post type ============ */

add_action( 'init', 'create_post_type' );
function create_post_type() {
  $labels = array(
    'name' => _x('Currículos', 'post type general name'),
    'singular_name' => _x('Currículo', 'post type singular name'),
    'add_new' => _x('Adicionar novo', 'property'),
    'add_new_item' => __('Adicionar novo curriculo'),
    'edit_item' => __('Editar currículo'),
    'new_item' => __('Novo'),
    'view_item' => __('Visualizar'),
    'search_items' => __('Buscar'),
    'not_found' =>  __('Nenhum currículo encontrado'),
    'not_found_in_trash' => __('Nenhum currículo na lixeira'), 
    'parent_item_colon' => ''
  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, // UI in admin panel
    'query_var' => true,
    'rewrite' => array("slug" => "property"), // Permalinks format
    'capability_type' => 'post',
    'hierarchical' => false,
    'menu_position' => 1,
    'supports' => array('title'),
	'_builtin' => false, // It's a custom post type, not built in!
    '_edit_link' => 'post.php?post=%d',
	'exclude_from_search' => true
  ); 
  register_post_type('property',$args);
}

/* ================================= informações pessoais =========== */

add_action('admin_menu', 'wpcurriculos_add_custom_box_pessoais');
function wpcurriculos_add_custom_box_pessoais() {
	if( function_exists( 'add_meta_box' )) {
		add_meta_box( 'wpcurriculos_pessoais', __( 'Dados pessoais', 'wpcurriculos_textdomain' ), 'pessoais','property', 'advanced');
	}
}
function pessoais() {
	global $post, $estadocivil;
	$textfield2 = stripslashes(get_post_meta($post->ID, 'textfield2', true));
	$textfield3 = stripslashes(get_post_meta($post->ID, 'textfield3', true));
	$sexo  		= stripslashes(get_post_meta($post->ID, 'sexo', true));
	$e_civil  	= stripslashes(get_post_meta($post->ID, 'e_civil', true));
	$email 		= stripslashes(get_post_meta($post->ID, 'email', true));

	echo '<input type="hidden" name="pessoais_noncename" id="pessoais_noncename" value="'. wp_create_nonce( plugin_basename(__FILE__) ) . '" />'; ?>

<fieldset id="pessoais" class="cv">
<ol>
  <li>
    <label for="textfield2">Data de nascimento</label>
    <input type="text" name="textfield2" id="textfield2" class="data" value="<?php echo $textfield2; ?>" />
  </li>
  <li>
    <label for="textfield3">Nacionalidade</label>
    <input type="text" name="textfield3" id="textfield3" value="<?php echo $textfield3; ?>" />
  </li>
  <li>
    <label for="select">Estado civil</label>
    <select name="e_civil" id="select">
      <?php 
	  foreach ($estadocivil as $value) {
      	echo '<option value="'.$value.'" ';
		if ($e_civil == $value) { echo  'selected="selected"'; }
		echo '>'. $value .'(a)</option>';
      } ?>
    </select>
  </li>
  <li>
    <fieldset>
    <legend>Sexo</legend>
    <ul>
      <li> <label>
        <input type="radio" name="sexo" id="f" value="masculino" <?php if ($sexo == "masculino") { echo ' checked = "checked" ';} ?> />
       Masculino</label>
      </li>
      <li>
       <label>
        <input type="radio" name="sexo" id="m" value="feminino" <?php if ($sexo == "feminino") { echo ' checked = "checked" ';} ?> />
       Feminino</label>
      </li>
      
    </ul>
    </fieldset>
  </li>
   <li>
    <label for="email">E-mail</label>
    <input type="text" name="email" id="email" value="<?php echo $email; ?>" />
  </li>
</ol>
</fieldset>
<?php
}
/* ================================= informações residenciais =========== */

add_action('admin_menu', 'wpcurriculos_add_custom_box_residenciais');
function wpcurriculos_add_custom_box_residenciais() {
	if( function_exists( 'add_meta_box' )) {
		add_meta_box( 'wpcurriculos_residenciais', __( 'Dados residenciais', 'wpcurriculos_textdomain' ), 'residenciais', 'property', 'advanced');}
}
function residenciais() {
	global $post, $tipotel;
	$textfield5 = stripslashes(get_post_meta($post->ID, 'textfield5', true));
	$textfield6 = stripslashes(get_post_meta($post->ID, 'textfield6', true));
	$textfield7 = stripslashes(get_post_meta($post->ID, 'textfield7', true));
	$textfield8 = stripslashes(get_post_meta($post->ID, 'textfield8', true));
	$textfield9 = stripslashes(get_post_meta($post->ID, 'textfield9', true));
	$select4 	= stripslashes(get_post_meta($post->ID, 'select4', true));

?>
<fieldset id="residenciais" class="cv">
<ol>
  <li>
    <label for="textfield5">Estado</label>
    <input type="text" name="textfield5" id="textfield5" value="<?php echo $textfield5; ?>" />
  </li>
  <li>
    <label for="textfield6">Cidade</label>
    <input type="text" name="textfield6" id="textfield6" value="<?php echo $textfield6; ?>" />
  </li>
  <li>
    <label for="textfield7">Bairro</label>
    <input type="text" name="textfield7" id="textfield7" value="<?php echo $textfield7; ?>" />
  </li>
  <li>
    <label for="textfield8">Endereço</label>
    <input type="text" name="textfield8" id="textfield8" value="<?php echo $textfield8; ?>" />
  </li>
  <li>
    <fieldset id="telefones">
    <legend>Telefone</legend>
    <ul>
      <li>
        <label for="textfield9">Número</label>
        <input type="text" name="textfield9" id="textfield9" class="fone" value="<?php echo $textfield9; ?>" />
      </li>
      <li>
        <label for="select4">Tipo</label>
        <select name="select4" id="select4">
          <?php 
		  foreach ($tipotel as $value) {
			echo '<option value="'.$value.'" ';
			if ($select4 == $value) { echo  'selected="selected"'; }
			echo '>'. $value .'</option>';
		  } ?>
        </select>
      </li>
      <li>
        <input name="input5" type="button" value="Adicionar" />
        </li>
    </ul>
    <fieldset>
    <legend>Telefones cadastrados</legend>
    <ol>
      <li><span>(88)8888-8888</span> <span>Residencial</span> <a href="#">Excluir</a></li>
      <li><span>(99)9999-9999</span> <span>Comercial</span> <a href="eventos.html">Excluir</a></li>
      <li><span>(99)9999-9999</span> <span>Celular</span> <a href="eventos.html">Excluir</a></li>
    </ol>
    </fieldset>
    </fieldset>
  </li>
</ol>
</fieldset>
<?php
}
/* ================================= Objetivos =========== */

add_action('admin_menu', 'wpcurriculos_add_custom_box_objetivos');
function wpcurriculos_add_custom_box_objetivos() {
	if( function_exists( 'add_meta_box' )) {
		add_meta_box( 'wpcurriculos_objetivos', __( 'Objetivos Profissionais', 'wpcurriculos_textdomain' ), 'objetivos', 'property', 'advanced');}
}
function objetivos() {
	global $post;
	$profissao  	= stripslashes(get_post_meta($post->ID, 'profissao', true));
	$textarea3  	= stripslashes(get_post_meta($post->ID, 'textarea3', true));

	?>
<div class="ajuda"><p>Logo abaixo dos dados pessoais, é preciso informar, de maneira clara e sucinta, qual o seu objetivo profissional, ou seja, qual profissão ou área deseja ocupar.</p>
  
<p>É importante que o objetivo profissional esteja bem definido. Um objetivo muito amplo, ao contrário do que se pensa, vai diminuir a probabilidade do currículo ser selecionado.</p>
  
<p>A maioria das empresas recebe, diariamente, vários currículos de candidatos que os enviam antes mesmo de surgir alguma vaga. Quando a vaga é anunciada, essa quantidade de currículos se multiplica. Assim, um currículo com objetivo profissional exposto de forma genérica tende a fazer com que os avaliadores o descartem de cara, para evitar perda de tempo. Eles procuram algo que se encaixe perfeitamente na vaga que pretendem preencher.</p>
  </div>
  <fieldset id="objetivos" class="cv">

<ol>
  <li>
    <label>Profissão desejada
    <input type="text" name="profissao" id="profissao" value="<?php echo $profissao; ?>" /></label>
    <input name="input4" type="button" value="Adicionar" />
    <fieldset>
    <legend>Profissões escolhidas</legend>
    <ol>
      <li>Webdesigner <a href="#">Excluir</a></li>
      <li>Desenvolvedor web <a href="#">Excluir</a></li>
      <li>Programador <a href="#">Excluir</a></li>
    </ol>
    </fieldset>
  </li>
  <li>
      <label>Objetivos
      <textarea name="textarea3" id="textarea3" cols="45" rows="5"><?php echo $textarea3; ?></textarea></label>

      </li>
</ol>
</fieldset>
<?php
}

/* ================================= Idiomas =========== */

add_action('admin_menu', 'wpcurriculos_add_custom_box_idiomas');
function wpcurriculos_add_custom_box_idiomas() {
	if( function_exists( 'add_meta_box' )) {
		add_meta_box( 'wpcurriculos_idiomas', __( 'Idiomas', 'wpcurriculos_textdomain' ), 'idiomas', 'property', 'advanced');}
}
function idiomas() {
	global $post, $idioma, $nivel;
	$select5  	= stripslashes(get_post_meta($post->ID, 'select5', true));
	$select6  	= stripslashes(get_post_meta($post->ID, 'select6', true));
	?>
<div class="ajuda">
      <p>Indique o idioma, colocando ao lado seu grau de conhecimento: básico, intermediário e fluente. O inglês é fundamental para a maioria dos cargos, principalmente nos níveis gerenciais. Ao mencioná-lo, provavelmente você passará por um teste prático.</p>
    </div>
    
    <fieldset id="idiomas" class="cv">

<ol>
  <li>
    <label for="select5">Idioma</label>
    <select name="select5" id="select5">
      <?php 
	  foreach ($idioma as $value) {
      	echo '<option value="'.$value.'" ';
		if ($select5 == $value) { echo  'selected="selected"'; }
		echo '>'. $value .'</option>';
      } ?>
    </select>
  </li>
  <li>
    <label for="select6">Nível</label>
    <select name="select6" id="select6">
      <?php 
	  foreach ($nivel as $value) {
      	echo '<option value="'.$value.'" ';
		if ($select6 == $value) { echo  'selected="selected"'; }
		echo '>'. $value .'</option>';
      } ?>
    </select>
  </li>
  <li>
  	<input name="" type="text" width="80" id="resultado" />
    <input id="input3" type="button" value="Adicionar" />
  </li>
</ol>
<fieldset>
<legend>Meus Idiomas</legend>
<ol>
  <li><span>Inglês</span> <span>Básico</span> <a href="#">Excluir</a></li>
  <li><span>Espanhol</span> <span>Intermediário</span> <a href="eventos.html">Excluir</a></li>
  <li><span>Francês</span> <span>Avançado</span> <a href="eventos.html">Excluir</a></li>
</ol>
</fieldset>
</fieldset>
<?php
}

/* ================================= Escolaridade =========== */

add_action('admin_menu', 'wpcurriculos_add_custom_box_escolaridade');
function wpcurriculos_add_custom_box_escolaridade() {
	if( function_exists( 'add_meta_box' )) {
		add_meta_box( 'wpcurriculos_escolaridade', __( 'Escolaridade', 'wpcurriculos_textdomain' ), 'escolaridade', 'property', 'advanced');}
}
function escolaridade() {
	global $post, $tipoensino, $anoatual;
	$ensino  	 = stripslashes(get_post_meta($post->ID, 'ensino', true));
	$textfield10 = stripslashes(get_post_meta($post->ID, 'textfield10', true));
	$textfield11 = stripslashes(get_post_meta($post->ID, 'textfield11', true));
	$textfield12 = stripslashes(get_post_meta($post->ID, 'textfield12', true));
	$textfield13 = stripslashes(get_post_meta($post->ID, 'textfield13', true));	
	$ano  	 	 = stripslashes(get_post_meta($post->ID, 'ano', true));
	
	?>
	
<div id="escolaridade" class="cv">
<div class="ajuda">
      <p>Neste item estão o curso, nome da instituição e ano de conclusão. As informações devem estar de forma decrescente, do último curso para o primeiro.Não há necessidade de citar a escola em que fez o primeiro e o segundo graus, nem anexar cópias de diplomas e atestados.
        Cursos de pós-graduação, MBA e especialização são muito valorizados. </p>
      </div>
<fieldset>

<ol>
  <li>
    <label for="ensino">Tipo de ensino</label>
    <select name="ensino" id="ensino">
    	<?php 
	  foreach ($tipoensino as $value) {
      	echo '<option value="'.$value.'" ';
		if ($ensino == $value) { echo  'selected="selected"'; }
		echo '>'. $value .'</option>';
      } ?>
    </select>
  </li>
  <li>
    <label for="textfield10">Curso</label>
    <input type="text" name="textfield10" id="textfield10" value="<?php echo $textfield10; ?>" />
  </li>
  <li>
    <label for="textfield11">Instituição</label>
    <input type="text" name="textfield11" id="textfield11" value="<?php echo $textfield11; ?>" />
  </li>
  <li>
    <fieldset class="datas">
    <legend>Período</legend>
    <p>Digite Mês e ano (mm/aaaa)</p>
    <ul>
      <li>
        <label for="textfield12">Data de início<?php /*?> (mm/aaaa) <?php */?></label>
        <input type="text" name="textfield12" id="textfield12" class="periodo" value="<?php echo $textfield12; ?>" />
      </li>
      <li>
        <label for="textfield13">Data de conclusão<?php /*?> (mm/aaaa) <?php */?></label>
        <input type="text" name="textfield13" id="textfield13" class="periodo" value="<?php echo $textfield13; ?>" />
      </li>
    </ul>
    </fieldset>
  </li>
  <li>
  <label><input type="checkbox" id="ano" name="ano"  value="1" <?php if ($ano == 1) { echo 'checked="checked"';} ?> />Concluído</label>
  
   <label for="ano">Ano atual </label>
    <select name="ano" id="ano">
    <?php 
	  foreach ($anoatual as $value) {
      	echo '<option value="'.$value.'" ';
		if ($ano == $value) { echo  'selected="selected"'; }
		echo '>'. $value .'</option>';
      } ?>
    </select>
  </li>
  <li>
    <input name="input" type="button" value="Adicionar" />
  </li>
</ol>
</fieldset>
</div>
<?php
}

/* ================================= cursos complementares =========== */

add_action('admin_menu', 'wpcurriculos_add_custom_box_curso');
function wpcurriculos_add_custom_box_curso() {
	if( function_exists( 'add_meta_box' )) {
		add_meta_box( 'wpcurriculos_curso', __( 'Cursos complementares', 'wpcurriculos_textdomain' ), 'curso', 'property', 'advanced');}
}
function curso() {
	global $post;
	$textfield30 = stripslashes(get_post_meta($post->ID, 'textfield30', true));
	$textfield31 = stripslashes(get_post_meta($post->ID, 'textfield31', true));
	$textfield32 = stripslashes(get_post_meta($post->ID, 'textfield32', true));
	$textarea2 	 = stripslashes(get_post_meta($post->ID, 'textarea2', true));	

 ?>
	
<div id="cursos" class="cv">
<div class="ajuda">
      <p>Inicie mencionando os cursos mais condizentes com o seu objetivo. Coloque em ordem decrescente, o nome do curso, a instituição, ano e duração. Cursos de pós-graduação, MBA e especialização são muito valorizados. Não deixe de mencionar cursos que não tenham a ver com sua área, desde que cite como eles contribuíram para seu desenvolvimento. Por exemplo, como o curso de teatro ajudou-o a perder o medo e a inibição de falar em público e em reuniões. </p>   
    </div><fieldset>

<ol>
  <li>
    <label for="textfield30">Curso</label>
    <input type="text" name="textfield30" id="textfield30" value="<?php echo $textfield30; ?>" />
  </li>
  <li>
    <label for="textfield31">Instituição</label>
    <input type="text" name="textfield31" id="textfield31" value="<?php echo $textfield31; ?>" />
  </li>
   <li>
        <label for="textfield32">Carga horária</label>
        <input type="text" name="textfield32" id="textfield32" value="<?php echo $textfield32; ?>" />
      </li>
      <li>
      <label for="textarea2">Descrição</label>
      <textarea name="textarea2" id="textarea2" cols="45" rows="5"><?php echo $textarea2; ?></textarea>

      </li>
</ol>
</fieldset>
</div>
<?php
}


/* ================================= Experiência profissional =========== */

add_action('admin_menu', 'wpcurriculos_add_custom_box_experiencia');
function wpcurriculos_add_custom_box_experiencia() {
	if( function_exists( 'add_meta_box' )) {
		add_meta_box( 'wpcurriculos_experiencia', __( 'Experiência Profissional', 'wpcurriculos_textdomain' ), 'experiencia', 'property', 'advanced');}
}
function experiencia() {
	global $post;
	$textfield14 	= stripslashes(get_post_meta($post->ID, 'textfield14', true));
	$textfield15 	= stripslashes(get_post_meta($post->ID, 'textfield15', true));
	$textfield16 	= stripslashes(get_post_meta($post->ID, 'textfield16', true));
	$textfield17 	= stripslashes(get_post_meta($post->ID, 'textfield17', true));
	$textarea 		= stripslashes(get_post_meta($post->ID, 'textarea', true));
	$atual 			= stripslashes(get_post_meta($post->ID, 'atual', true));
	?>
<div id="experiencia" class="cv">
<div class="ajuda"><p>Estão relacionadas às empresas nas quais você trabalhou. Comece da última para a primeira empresa, colocando dados como nome da organização, cargo, tempo de trabalho e atividades / resultados. Coloque as datas de entrada e saída. Serviços temporários devem ser citados, deixando clara essa temporariedade. Para profissionais com mais de 20 anos no mercado de trabalho, geralmente não há necessidade de citar as primeiras empresas em que trabalhou.</p></div>
  <fieldset>
  
  <ol>
    <li>
       <label>
       <input type="checkbox" name="atual" id="atual" value="1" <?php if ($atual == 1) { echo 'checked="checked"';} ?> />
     Emprego atual</label>
    </li>
    <li>
      <label>Nome da empresa
      <input type="text" name="textfield14" id="textfield14" value="<?php echo $textfield14; ?>" /></label>
    </li>
    <li>
      <label for="textfield15">Último cargo</label>
      <input type="text" name="textfield15" id="textfield15" value="<?php echo $textfield15; ?>" />
    </li>
    <li>
      <fieldset class="datas">
      <legend>Período</legend>
      <p>Digite Mês e ano (mm/aaaa)</p>
      <ul>
        <li>
          <label for="textfield16">Data de início</label>
          <input type="text" name="textfield16" id="textfield16" class="periodo" value="<?php echo $textfield16; ?>" />
        </li>
        <li>
          <label for="textfield17">Data de conclusão</label>
          <input type="text" name="textfield17" id="textfield17" class="periodo" value="<?php echo $textfield17; ?>" />
        </li>
      </ul>
      </fieldset>
    </li>
    <li>
      <label for="textarea">Atividades desempenhadas</label>
      <textarea name="textarea" id="textarea" cols="45" rows="5"><?php echo $textarea; ?></textarea>
    </li>
    <li>
      <input name="input2" type="button" value="Adicionar" />
    </li>
  </ol>
  </fieldset>
</div>
<?php
}
/* ================================= Modelo =========== */

add_action('admin_menu', 'wpcurriculos_add_custom_box_modelo');
function wpcurriculos_add_custom_box_modelo() {
	if( function_exists( 'add_meta_box' )) {
		add_meta_box( 'wpcurriculos_modelo', __( 'Escolha um modelo', 'wpcurriculos_textdomain' ), 'modelo', 'property', 'advanced');}
}
function modelo() {
	global $post, $modelos;
	$select7  	= stripslashes(get_post_meta($post->ID, 'select7', true));
	?>
<fieldset id="modelo" class="cv">
<ol>
  <li>
    <label for="select7">Modelos Disponíveis</label>
    <select name="select7" id="select7">
      <?php 
	  foreach ($modelos as $value) {
      	echo '<option value="'.$value.'" ';
		if ($select7 == $value) { echo  'selected="selected"'; }
		echo '>'. $value .'</option>';
      } ?>
    </select>
  </li>
</ol>
</fieldset>
<?php
}

/* When the post is saved, saves our custom data */
function wpcurriculos_save_postdata( $post_id ) {

  if ( !wp_verify_nonce( $_POST['pessoais_noncename'], plugin_basename(__FILE__) )) { return $post_id;}

  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
  		
			$textfield2 = $_POST["textfield2"];
			delete_post_meta($post_id, 'textfield2');
			if (isset($textfield2) && !empty($textfield2)) 	{ add_post_meta($post_id, 'textfield2', $textfield2);}
			
			$textfield3 = $_POST["textfield3"];
			delete_post_meta($post_id, 'textfield3');
			if (isset($textfield3) && !empty($textfield3)) 	{ add_post_meta($post_id, 'textfield3', $textfield3);}
			$sexo = $_POST["sexo"];
			delete_post_meta($post_id, 'sexo');
			if (isset($sexo) && !empty($sexo)) 	   			{ add_post_meta($post_id, 'sexo', $sexo);}
			
			$e_civil = $_POST["e_civil"];
			delete_post_meta($post_id, 'e_civil');
			if (isset($e_civil) && !empty($e_civil)) 	   	{ add_post_meta($post_id, 'e_civil', $e_civil);}

			$textfield5 = $_POST["textfield5"];
			delete_post_meta($post_id, 'textfield5');
			if (isset($textfield5) && !empty($textfield5)) 	{ add_post_meta($post_id, 'textfield5', $textfield5);}
			
			$textfield6 = $_POST["textfield6"];
			delete_post_meta($post_id, 'textfield6');
			if (isset($textfield6) && !empty($textfield6)) 	{ add_post_meta($post_id, 'textfield6', $textfield6);}
			
			$textfield7 = $_POST["textfield7"];
			delete_post_meta($post_id, 'textfield7');
			if (isset($textfield7) && !empty($textfield7)) 	{ add_post_meta($post_id, 'textfield7', $textfield7);}
			
			$textfield8 = $_POST["textfield8"];
			delete_post_meta($post_id, 'textfield8');
			if (isset($textfield8) && !empty($textfield8)) 	{ add_post_meta($post_id, 'textfield8', $textfield8);}
			
			$textfield9 = $_POST["textfield9"];
			delete_post_meta($post_id, 'textfield9');
			if (isset($textfield9) && !empty($textfield9)) 	{ add_post_meta($post_id, 'textfield9', $textfield9);}

			$select4 = $_POST["select4"];
			delete_post_meta($post_id, 'select4');
			if (isset($select4) && !empty($select4)) 	{ add_post_meta($post_id, 'select4', $select4);}
			
			$select5 = $_POST["select5"];
			delete_post_meta($post_id, 'select5');
			if (isset($select5) && !empty($select5)) 	{ add_post_meta($post_id, 'select5', $select5);}

			$select6 = $_POST["select6"];
			delete_post_meta($post_id, 'select6');
			if (isset($select6) && !empty($select6)) 	{ add_post_meta($post_id, 'select6', $select6);}
			
			$select7 = $_POST["select7"];
			delete_post_meta($post_id, 'select7');
			if (isset($select7) && !empty($select7)) 	{ add_post_meta($post_id, 'select7', $select7);}
			
			$ensino = $_POST["ensino"];
			delete_post_meta($post_id, 'ensino');
			if (isset($ensino) && !empty($ensino)) 	{ add_post_meta($post_id, 'ensino', $ensino);}
			
			$textfield10 = $_POST["textfield10"];
			delete_post_meta($post_id, 'textfield10');
			if (isset($textfield10) && !empty($textfield10)) 	{ add_post_meta($post_id, 'textfield10', $textfield10);}
			
			$textfield11 = $_POST["textfield11"];
			delete_post_meta($post_id, 'textfield11');
			if (isset($textfield11) && !empty($textfield11)) 	{ add_post_meta($post_id, 'textfield11', $textfield11);}
			
			$textfield12 = $_POST["textfield12"];
			delete_post_meta($post_id, 'textfield12');
			if (isset($textfield12) && !empty($textfield12)) 	{ add_post_meta($post_id, 'textfield12', $textfield12);}
			
			$textfield13 = $_POST["textfield13"];
			delete_post_meta($post_id, 'textfield13');
			if (isset($textfield13) && !empty($textfield13)) 	{ add_post_meta($post_id, 'textfield13', $textfield13);}
			
			$textfield14 = $_POST["textfield14"];
			delete_post_meta($post_id, 'textfield14');
			if (isset($textfield14) && !empty($textfield14)) 	{ add_post_meta($post_id, 'textfield14', $textfield14);}
			
			$textfield15 = $_POST["textfield15"];
			delete_post_meta($post_id, 'textfield15');
			if (isset($textfield15) && !empty($textfield15)) 	{ add_post_meta($post_id, 'textfield15', $textfield15);}
			
			$textfield16 = $_POST["textfield16"];
			delete_post_meta($post_id, 'textfield16');
			if (isset($textfield16) && !empty($textfield16)) 	{ add_post_meta($post_id, 'textfield16', $textfield16);}
			
			$textfield17 = $_POST["textfield17"];
			delete_post_meta($post_id, 'textfield17');
			if (isset($textfield17) && !empty($textfield17)) 	{ add_post_meta($post_id, 'textfield17', $textfield17);}
			
			$ano = $_POST["ano"];
			delete_post_meta($post_id, 'ano');
			if (isset($ano) && !empty($ano)) 	{ add_post_meta($post_id, 'ano', $ano);}
			
			$textarea = $_POST["textarea"];
			delete_post_meta($post_id, 'textarea');
			if (isset($textarea) && !empty($textarea)) 	{ add_post_meta($post_id, 'textarea', $textarea);}
			
			$atual = $_POST["atual"];
			delete_post_meta($post_id, 'atual');
			if (isset($atual) && !empty($atual)) 	{ add_post_meta($post_id, 'atual', $atual);}
			
			$textfield30 = $_POST["textfield30"];
			delete_post_meta($post_id, 'textfield30');
			if (isset($textfield30) && !empty($textfield30)) 	{ add_post_meta($post_id, 'textfield30', $textfield30);}

			$textfield31 = $_POST["textfield31"];
			delete_post_meta($post_id, 'textfield31');
			if (isset($textfield31) && !empty($textfield31)) 	{ add_post_meta($post_id, 'textfield31', $textfield31);}

			$textfield32 = $_POST["textfield32"];
			delete_post_meta($post_id, 'textfield32');
			if (isset($textfield32) && !empty($textfield32)) 	{ add_post_meta($post_id, 'textfield32', $textfield32);}

			$textfield33 = $_POST["textfield33"];
			delete_post_meta($post_id, 'textfield33');
			if (isset($textfield33) && !empty($textfield33)) 	{ add_post_meta($post_id, 'textfield33', $textfield33);}
				
			$textarea2 = $_POST["textarea2"];
			delete_post_meta($post_id, 'textarea2');
			if (isset($textarea2) && !empty($textarea2)) 	{ add_post_meta($post_id, 'textarea2', $textarea2);}
			
			$profissao = $_POST["profissao"];
			delete_post_meta($post_id, 'profissao');
			if (isset($profissao) && !empty($profissao)) 	{ add_post_meta($post_id, 'profissao', $profissao);}
				
			$textarea3 = $_POST["textarea3"];
			delete_post_meta($post_id, 'textarea3');
			if (isset($textarea3) && !empty($textarea3)) 	{ add_post_meta($post_id, 'textarea3', $textarea3);}
			
			$email = $_POST["email"];
			delete_post_meta($post_id, 'email');
			if (isset($email) && !empty($email)) 	{ add_post_meta($post_id, 'email', $email);}
			
}
function StyleAction() {  
	global $post, $wp_plugin_url;
	if (is_admin()){ 
		wp_enqueue_style('wpcurriculos', $wp_plugin_url.'/css/style.css', false, '1.0', 'screen');
	}
}

function ScriptsAction() {  
	global $post, $wp_plugin_url;	
	if (is_admin()){ 
		wp_enqueue_script('maskedinput', $wp_plugin_url.'/js/jquery.maskedinput-1.1.2.js', array('jquery'),'1.1.2');
	}
}

add_action('wp_print_scripts', 'ScriptsAction');
add_action('admin_print_styles', 'StyleAction');
?>
