#!/usr/bin/python
#---------------------------------------------------------------------------------------------------
# Show all TA assignments in the database for a given Person (email).
#---------------------------------------------------------------------------------------------------
import sys,re,os
import MySQLdb
import Database

debug = False

usage  = " usage:  showTaSummary.py   <email>\n\n"
usage += "           email            generate emails and print email linux commands\n"
usage += "                            ex . 'johndoe@mit.edu'\n\n"

if len(sys.argv) < 1:
    print "\n ERROR - need to specify the email address.\n"
    print usage
    sys.exit(0)
    
email = sys.argv[1]

# Open database connection
db = Database.DatabaseHandle()
# Prepare a cursor object using cursor() method
cursor = db.getCursor()

# Make a new objects of all courses
courses = Database.Container()

# Prepare SQL query to select all courses from the Courses table
sql = "SELECT * FROM Courses"

try:
    # Execute the SQL command
    cursor.execute(sql)
    # Fetch all the results
    results = cursor.fetchall()
    for row in results:
        number     = row[0]
        name       = row[1]
        version    = row[2]
        # Now print fetched result
        if debug:
            print " found Course with ('%s','%s',%d);"%(number,name,version)

        # create a new course and add it to our courses object
        course = Database.Course(number,name,version)
        courses.addElement(number,course);

except:
    print " ERROR - unable to fetch data from Courses table."
    # disconnect from server
    db.disco()
    sys.exit()

# Make a new objects of faculties
faculties = Database.Container()

# Prepare SQL query to select all TAs from the Faculties table
sql = "SELECT * FROM Faculties"

try:
    # Execute the SQL command
    cursor.execute(sql)
    # Fetch all the results
    results = cursor.fetchall()
    for row in results:
        firstName  = row[0]
        lastName   = row[1]
        eMail      = row[2]
        position   = row[3]
        status     = row[4]
        # Now print fetched result
        if debug:
            print " found Faculty with ('%s','%s','%s','%s','%s');"% \
                  (firstName,lastName,eMail,position,status)

        # create a new faculty and add it to our faculties object
        faculty = Database.Faculty(firstName,lastName,eMail,position,status)
        faculties.addElement(eMail,faculty);

except:
    print " ERROR - unable to fetch data from Faculties table."
    # disconnect from server
    db.disco()
    sys.exit()

# Make a new objects of students
students = Database.Container()

# Prepare SQL query to select all TAs from the Students table
sql = "SELECT * FROM Students"

try:
    # Execute the SQL command
    cursor.execute(sql)
    # Fetch all the results
    results = cursor.fetchall()
    for row in results:
        firstName  = row[0]
        lastName   = row[1]
        eMail      = row[2]
        advisor    = row[3]
        supervisor = row[4]
        year       = row[5]
        division   = row[6]
        research   = row[7]
        # Now print fetched result
        if debug:
            print " found Student with ('%s','%s','%s','%s','%s',%d,'%s','%s');"% \
                  (firstName,lastName,eMail,advisor,supervisor,year,division,research)

        # create a new student and add it to our students object
        student = Database.Student(firstName,lastName,eMail,advisor,supervisor,year,division,research)
        students.addElement(eMail,student);

except:
    print " ERROR - unable to fetch data from Students table."
    # disconnect from server
    db.disco()
    sys.exit()

# Prepare SQL to accumulate all Assignment tables.
sql = "show tables"

if debug:
    print "\n == Collecting Assignment tables =="
tables = [ ]
try:
    cursor.execute(sql)
    for (tableName,) in cursor:
        if tableName[0:11] == 'Assignments':
            if debug:
                print "   -> " + tableName
            tables.append(tableName)
except:
    print " ERROR - unable to find tables."


print '\n == Student Details =='
student = students.retrieveElement(email)
student.show()
        
# Prepare SQL query to select a record from the database.
sql = ''
for table in tables:
    if sql == "":
        sql = "select * from " + table + " where Person = '" + email + "'"
    else:
        sql += " union select * from " + table + " where Person = '" + email + "'"

#assignments = { }

print '\n == Finding all Assignments =='

try:
    # Execute the SQL command
    cursor.execute(sql)
    # Fetch results
    results = cursor.fetchall()
    for row in results:
        task    = row[0]
        email   = row[1]

        print ' Task: ' + task

except:
    print " ERROR - unable to generate complete assignment summary."

sys.exit(0)
