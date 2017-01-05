#!/usr/bin/env perl
use strict;
use DBI;
#---------------------------------------------------------------------------------------------------
# Script to read the standardized input from the physics department to generate the required slots.
#
# Input file is:                                                     ./tmp/${SEMESTERID}Courses.csv
#
#                                                        Written: December 04, 2013 (Christoph Paus)
#---------------------------------------------------------------------------------------------------

# ?? needed ?? #use lib "$ENV{'TAPAS_TOOLS'}/perl";

my ($host,$driver,$database,$dsn,$userid,$password,$dbh);
my ($query,$sqlQuery);
my ($course,$courseTeacher,$courseEmail);
my ($Lec,$Rec);
my ($fullTimeTasR,$fullTimeTasU);
my ($halfTimeTasR,$halfTimeTasU);
my ($partTimeTasR,$partTimeTasU);
my ($totalCourseTas);
my ($totalTasF,$totalTasP);
my ($totalLec,$totalRec);
my (@f,@g,@h);
my ($i,$line,$cmd,$rc);

sub findTag {
  my $file = shift(@_);
  my $group = shift(@_);
  my $tag = shift(@_);

  my $active = 0;
  my $value = "";
  my $testTag = "";
  my $testValue = "";
  
  
  open(INPUT,"<$file");
  while($line = <INPUT>) {
    chop($line);
    if ("$line" eq "[$group]") {
	$active = 1;
    }
    if ($active == 1) {
      @f = split('=',$line);
      $testTag = $f[0];
      $testTag =~ s/ //g;
      $testValue = $f[1];
      $testValue =~ s/ //g;
      if ($testTag eq $tag) {
        $value = $testValue;
        last;
      }
    }
  } 
  
  return $value;
}
  
sub addToDb {
  my $db = shift(@_);
  my $dbh = shift(@_);
  my $query = shift(@_);

  printf " MYSQL> $query\n";

  if ("$db" ne "") {
      my $sqlQuery  = $dbh->prepare($query)
          or die "Cannot prepare \"$query\": $dbh->errstr\n";
      my $rv = $sqlQuery->execute
          or printf " insert failed. moving on.\n";
      my $rc = $sqlQuery->finish;

      return $rc;
  }
}

#---------------------------------------------------------------------------------------------------
if ($#ARGV < 0) {
  printf "\n usage:  generateSlots.pl  <semesterId>  [ <db> ]\n\n";
  printf "           semesterId      the semester identification (ex. F14, I14, S14)\n";
  printf "           db              activate db actions [ def='', yes ]\n\n";
  exit;
}
my $SEMESTERID = $ARGV[0];
my $DB         = $ARGV[1];


if ("$DB" ne "") {
	
  # Connect to database
  $host = findTag("/etc/my.cnf","mysql-teaching","host");
  $userid = findTag("/etc/my.cnf","mysql-teaching","user");
  $password = findTag("/etc/my.cnf","mysql-teaching","password");
  $driver   = "mysql";
  $database = "Teaching";
  $dsn      = "DBI:$driver:database=$database:$host";
  $dbh      = DBI->connect($dsn, $userid, $password) or die $DBI::errstr;
  
  # Make sure to create the table is it does not exist
  $query    = "create table Assignments${SEMESTERID}(Task char(20), Person char(40));";
  $sqlQuery = $dbh->prepare($query) or die "Can't prepare $query: $dbh->errstr\n";
  $rc       = $sqlQuery->execute    or printf "\n WARNING -- Cannot execute: $sqlQuery->errstr\n\n";
  $rc       = $sqlQuery->finish;
  
  # Make sure the Task is unique (they have to have different names)
  $query    = "alter table Assignments${SEMESTERID} add unique index(Task);";
  $sqlQuery = $dbh->prepare($query) or die "Can't prepare $query: $dbh->errstr\n";
  $rc       = $sqlQuery->execute    or printf "\n WARNING -- Cannot execute: $sqlQuery->errstr\n\n";
  $rc       = $sqlQuery->finish;
}


# Read course list
printf " MYSQL> create table Assignments${SEMESTERID}(Task char(20), Person char(40));\n";

printf " Course --Lecturer(s)--------------------------- Lec Rec TaFR TaFU TaHR TaHU TaPU\n";
printf "=================================================================================\n";

