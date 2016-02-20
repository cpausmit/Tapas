<?php

ini_set('error_log', 'app/logs/error.log');
$f3 = require('app/lib/fatfree/lib/base.php');

$f3->set('CACHE',        FALSE);
$f3->set('DEBUG',        3);
$f3->set('UI',           'app/views/');
$f3->set('AUTOLOAD',     'app/models/;app/controllers/');
$f3->set('LOGS',         'app/logs/');
$f3->set('TEMP',         'app/tmp/');
$f3->set('CACHE',        'app/tmp/cache/');
$f3->set('service_name', 'mit-tapas.dyndns.org');
$f3->set('JAR.expire',    time() + 3600*24*365);

////////////////////////////////////////////////////////////////////////////////
// Routes
////////////////////////////////////////////////////////////////////////////////

$f3->route('GET /email',                'Main->email');
$f3->route('GET|POST /sendEmail',       'Main->sendEmail');


$f3->route('GET /',                     'Main->index');
$f3->route('GET /questions',            'Main->questions');
$f3->route('GET /contact',              'Main->contact');
$f3->route('GET /you',                  'Main->you');

$f3->route('GET /admin',                'Main->admin');
$f3->route('GET /courses',              'Main->courses');
$f3->route('GET /assignments',          'Main->assignments');
$f3->route('GET /students',             'Main->students');
$f3->route('GET /addStudent',           'Main->addStudent');
$f3->route('GET|POST /recordStudent',   'Main->recordStudent');
$f3->route('GET /showTaSummary',        'Main->showTaSummary');
$f3->route('GET /showTaskSummary',      'Main->showTaskSummary');
$f3->route('GET /showAssignments',      'Main->showAssignments');
$f3->route('GET /updateAssignments',    'Main->updateAssignments');
$f3->route('GET /showActiveAssignments','Main->showActiveAssignments');
$f3->route('GET /showLastAssignments',  'Main->showLastAssignments');
$f3->route('GET /copyLastAssignments',  'Main->copyLastAssignments');
$f3->route('GET /showActiveTas',        'Main->showActiveTas');
$f3->route('GET /findTas',              'Main->findTas');
$f3->route('GET /showPreferences',      'Main->showPreferences');
$f3->route('GET /showAccessLists',      'Main->showAccessLists');
$f3->route('GET /evaluations',          'Main->evaluations');
$f3->route('GET /showEvaluations',      'Main->showEvaluations');
$f3->route('GET /showActiveEvaluations','Main->showActiveEvaluations');
$f3->route('GET /showEvaluators',       'Main->showEvaluators');
$f3->route('GET /addEvaluation',        'Main->addEvaluation');
$f3->route('GET|POST /recordEvaluation','Main->recordEvaluation');

$f3->route('GET /ta',                   'Main->ta');
$f3->route('GET /select',               'Main->select');
$f3->route('GET /show',                 'Main->show');
$f3->route('GET /remove',               'Main->remove');
$f3->route('GET|POST /register',        'Main->register');

$f3->route('GET /teacher',              'Main->teacher');
$f3->route('GET /enter',                'Main->enter');
$f3->route('GET|POST /enterEvaluation', 'Main->enterEvaluation');
$f3->route('GET|POST /record',          'Main->record');
$f3->route('GET /review',               'Main->review');

////////////////////////////////////////////////////////////////////////////////
// Error handler
////////////////////////////////////////////////////////////////////////////////
$f3->set('ONERROR',function($f3) {
    $err_code = $f3->get('ERROR.code');
    error_log("$err_code ERROR: " . $f3->get("URI"));
    $f3->set("css_bundle", "//" . $_SERVER['SERVER_NAME']
	     . "/min/?f=/css/style.css,/css/timeline.css");
  
    if ($err_code == 404) {
      $f3->set("title", "404 Not found");
      $view = new View;
      echo $view->render('404.php');
      return;
    }
    else {
      $f3->set("title", "$err_code Error!");
      $view = new View;
      echo $view->render("error.php");
      return;
    }
  }
  );

$f3->run();

?>
