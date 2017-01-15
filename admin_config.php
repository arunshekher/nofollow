<?php

// Generated e107 Plugin Admin Area 

require_once('../../class2.php');
if (!getperms('P')) 
{
	e107::redirect('admin');
	exit;
}

// e107::lan('nofollow',true);


class nofollow_adminArea extends e_admin_dispatcher
{

	protected $modes = array(	
	
		'main'	=> array(
			'controller' 	=> 'nofollow_ui',
			'path' 		=> null,
			'ui' 		=> 'nofollow_form_ui',
			'uipath' 	=> null
		),
		

	);	
	
	
	protected $adminMenu = array(
			
		'main/prefs' 		=> array('caption'=> LAN_PREFS, 'perm' => 'P'),	

		// 'main/custom'		=> array('caption'=> 'Custom Page', 'perm' => 'P')
	);

	protected $adminMenuAliases = array(
		'main/edit'	=> 'main/list'				
	);	
	
	protected $menuTitle = 'Nofollow';
}




				
class nofollow_ui extends e_admin_ui
{
			
		protected $pluginTitle		= 'Nofollow';
		protected $pluginName		= 'nofollow';
	//	protected $eventName		= 'nofollow-'; // remove comment to enable event triggers in admin. 		
		protected $table			= '';
		protected $pid				= '';
		protected $perPage			= 10; 
		protected $batchDelete		= true;
	//	protected $batchCopy		= true;		
	//	protected $sortField		= 'somefield_order';
	//	protected $orderStep		= 10;
	//	protected $tabs				= array('Tabl 1','Tab 2'); // Use 'tab'=>0  OR 'tab'=>1 in the $fields below to enable. 
		
	//	protected $listQry      	= "SELECT * FROM `#tableName` WHERE field != '' "; // Example Custom Query. LEFT JOINS allowed. Should be without any Order or Limit.
	
		protected $listOrder		= ' DESC';
	
		protected $fields 		= NULL;		
		
		protected $fieldpref = array();
		

	//	protected $preftabs        = array('General', 'Other' );
		protected $prefs = array(
			'Activate NoFollow?'		=> array('title'=> 'Activate NoFollow?', 'tab'=>0, 'type'=>'boolean', 'data' => 'str', 'help'=>'Turn Nofollow on or off.'),
			'Activate &#39;NoFollow Onpost&#39;?'		=> array('title'=> 'Activate &#39;NoFollow Onpost&#39;?', 'tab'=>0, 'type'=>'boolean', 'data' => 'str', 'help'=>'Activate conversion of anchor tags with rel=&#39;nofollow&#39; while user makes posts.'),
			'Omit Pages: '		=> array('title'=> 'Omit Pages: ', 'tab'=>0, 'type'=>'textarea', 'data' => 'str', 'help'=>'Same format as menu visibility control. One match per line. Specify a partial or'),
			'Omit Domains:'		=> array('title'=> 'Omit Domains:', 'tab'=>0, 'type'=>'textarea', 'data' => 'str', 'help'=>'List of domains which you don&#39;t want to pass through the nofollow filter or the '),
		); 

	
		public function init()
		{
			// Set drop-down values (if any). 
	
		}

		
		// ------- Customize Create --------
		
		public function beforeCreate($new_data,$old_data)
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
		
			
	/*	
		// optional - a custom page.  
		public function customPage()
		{
			$text = 'Hello World!';
			$otherField  = $this->getController()->getFieldVar('other_field_name');
			return $text;
			
		}
	*/
			
}
				


class nofollow_form_ui extends e_admin_form_ui
{

}		
		
		
new nofollow_adminArea();

require_once(e_ADMIN."auth.php");
e107::getAdminUI()->runPage();

require_once(e_ADMIN."footer.php");
exit;

?>