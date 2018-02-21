#!/usr/bin/python
#---------------------------------------------------------------------------------------------------
# Show the TA assignments in the database that have been made for a specific semester.
#
#---------------------------------------------------------------------------------------------------
import sys,re,os
import MySQLdb
import Database

debug = False
check = False
dataDir = os.getenv('TAPAS_TOOLS_DATA','./')
os.chdir(dataDir)

usage  = " usage: showAssignment.py  <semesterId>  <taskType>  [ <printEmail> ]\n\n"
usage += "          semesterId       identification string for a specific semster\n"
usage += "                           ex. F2013 (Fall 2013), I2013 (IAP 2013), S2013 (Spring 2013)\n"
usage += "          taskType         description of the type of assignment\n"
usage += "                           'full' (fulltime, includes 2xhalf), 'part' (10%,20%IAP)\n\n"
usage += "          printEmail       generate emails and print email linux commands\n"
usage += "                           def = '', activate with 'email'\n\n"

if len(sys.argv) < 3:
    print "\n ERROR - need to specify the semester id and the relevant task type.\n"
    print usage
    sys.exit(0)
    
period     = sys.argv[1]
taskType   = sys.argv[2]
printEmail = ''
if len(sys.argv) > 3:
    printEmail = sys.argv[3]

if taskType == "part":
    taskType = "part"
else:
    taskType = "full"

# Open database connection
db = Database.DatabaseHandle()
# Prepare a cursor object using cursor() method
cursor = db.getCursor()

# Make a new objects of all courses
courses = Database.Container()
rc = courses.fillWithCourses(db.handle,debug)
if rc != 0:
    print " ERROR - filling courses."
    # disconnect from server
    db.disco()
    sys.exit()

# Make a new objects of faculties
teachers = Database.Container()
rc = teachers.fillWithTeachers(db.handle)
if rc != 0:
    print " ERROR - filling teachers."
    # disconnect from server
    db.disco()
    sys.exit()

# Make a new objects of students
students = Database.Container()
rc = students.fillWithStudents(db.handle)
if rc != 0:
    print " ERROR - filling students."
    # disconnect from server
    db.disco()
    sys.exit()

# Remember active courses, teachers and students
activeCourses  = Database.Container()
activeTeachers = Database.Container()
activeStudents = Database.Container()

#---------------------------------------------------------------------------------------------------
# Make a complete list of all assignments
#---------------------------------------------------------------------------------------------------
assignments = {}

# Prepare SQL query to select a record from the database.
sql = "select * from Assignments where Term = '" + period + "';"
try:
    # Execute the SQL command
    cursor.execute(sql)
    # Fetch results
    results = cursor.fetchall()
    for row in results:
        term    = row[0]
        task    = row[1]
        email   = row[2]

        # deal with empty assignments first
        if email == None or email == '':
            print ' WARNING - empty assignment for task: ' + task
            continue
        
        if debug:
            print " TASK : %s  EMAIL: %s"%(task,email)

        # decode the course number
        number = task.split('-')[1]

        # flags to test whether person is part of the database
        isTeacher = False
        isStudent = False

        # find corresponding course in our courses list
        try:
            course = courses.retrieveElement(number);
            #print "%-20s"%(number)
            activeCourses.addElement(number,course)
        except:
            print " ERROR - course is not in our master table (%s)."%(number)

            # disconnect from server
            db.disco()
            sys.exit()
            
        # try if it is a techer in our teachers list
        try:
            teacher = teachers.retrieveElement(email);
            activeTeachers.addElement(email,teacher)
            isTeacher = True
            # Add teaching teacher to the course
            if   task.split('-')[2] == 'Lec':          ## and task.split('-')[3] == '1':
                course = activeCourses.retrieveElement(number);
                course.setTeacher(email)
            elif task.split('-')[2] == 'Adm':
                course = activeCourses.retrieveElement(number);
                course.setAdmin(email)
        except:
            #print " Not a teacher (%s)"%(email)
            teacher = 0

        # find the student in our students list
        try:
            student = students.retrieveElement(email)
            activeStudents.addElement(email,student)
            isStudent = True
        except:
            #print " Not a student (%s)"%(email)
            student = 0

        # store assignment
        try:
            tmp = assignments[email]
            assignments[email] = tmp + ',' + task
        except:
            assignments[email] = task

        # did we find the person in the database
        if isStudent or isTeacher:
            if check:
                print " Found the person."
        else:
            print " ERROR -- Did not find the person: " + email + "  (task: " + task + ")"
            db.disco()
            sys.exit(0)


except:
    print " ERROR - unable to complete ACTIVE elements loop."

# disconnect from server
db.disco()

#---------------------------------------------------------------------------------------------------
# Prepare the assignment emails
#---------------------------------------------------------------------------------------------------

# prepare unique list of students that get assignments (there could be several)

