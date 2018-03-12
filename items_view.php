<?php
// This script and data application were generated by AppGini 5.61
// Download AppGini for free from https://bigprof.com/appgini/download/

	$currDir=dirname(__FILE__);
	include("$currDir/defaultLang.php");
	include("$currDir/language.php");
	include("$currDir/lib.php");
	@include("$currDir/hooks/items.php");
	include("$currDir/items_dml.php");

	// mm: can the current member access this page?
	$perm=getTablePermissions('items');
	if(!$perm[0]){
		echo error_message($Translation['tableAccessDenied'], false);
		echo '<script>setTimeout("window.location=\'index.php?signOut=1\'", 2000);</script>';
		exit;
	}

	$x = new DataList;
	$x->TableName = "items";

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV = array(   
		"`items`.`id`" => "id",
		"`items`.`item`" => "item",
		"`items`.`code`" => "code",
		"`items`.`balance`" => "balance",
		"IF(    CHAR_LENGTH(`categories1`.`category`), CONCAT_WS('',   `categories1`.`category`), '') /* Category */" => "category"
	);
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = array(   
		1 => '`items`.`id`',
		2 => 2,
		3 => 3,
		4 => '`items`.`balance`',
		5 => '`categories1`.`category`'
	);

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV = array(   
		"`items`.`id`" => "id",
		"`items`.`item`" => "item",
		"`items`.`code`" => "code",
		"`items`.`balance`" => "balance",
		"IF(    CHAR_LENGTH(`categories1`.`category`), CONCAT_WS('',   `categories1`.`category`), '') /* Category */" => "category"
	);
	// Fields that can be filtered
	$x->QueryFieldsFilters = array(   
		"`items`.`id`" => "ID",
		"`items`.`item`" => "Item",
		"`items`.`code`" => "Code",
		"`items`.`balance`" => "Balance",
		"IF(    CHAR_LENGTH(`categories1`.`category`), CONCAT_WS('',   `categories1`.`category`), '') /* Category */" => "Category"
	);

	// Fields that can be quick searched
	$x->QueryFieldsQS = array(   
		"`items`.`id`" => "id",
		"`items`.`item`" => "item",
		"`items`.`code`" => "code",
		"`items`.`balance`" => "balance",
		"IF(    CHAR_LENGTH(`categories1`.`category`), CONCAT_WS('',   `categories1`.`category`), '') /* Category */" => "category"
	);

	// Lookup fields that can be used as filterers
	$x->filterers = array(  'category' => 'Category');

	$x->QueryFrom = "`items` LEFT JOIN `categories` as categories1 ON `categories1`.`id`=`items`.`category` ";
	$x->QueryWhere = '';
	$x->QueryOrder = '';

	$x->AllowSelection = 1;
	$x->HideTableView = ($perm[2]==0 ? 1 : 0);
	$x->AllowDelete = $perm[4];
	$x->AllowMassDelete = false;
	$x->AllowInsert = $perm[1];
	$x->AllowUpdate = $perm[3];
	$x->SeparateDV = 1;
	$x->AllowDeleteOfParents = 0;
	$x->AllowFilters = 1;
	$x->AllowSavingFilters = 1;
	$x->AllowSorting = 1;
	$x->AllowNavigation = 1;
	$x->AllowPrinting = 1;
	$x->AllowCSV = 1;
	$x->RecordsPerPage = 10;
	$x->QuickSearch = 1;
	$x->QuickSearchText = $Translation["quick search"];
	$x->ScriptFileName = "items_view.php";
	$x->RedirectAfterInsert = "items_view.php?SelectedID=#ID#";
	$x->TableTitle = "Items";
	$x->TableIcon = "resources/table_icons/injection.png";
	$x->PrimaryKey = "`items`.`id`";

	$x->ColWidth   = array(  150, 150, 150, 150);
	$x->ColCaption = array("Item", "Code", "Balance", "Category");
	$x->ColFieldName = array('item', 'code', 'balance', 'category');
	$x->ColNumber  = array(2, 3, 4, 5);

	// template paths below are based on the app main directory
	$x->Template = 'templates/items_templateTV.html';
	$x->SelectedTemplate = 'templates/items_templateTVS.html';
	$x->TemplateDV = 'templates/items_templateDV.html';
	$x->TemplateDVP = 'templates/items_templateDVP.html';

	$x->ShowTableHeader = 1;
	$x->ShowRecordSlots = 0;
	$x->TVClasses = "";
	$x->DVClasses = "";
	$x->HighlightColor = '#FFF0C2';

	// mm: build the query based on current member's permissions
	$DisplayRecords = $_REQUEST['DisplayRecords'];
	if(!in_array($DisplayRecords, array('user', 'group'))){ $DisplayRecords = 'all'; }
	if($perm[2]==1 || ($perm[2]>1 && $DisplayRecords=='user' && !$_REQUEST['NoFilter_x'])){ // view owner only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `items`.`id`=membership_userrecords.pkValue and membership_userrecords.tableName='items' and lcase(membership_userrecords.memberID)='".getLoggedMemberID()."'";
	}elseif($perm[2]==2 || ($perm[2]>2 && $DisplayRecords=='group' && !$_REQUEST['NoFilter_x'])){ // view group only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `items`.`id`=membership_userrecords.pkValue and membership_userrecords.tableName='items' and membership_userrecords.groupID='".getLoggedGroupID()."'";
	}elseif($perm[2]==3){ // view all
		// no further action
	}elseif($perm[2]==0){ // view none
		$x->QueryFields = array("Not enough permissions" => "NEP");
		$x->QueryFrom = '`items`';
		$x->QueryWhere = '';
		$x->DefaultSortField = '';
	}
	// hook: items_init
	$render=TRUE;
	if(function_exists('items_init')){
		$args=array();
		$render=items_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// hook: items_header
	$headerCode='';
	if(function_exists('items_header')){
		$args=array();
		$headerCode=items_header($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$headerCode){
		include_once("$currDir/header.php"); 
	}else{
		ob_start(); include_once("$currDir/header.php"); $dHeader=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%HEADER%%>', $dHeader, $headerCode);
	}

	echo $x->HTML;
	// hook: items_footer
	$footerCode='';
	if(function_exists('items_footer')){
		$args=array();
		$footerCode=items_footer($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$footerCode){
		include_once("$currDir/footer.php"); 
	}else{
		ob_start(); include_once("$currDir/footer.php"); $dFooter=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%FOOTER%%>', $dFooter, $footerCode);
	}
?>