<?php 
/*
 * Catalan language file
 */
  $catalan = array(
		'thewire_tools' => "Eines de The Wire",
		'thewire_tools:no_result' => "No s'han trobat actualitzacions d'estat",	
		'thewire_tools:login_required' => "Heu d'estar connectada per utilitzar aquesta funcionalitat",
	
		// menu
		'thewire_tools:menu:mentions' => "Interaccions",
	
		// settings
		'thewire_tools:general_settings' => 'Configuració general',
		'thewire_tools:activity_settings' => 'Configuració relacionada amb el canal de notícies',
		'thewire_tools:group_settings' => 'Configuració relacionada amb els grups',
		'thewire_tools:menu_settings' => 'Configuració dels elements de menú',
		'thewire_tools:settings:enable_site_menu_item' => 'Habilita entrada al menú del lloc',
		'thewire_tools:settings:enable_group' => "Habilita els missatges d'estat per als grups",
		'thewire_tools:settings:enable_group_menu_item' => "Habilita entrada al menú del grup",
		'thewire_tools:settings:extend_widgets' => "Estén el giny d'estat amb l'opció d'enviar una actualització directament des del giny",
		'thewire_tools:settings:extend_activity' => "Estén el canal de notícies amb el formulari d'estat",
		'thewire_tools:settings:extend_group_activity' => "Estén les publicacions recents del grup amb el formulari d'estat", 
		'thewire_tools:settings:textarea_label' => 'Etiqueta del camp de text',
		'thewire_tools:settings:textarea_label_hint' => 'Etiqueta que es mostrarà acompanyant el camp de text al giny. Podeu escriure un identificador de cadena per agafar el text del fitxer d\'idioma. Deixeu-ho buit perquè es mostri el text per defecte',
		'thewire_tools:settings:default_textarea_label' => 'Actualitzeu l\'estat',
		'thewire_tools:settings:group_textarea_label' => 'Etiqueta del camp de text per als grups',
		'thewire_tools:settings:group_textarea_label_hint' => 'Etiqueta que es mostrarà acompanyant el camp de text al giny quan es mostri en un grup. Podeu escriure un identificador de cadena per agafar el text del fitxer d\'idioma. Deixeu-ho buit perquè es mostri el text per defecte',
	    'thewire_tools:settings:default_group_textarea_label' => 'Escriviu alguna publicació', 	
	
		// notification
		// mention
		'thewire_tools:notify:mention:subject' => "Teniu una interacció",
		'thewire_tools:notify:mention:message' => "Hola %s,

%s us ha citat en la seva actualització d'estat.

Per veure les vostres mencions feu clic aquí:
%s",

		// user settings
		'thewire_tools:usersettings:notify_mention' => "Vull rebre notificacions quan em citin en una actualització d'estat",
		
		// group wire
		'thewire_tools:group:title' => "Actualitzacions d'estat del grup",
		'thewire_tools:groups:tool_option' => "Habilita les actualitzacions d'estat als grups",
		'thewire_tools:groups:error:not_enabled' => "Les actualitzacions d'estat s'han deshabilitat en aquest grup",
		
		// search
		'thewire_tools:search:title' => "Cerca a les actualitzacions d'estat: '%s'",
		'thewire_tools:search:title:no_query' => "Cerca",
		'thewire_tools:search:no_query' => "Per cercar a les actualitzacions d'estat, si us plau introduïu el text de cerca a sobre",
				
		// widget
		// thewire_groups
		'widgets:thewire_groups:title' => "Actualitzacions d'estat del grup",
		'widgets:thewire_groups:description' => "Mostra les actualitzacions d'estat al grup",
		
		// index_thewire
		'widgets:index_thewire:title' => "Actualitzacions d'estat",
		'widgets:index_thewire:description' => "Mostra les darreres actualitzacions d'estat a la vostra comunitat",
		
		// the wire post
		'widgets:thewire_post:title' => "Actualització del canal de notícies",
		'widgets:thewire_post:description' => "Actualitzeu el vostre estat al canal de notícies amb aquest giny",
	
		// the wire (default widget)
		'widgets:thewire:owner' => "Lloc on mostrar les actualitzacions d'estat",
		'widgets:thewire:filter' => "Filtra les actualitzacions d'estat (opcional)",
			
	);

	add_translation("ca", $catalan);