departmentEmail = os.getenv('TAPAS_TOOLS_DEPEML','XX-ERROR-XX@mit.edu')
preAssignment   = [ ]
preEmails       = ''
teachersEmails  = ''

for key, assignment in assignments.iteritems():
    if debug:
        print "\n\n# NEXT # Key: " + key + ' --> ' + assignment

#    try:
    if True:
        try:
            student = activeStudents.retrieveElement(key)
        except:
            if debug:
                print ' Not a student (%s) ... moving on to next entry.'%(key)
            # make sure to add up all the teachers emails
            if re.search('-Lec-',assignment):
                if teachersEmails == '':
                    teachersEmails = key
                else:
                    teachersEmails += "," + key
            continue
            
        if debug:
            print "\n Assignment for %s %s (%s)"%(student.firstName,student.lastName,key)

        if preEmails == '':
            preEmails = key
        else:
            preEmails += "," + key

        # filename for the email
        filename = period + "_" + taskType + "_" + student.firstName + "_" + student.lastName

        # reset the assignment string
        assignString = ""

        # construct the '*visors' email
        additionalCc = student.advisorEmail
        if student.supervisorEmail != "?":
            additionalCc += ',' + student.supervisorEmail

        for task in assignment.split(','):
            if assignString != "":
                assignString += "\n"
                
            term   = task.split('-')[0]
            number = task.split('-')[1]
            type   = task.split('-')[2]
            
            filename += "_" + number

            if debug:
                print " FileName: %s"%(filename)

            if ((taskType == 'full' and (re.search('TaF',type) or re.search('TaH',type))) or \
                (taskType == 'part' and  re.search('TaP',type)) ):

                if debug:
                    print " Number: %s"%(number)
                try:
                    course  = activeCourses.retrieveElement(number)
                except:
                    print '\n ERROR - Not a registered course (%s). EXIT!\n'%(number)
                    sys.exit(1)

                if debug:
                    print " Admin: %s"%(course.admin)
                try:
                    teacher = activeTeachers.retrieveElement(course.admin)
                except:
                    print '\n ERROR - Not a registered teacher (%s). EXIT!\n'%(course.admin)
                    sys.exit(1)

                if debug:
                    print " Course: " + number + "  Teacher: " + course.admin

                if type[3] == "U":
                    tmp = "%-14s, %-14s TA (U) - %-6s  %-40s %s %s (%s)"%\
                          (student.lastName,student.firstName,course.number,course.name, \
                           teacher.firstName,teacher.lastName,teacher.eMail)
                    preAssignment.append(tmp)
                    assignString += " Utility TA in course  " + course.number + \
                                    " (" + course.name + ") administered by " + \
                                    teacher.firstName + " " + teacher.lastName + \
                                    " (" + teacher.eMail + ")"
                elif type[3] == "R" or type[3] == "L":
                    tmp = "%-14s, %-14s TA (R) - %-6s  %-40s %s %s (%s)"%\
                          (student.lastName,student.firstName,course.number,course.name, \
                           teacher.firstName,teacher.lastName,teacher.eMail)
                    preAssignment.append(tmp)

                    assignString += " Recitation TA in course  " + course.number + \
                                    " (" + course.name + ") administered by " + \
                                    teacher.firstName + " " + teacher.lastName + \
                                    " (" + teacher.eMail + ")"
                    
                else:
                    assignString += " ERROR - Unknown TA type found: " + type[3]

                # addup the additional teacher to be copied
                ##additionalCc += "," + teacher.eMail
                if course.admin != 'EMPTY@mit.edu':
                    additionalCc += "," + course.admin

        if debug:
            print assignString

        if assignString == "":
            if debug:
                print "No type match %s %s %s %s\n"% \
                      (student.firstName,student.lastName,key,assignment)
            continue

        filename += ".eml"

        print "\n" + term + " " + student.firstName + " " + student.lastName + "\n" \
              + assignString

        if printEmail == "email":
            cmd = "generateEmail.sh '" + term + "' \"" + student.firstName + " " \
                  + student.lastName + "\" '" + assignString +"' \"" + filename + "\" " + taskType
            if debug:
                print " CMD: " + cmd
            os.system(cmd)
            print " mail -S replyto=paus@mit.edu " + "-c " + additionalCc + "," + departmentEmail \
                + " -s \'TA Assignment " + term + " (" + student.firstName + " " \
                + student.lastName + ")\' " + student.eMail + " < " + dataDir + "/spool/" + filename

#    except:
#        student = 0       
#
        

#---------------------------------------------------------------------------------------------------
# Print out (pre-)assignment Summary
#---------------------------------------------------------------------------------------------------

print "\nEMAIL TEXT for pre-assignments"
preAssignment.sort();
for task in preAssignment:
    print task

print "\nEMAIL ADDRESS for pre-assignments"
print preEmails

print "\nEMAIL ADDRESS for feedback"
print teachersEmails

sys.exit(0)
