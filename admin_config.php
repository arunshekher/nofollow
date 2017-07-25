<?php
require_once('../../class2.php');
if ( ! getperms('P') || ! e107::isInstalled('nofollow')) {
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
	 * @var array
	 */
	protected $adminMenu = [

		'main/prefs' => ['caption' => LAN_PREFS, 'perm' => 'P'],
		'main/help'  => ['caption' => 'Help', 'perm' => 'P'],

	];

	protected $adminMenuAliases = [
		'main/edit' => 'main/list',
	];

	/**
	 * Admin Menu title
	 *
	 * @var string
	 */
	protected $menuTitle = LAN_NOFOLLOW_PLUGIN_TITLE;
}


class nofollow_ui extends e_admin_ui
{
	/**
	 * @var string
	 */
	protected $pluginTitle = LAN_NOFOLLOW_PLUGIN_TITLE;

	/**
	 * @var string
	 */
	protected $pluginName = 'nofollow';

	/**
	 * @var array
	 */
	protected $parseMethods = [
		'regexHtmlParse_Nofollow'     => LAN_NOFOLLOW_REGEX_PARSER,
		'simpleHtmlDomParse_Nofollow' => LAN_NOFOLLOW_SIMPLE_HTML_DOM_PARSER,
	];

	protected $filterContexts = [
		1 => 'User Posted Only',
		2 => 'User + Admin Posted',
		3 => 'Everything',
	];

	/**
	 * @var array
	 */
	protected $fieldpref = [];

	/**
	 *
	 */
	//protected $preftabs = array('General Settings', 'Manage Exclusions', 'Manage Parse Method' );

	/**
	 * Plugin preferences
	 *
	 * @var array
	 */
	protected $prefs = [

		'active' => [
			'title' => LAN_NOFOLLOW_ACTIVATE,
			'tab'   => 0,
			'type'  => 'boolean',
			'data'  => 'int',
			'help'  => LAN_NOFOLLOW_HINT_ACTIVATE,
		],

		'filter_context' => [
			'title' => 'What type of content should NoFollow filter be applied to:',
			'tab'   => 0,
			'type'  => 'dropdown',
			'size'  => 'xxlarge',
			'data'  => 'int',
			'help'  => 'In what context NoFollow parse filter is called for.',
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

		'use_global_path' => [
			'title' => 'Use global path for simple dom parser lib',
			'tab'   => 0,
			'type'  => 'boolean',
			'data'  => 'int',
			'help'  => 'Use global path for lib'
		]
	];


	public function init()
	{
		$this->prefs['parse_method']['writeParms'] = $this->parseMethods;
		$this->prefs['filter_context']['writeParms'] = $this->filterContexts;
		$this->simpleDomParseLibWarning();

	}


	// ------- Customize Create --------


	private function simpleDomParseLibWarning()
	{
		$parseMethod = e107::pref('nofollow', 'parse_method');
		if ($parseMethod === 'simpleHtmlDomParse_Nofollow') {
			e107::getMessage()
				->addWarning('You need to install Simple DOM Parse Library to use ' . LAN_NOFOLLOW_SIMPLE_HTML_DOM_PARSER);
		}
	}


	public function beforeCreate($new_data, $old_data)
	{
		return $new_data;
	}


	public function afterCreate($new_data, $old_data, $id)
	{
		// do something
	}


	// ------- Customize Update --------


	public function onCreateError($new_data, $old_data)
	{
		// do something
	}


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


	public function renderHelp()
	{
		$caption = 'Project Info';
		$text = '<div style="text-align: center">
					<img src="images/nofollow.svg" alt="Nofollow" width="128" height="128">
				</div>';
		$text .= '<br><p>Project link on github: http://github.com/arunshekher/nofollow</p>';
		$text .= '<p>Author: Arun S. Sekher</p>';

		return ['caption' => $caption, 'text' => $text];

	}


	public function helpPage()
	{
		$ns = e107::getRender();
		$text = "Hello World!";
		$ns->tablerender("Greeting", $text);

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