open(INPUT,"<$ENV{'TAPAS_TOOLS_DATA'}/spreadsheets/${SEMESTERID}Courses.csv");
while($line = <INPUT>) {
  chop($line);
  $line =~ s/\"//g;
  $line =~ s/\'//g;
  @f = split(',',$line);

  if ($f[0] =~ m/^8/) {
    $course       = $f[0];
    $courseEmail  = $f[1]; $courseEmail =~ s/\//,/g;

    $Lec          = $f[2];
    $Rec          = $f[3];
    $fullTimeTasR = $f[4];
    $fullTimeTasU = $f[5];
    $halfTimeTasR = $f[6];
    $halfTimeTasU = $f[7];
    $partTimeTasU = $f[8];

    $totalLec       += $Lec;
    $totalRec       += $Rec;
    $totalCourseTas  = 0.5*($halfTimeTasR+$halfTimeTasU)+$fullTimeTasR+$fullTimeTasU;
    $totalTasF      += $totalCourseTas;
    $totalTasP      += $partTimeTasU;

    # print course slot summary
    printf " %-6s, %-40s, %3d, %3d, %4d, %4d, %4d, %4d, %4d\n",$course,$courseEmail,$Lec,$Rec,
      $fullTimeTasR,$fullTimeTasU,$halfTimeTasR,$halfTimeTasU,$partTimeTasU;
  
    my $base = " insert into Assignments${SEMESTERID} values ('$SEMESTERID-$course";
    
    # print all slots per course
    for (my $i=0; $i < $Lec; $i++) {
      my $lecEmail = "EMPTY\@mit.edu";
      if ("$i" eq "0") {
        @g = split(',',$courseEmail);
        $courseEmail = @g[0];
        $lecEmail = $courseEmail;
        my $n = $i+1;
        my $query = "${base}-Lec-$n','$lecEmail');";

        $rc = addToDb($DB,$dbh,$query);
        ## >> printf " MYSQL> $query\n";
        ## >> if ("$DB" ne "") {
        ## >>   my $sqlQuery  = $dbh->prepare($query)
        ## >>     or die "Cannot prepare \"$query\": $dbh->errstr\n";
        ## >>   #my $rv = $sqlQuery->execute
        ## >>   #  or die "cannot execute query: $sqlQuery->errstr";
        ## >> 
        ## >>   my $rv = $sqlQuery->execute
        ## >>     or printf " insert failed. moving on.\n";
        ## >> 
        ## >>   ## #print "<h3>********** My Perl DBI Test ***************</h3>";
        ## >>   ## #print "<p>Here is a list of tables in the MySQL database $db.</p>";
        ## >>   ## #while (@row= $sqlQuery->fetchrow_array()) {
        ## >>   ## #  my $tables = $row[0];
        ## >>   ## #  print "$tables\n<br>";
        ## >>   ## #}
        ## >>   my $rc = $sqlQuery->finish;
        ## >> }
      }
    }
    for (my $i=0; $i < $Rec; $i++) {
      my $counter = sprintf("%1d",$i+1);
      my $query = "${base}-Rec-$counter','EMPTY\@mit.edu');";
      $rc = addToDb($DB,$dbh,$query);
    }
    for (my $i=0; $i < $fullTimeTasR; $i++) {
      my $counter = sprintf("%1d",$i+1);
      my $query = "${base}-TaFR-$counter','EMPTY\@mit.edu');";
      $rc = addToDb($DB,$dbh,$query);
    }
    for (my $i=0; $i < $fullTimeTasU; $i++) {
      my $counter = sprintf("%1d",$i+1);
      my $query = "${base}-TaFU-$counter','EMPTY\@mit.edu');";
      $rc = addToDb($DB,$dbh,$query);
    }
    for (my $i=0; $i < $halfTimeTasR; $i++) {
      my $counter = sprintf("%1d",$i+1);
      my $query = "${base}-TaHR-$counter','EMPTY\@mit.edu');";
      $rc = addToDb($DB,$dbh,$query);
    }
    for (my $i=0; $i < $halfTimeTasU; $i++) {
      my $counter = sprintf("%1d",$i+1);
      my $query = "${base}-TaHU-$counter','EMPTY\@mit.edu');";
      $rc = addToDb($DB,$dbh,$query);
    }
    for (my $i=0; $i < $partTimeTasU; $i++) {
      my $counter = sprintf("%1d",$i+1);
      my $query = "${base}-TaPU-$counter','EMPTY\@mit.edu');";
      $rc = addToDb($DB,$dbh,$query);
    }
    #printf "\n";
  }
}
close(INPUT);
system("rm -f tmp.csv");

printf "==========================================\n";
printf "  Total %3d %3d                %4.1f %4.1f\n",
  $totalLec,$totalRec,$totalTasF,$totalTasP;
printf "==========================================\n";
