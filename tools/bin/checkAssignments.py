#!/usr/bin/python
#---------------------------------------------------------------------------------------------------
# Check validity of TA assignments in the database that have been made for a specific semester.
#
#---------------------------------------------------------------------------------------------------
import sys,re,os
import MySQLdb
import Database

print " UNTESTED -- CAREFUL NEW SUMMARY TABLES -- Assignments etc."
sys.exit(0)

debug = False

usage  = " usage:  checkAssignment.py  <semesterId>\n\n"
usage += "           semesterId        identification string for a specific semster\n"
usage += "                             ex. F13 (Fall 2013), I13 (IAP 2013), S13 (Spring 2013)\n\n"

if len(sys.argv) < 1:
    print "\n ERROR - need to specify the semester id and the relevant task type.\n"
    print usage
    sys.exit(0)
    
semesterId = sys.argv[1]

# Open database connection
db = Database.DatabaseHandle()
# Prepare a cursor object using cursor() method
cursor = db.getCursor()

# Make a new objects of all courses
courses = Database.Container()
activeCourses = Database.Container()
rc = courses.fillWithCourses(db.handle)
if rc != 0:
    print " ERROR - filling courses."
    # disconnect from server
    db.disco()
    sys.exit()
#courses.show()
    
# Make a new objects of teachers
teachers = Database.Container()
activeTeachers = Database.Container()
rc = teachers.fillWithTeachers(db.handle)
if rc != 0:
    print " ERROR - filling teachers."
    # disconnect from server
    db.disco()
    sys.exit()

# Make a new objects of students
students = Database.Container()
activeStudents = Database.Container()
rc = students.fillWithStudents(db.handle)
if rc != 0:
    print " ERROR - filling students."
    # disconnect from server
    db.disco()
    sys.exit()


# Prepare SQL query to select a record from the database.
sql = "SELECT * FROM Assignments where Term = '" + semesterId + "';"

tasks = []
assignments = {}
try:
    # Execute the SQL command
    cursor.execute(sql)
    # Fetch results
    results = cursor.fetchall()
    for row in results:
        term    = row[0]
        task    = row[1]
        email   = row[2]

        ## make sure to exclude useless lines
        #if email == "EMPTY@mit.edu":
        #    continue

        # decode the course number
        number = task.split('-')[1]
        # find corresponding course in our courses list
        try:
            #print ">%s<"%(number)
            course = courses.retrieveElement(number);
            #course.show()
            activeCourses.addElement(number,course)
        except:
            print " ERROR - course is not in our master table (%s)."%(number)
            # disconnect from server
            db.disco()
            sys.exit()
            
        # find the teacher in our teachers list
        try:
            teacher = teachers.retrieveElement(email);
            activeTeachers.addElement(email,teacher)
            # Add teaching teacher to the course
            if task.split('-')[2] == 'Lec' and task.split('-')[3] == '1':
                course = activeCourses.retrieveElement(number);
                course.setTeacher(email)
        except:
            #print " Not a teacher (%s)"%(email)
            teacher = 0
        # find the student in our students list
        try:
            student = students.retrieveElement(email)
            activeStudents.addElement(email,student)
        except:
            #print " Not a student (%s)"%(email)
            student = 0

        # store assignment pair
        if task in tasks:
            pass
            print ' Already found: ' + task
        else:
            tasks.append(task)
            print "insert into Assignments (Term,Task,Person) values" + \
                " ('" + term + "','" + task + "','" + email + "');"
            
        try:
            tmp = assignments[email]
            assignments[email] = tmp + ',' + task
            #print ' Double Assignment: ' + assignments[email]
        except:
            assignments[email] = task

except:
    print " ERROR - unable to complete ACTIVE elements loop."

# disconnect from server
db.disco()

sys.exit(0)
