<?php

class Main extends Controller {

  public function __construct() {
    $this->add_js("/jslib/knockout-3.1.0.js");
    $this->add_js("/js/main.js");
    $this->add_css("/css/timeline.css");
    $f3 = \Base::instance();
    $f3->set("js_bundle", $this->get_min_js_url());
    $f3->set("css_bundle", $this->get_min_css_url());
  }

  public function email($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/email.php');
  }
  public function sendEmail($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/sendEmail.php');
  }

  // Main index page
  public function index($f3) {
    $f3->set('selected', 'home');
    $view = new View;
    echo $view->render('index.php');
  }
  public function questions($f3) {
    $f3->set('selected', 'questions');
    $view = new View;
    echo $view->render('questions.php');
  }
  public function contact($f3) {
    $f3->set('selected', 'contact');
    $view = new View;
    echo $view->render('contact.php');
  }
  public function you($f3) {
    $f3->set('selected', 'you');
    $view = new View;
    echo $view->render('you.php');
  }

  // Admin functions
  public function admin($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin.php');
  }
  public function courses($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/courses.php');
  }
  public function students($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/students.php');
  }
  public function addStudent($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/addStudent.php');
  }
  public function updateStudent($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/updateStudent.php');
  }
  public function recordStudent($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/recordStudent.php');
  }
  public function addActiveTa($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/addActiveTa.php');
  }
  public function updateActiveTas($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/updateActiveTas.php');
  }
  public function showTaSummary($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/showTaSummary.php');
  }
  public function showTaskSummary($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/showTaskSummary.php');
  }
  public function showAssignments($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/showAssignments.php');
  }
  public function updateAssignments($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/updateAssignments.php');
  }
  public function assignments($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/assignments.php');
  }
  public function showActiveAssignments($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/showActiveAssignments.php');
  }
  public function showLastAssignments($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/showLastAssignments.php');
  }
  public function copyLastAssignments($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/copyLastAssignments.php');
  }
  public function showActiveTas($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/showActiveTas.php');
  }
  public function findTas($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/findTas.php');
  }
  public function showPreferences($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/showPreferences.php');
  }
  public function showAccessLists($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/showAccessLists.php');
  }
  public function evaluations($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/evaluations.php');
  }
  public function showEvaluations($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/showEvaluations.php');
  }
  public function showActiveEvaluations($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/showActiveEvaluations.php');
  }
  public function showEvaluators($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/showEvaluators.php');
  }
  public function addEvaluation($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/addEvaluation.php');
  }
  public function recordEvaluation($f3) {
    $f3->set('selected', 'admin');
    $view = new View;
    echo $view->render('admin/recordEvaluation.php');
  }

  // TA functions
  public function ta($f3) {
    $f3->set('selected', 'ta');
    $view = new View;
    echo $view->render('ta.php');
  }
  public function select($f3) {
    $f3->set('selected', 'ta');
    $view = new View;
    echo $view->render('ta/select.php');
  }
  public function show($f3) {
    $f3->set('selected', 'ta');
    $view = new View;
    echo $view->render('ta/show.php');
  }
  public function remove($f3) {
    $f3->set('selected', 'ta');
    $view = new View;
    echo $view->render('ta/remove.php');
  }
  public function register($f3) {
    $f3->set('selected', 'ta');
    $view = new View;
    echo $view->render('ta/register.php');
  }

  // Teachers functions
  public function teacher($f3) {
    $f3->set('selected', 'teacher');
    $view = new View;
    echo $view->render('teacher.php');
  }
  public function enter($f3) {
    $f3->set('selected', 'teacher');
    $view = new View;
    echo $view->render('teacher/enter.php');
  }
  public function enterEvaluation($f3) {
    $f3->set('selected', 'teacher');
    $view = new View;
    echo $view->render('teacher/enterEvaluation.php');
  }
  public function record($f3) {
    $f3->set('selected', 'teacher');
    $view = new View;
    echo $view->render('teacher/record.php');
  }
  public function review($f3) {
    $f3->set('selected', 'teacher');
    $view = new View;
    echo $view->render('teacher/review.php');
  }
}

?>
