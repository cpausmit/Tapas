<?php
include("access/checkAccessRights.php");

// Standard Menu
print '<!DOCTYPE HTML>'."\n";
print '<html>'."\n";
print '  <head>'."\n";
print '    <title>TAPAS</title>'."\n";
print '    <meta http-equiv="content-type" content="text/html; charset=utf-8" />'."\n";
print '    <meta name="description" content="" />'."\n";
print '    <meta name="keywords" content="" />'."\n";
print '    <link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600"'.
      ' rel="stylesheet" type="text/css" />'."\n";
// Javascript page layout
print '    <script src="/jslib/jquery-1.11.1.min.js"></script>'."\n";
print '    <script src="/jslib/skel.min.js"></script>'."\n";
print '    <script src="/jslib/skel-panels.min.js"></script>'."\n";
print '    <script src="/js/init.js"></script>'."\n";
print '    <script src="/js/main.js"></script>'."\n";
// Plotting tools
print '    <script src="/jsplots/plotly-latest.min.js"></script>'."\n";
print '    <script src="/jsplots/initPlots.js"></script>'."\n";
print '    <script src="/jsplots/plots.js"></script>'."\n";
print '    <noscript>'."\n";
print '      <link rel="stylesheet" href="/css/skel-noscript.css" />'."\n";
print '      <link rel="stylesheet" href="/css/style.css" />'."\n";
print '      <link rel="stylesheet" href="/css/style-wide.css" />'."\n";
print '    </noscript>'."\n";
print '    <check if="isset({{@css_bundle}})"><link href="{{@css_bundle}}"'.
      ' rel="stylesheet" media="screen"></check>'."\n";
print '  </head>'."\n";
print '  <body>'."\n";

// Check access rights - basics
if (forbidden()) {
  exitAccessError();
}

print '    <div id="header" class="skel-panels-fixed">'."\n";
print '      <div class="top">'."\n";
print '        <div id="logo" >'."\n";
print '	         <span class="image tapas"><img class="rounded" src="/img/tapas.jpg" alt="" /></span>'."\n";
print '          <h1 id="title"><a href="/">TAPAS</a></h1>'."\n";
print '	         <hr>'."\n";
print '          <span class="byline">Teaching Assistants</span>'."\n";
print '          <span class="byline">Physics department</span>'."\n";
print '          <span class="byline">Assignment System</span>'."\n";
print '          <span class="byline">MIT</span>'."\n";
print '	         <hr>'."\n";
print '        </div>'."\n";
print '        <nav id="nav">'."\n";
print '          <ul>'."\n";
print '            <li><a href="/you"       class="{{isset(@selected) && @selected == \'you\'       ? \'active\' : null}}"><span class="fa fa-user">About You</span></a></li>'."\n";
print '            <li><a href="/"          class="{{isset(@selected) && @selected == \'home\'      ? \'active\' : null}}"><span class="fa fa-home">Home</span></a></li>'."\n";
if (isAdmin()   || isMaster())
  print '          <li><a href="/admin"     class="{{isset(@selected) && @selected == \'admin\'     ? \'active\' : null}}"><span class="fa fa-wrench">Admins</span></a></li>'."\n";
if (isTa()      || isMaster())
  print '          <li><a href="/ta"        class="{{isset(@selected) && @selected == \'ta\'        ? \'active\' : null}}"><span class="fa fa-pencil">TAs</span></a></li>'."\n";
if (isTeacher() || isMaster())
  print '          <li><a href="/teacher"   class="{{isset(@selected) && @selected == \'teacher\'   ? \'active\' : null}}"><span class="fa fa-pencil">Teachers</span></a></li>'."\n";
print '            <li><a href="/questions" class="{{isset(@selected) && @selected == \'questions\' ? \'active\' : null}}"><span class="fa fa-question">Questions</span></a></li>'."\n";
print '            <li><a href="/contact"   class="{{isset(@selected) && @selected == \'contact\'   ? \'active\' : null}}"><span class="fa fa-phone">Contact Me</span></a></li>'."\n";
print '          </ul>'."\n";
print '        </nav>'."\n";
print '      </div> <!-- top -->'."\n";
print '    </div> <!-- header -->'."\n";
print '    <div id="main">'."\n";

?>
