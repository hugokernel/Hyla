<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">

<head>

    <title>{TITLE}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="robots" content="noindex,nofollow" />

    {HEADER}

    {STYLESHEET_PLUGIN}

    {STYLESHEET}


		<link rel="stylesheet" href="{DIR_TEMPLATE}/css/jquery.jgrowl.css" type="text/css"/>
		<link rel="stylesheet" href="{DIR_TEMPLATE}/css/jquery.treeview.css" type="text/css"/>

    <link rel="icon" type="image/png" href="{DIR_ROOT}img/icon.png" />
    <link rel="shortcut icon" href="{DIR_ROOT}img/icon.ico"/>

    <script language="javascript" type="text/javascript" src="{DIR_TEMPLATE}/js/jquery.js"></script>
    <script language="javascript" type="text/javascript" src="{DIR_TEMPLATE}/js/jquery.hyla.js"></script>
    <script language="javascript" type="text/javascript" src="{DIR_TEMPLATE}/js/jquery.jgrowl.js"></script>
    <script language="javascript" type="text/javascript" src="{DIR_TEMPLATE}/js/jquery.tablesorter.js"></script>
    <script language="javascript" type="text/javascript" src="{DIR_TEMPLATE}/js/jquery.autogrow.js"></script>
    <script language="javascript" type="text/javascript" src="{DIR_TEMPLATE}/js/jquery.jeditable.js"></script>
    <script language="javascript" type="text/javascript" src="{DIR_TEMPLATE}/js/jquery.jeditable.autogrow.js"></script>
    <script language="javascript" type="text/javascript" src="{DIR_TEMPLATE}/js/jquery.simplemodal.js"></script>
    <script language="javascript" type="text/javascript" src="{DIR_TEMPLATE}/js/jquery.treeview.js"></script>
    <script language="javascript" type="text/javascript" src="{DIR_TEMPLATE}/js/jquery-ui.js"></script>
    <script language="javascript" type="text/javascript" src="{DIR_TEMPLATE}/js/styleswitcher.js"></script>
    <script language="javascript" type="text/javascript" src="{DIR_TEMPLATE}/js/lib.js"></script>

<script language="javascript" type="text/javascript">
//<!--
var hyla_const = new Object;
var hyla_const = {
    'OBJECT'        :   '{OBJECT}',
    'DIR_ROOT'      :   '{DIR_ROOT}',
    'DIR_TEMPLATE'  :   '{DIR_TEMPLATE}',
    'Loading'       :   '{LANG:Loading}',
    'Saving...'     :   '{LANG:Saving...}',
    'Ok'            :   '{LANG:Ok}',
    'Cancel'        :   '{LANG:Cancel}',
    'Error !'       :   '{LANG:Error !}',
    'Click to rename...'                :   '{LANG:Click to rename...}',
    'Click to modify icon...'           :   '{LANG:Click to modify icon...}',
    'Click to modify description ...'   :   '{LANG:Click to modify description...}'
};
function _(key) { return hyla_const[key]; }

//-->
</script>

    <script language="javascript" type="text/javascript" src="{DIR_TEMPLATE}/js/ws.js"></script>

<style type="text/css">
.droppable-hover {
background: red !important;
font-size: 120%;
}


#modalOverlay {
  background-color:#000;
  cursor:wait;
}

#modalContainer {

/*  height:200px;*/
  width:600px;

  text-align: center;

  left:50%;
  top:15%;


  margin-left:-300px;

  /*// half the width, to center*/
/*  padding: 20px;*/
  background-color:#444;
  border:3px solid #000;
}

#modalContainer a.modalCloseImg {
  background:url(/trunk/tpl/crystal/img/x.png) no-repeat;

  width:25px;
  height:29px;
  display:inline;
  z-index:3200;
  position:absolute;
  top:-14px;
  right:-18px;
  cursor:pointer;
}


</style>

</head>

<body style="margin: 10px;">

    {CONTENT}

</body>

</html>
