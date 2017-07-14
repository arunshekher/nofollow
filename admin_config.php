<?php

// Generated e107 Plugin Admin Area 

require_once('../../class2.php');
if ( ! getperms('P')) {
	e107::redirect('admin');
	exit;
}

e107::lan('nofollow', 'admin', true);


class nofollow_adminArea extends e_admin_dispatcher
{

	protected $modes = [

		'main' => [
			'controller' => 'nofollow_ui',
			'path'       => null,
			'ui'         => 'nofollow_form_ui',
			'uipath'     => null,
		],

	];

	/**
	 * Admin Menu
	 *
	 * @var type
	 */
	protected $adminMenu = [

		'main/prefs' => ['caption' => LAN_PREFS, 'perm' => 'P'],
		//'main/custom' => array('caption'=> 'Custom Page', 'perm' => 'P')

	];

	protected $adminMenuAliases = [
		'main/edit' => 'main/list',
	];

	/**
	 * Admin Menu title
	 *
	 * @var type
	 */
	protected $menuTitle = LAN_NOFOLLOW_PLUGIN_TITLE;
}


class nofollow_ui extends e_admin_ui
{
	/**
	 * @var type
	 */
	protected $pluginTitle = LAN_NOFOLLOW_PLUGIN_TITLE;

	/**
	 * @var type
	 */
	protected $pluginName = 'nofollow';

	/**
	 * @var type
	 */
	protected $parseMethods = [
		'regexHtmlParse_Nofollow'     => LAN_NOFOLLOW_REGEX_PARSER,
		'simpleHtmlDomParse_Nofollow' => LAN_NOFOLLOW_SIMPLE_HTML_DOM_PARSER,
	];

	/**
	 * @var type
	 */
	protected $fieldpref = [];

	/**
	 *
	 */
	//protected $preftabs = array('General', 'Other' );

	/**
	 * Plugin preferences
	 *
	 * @var type
	 */
	protected $prefs = [

		'active' => [
			'title' => LAN_NOFOLLOW_ACTIVATE,
			'tab'   => 0,
			'type'  => 'boolean',
			'data'  => 'int',
			'help'  => LAN_NOFOLLOW_HINT_ACTIVATE,
		],

		'ignore_pages' => [
			'title' => LAN_NOFOLLOW_EXCLUDE_PAGES,
			'tab'   => 0,
			'type'  => 'textarea',
			'data'  => 'str',
			'help'  => LAN_NOFOLLOW_HINT_EXCLUDE_PAGES,
		],

		'ignore_domains' => [
			'title' => LAN_NOFOLLOW_EXCLUDE_DOMAINS,
			'tab'   => 0,
			'type'  => 'textarea',
			'data'  => 'str',
			'help'  => LAN_NOFOLLOW_HINT_EXCLUDE_DOMAINS,
		],

		'parse_method' => [
			'title' => LAN_NOFOLLOW_PARSE_METHOD_TO_USE,
			'tab'   => 0,
			'type'  => 'dropdown',
			'size'  => 'xxlarge',
			'data'  => 'str',
			'help'  => LAN_NOFOLLOW_HINT_PARSE_METHOD,
		],
	];


	public function init()
	{
		$this->prefs['parse_method']['writeParms'] = $this->parseMethods;

	}


	// ------- Customize Create --------

	public function beforeCreate($new_data, $old_data)
	{
		return $new_data;
	}


	public function afterCreate($new_data, $old_data, $id)
	{
		// do something
	}


	public function onCreateError($new_data, $old_data)
	{
		// do something
	}


	// ------- Customize Update --------

	public function beforeUpdate($new_data, $old_data, $id)
	{
		return $new_data;
	}


	public function afterUpdate($new_data, $old_data, $id)
	{
		// do something
	}


	public function onUpdateError($new_data, $old_data, $id)
	{
		// do something
	}


}


class nofollow_form_ui extends e_admin_form_ui
{

}


new nofollow_adminArea();

require_once(e_ADMIN . "auth.php");
e107::getAdminUI()->runPage();

require_once(e_ADMIN . "footer.php");
exit;
