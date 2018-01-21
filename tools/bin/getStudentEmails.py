#!/usr/bin/python
#---------------------------------------------------------------------------------------------------
# A raw first name, last name list will be analyzed to match to an existing student in the database
# to print the corresponding email.
#---------------------------------------------------------------------------------------------------
import sys,os
import MySQLdb
import Database

debug = False

usage  = " usage:  getStudentsEmail.py  <semesterId>\n\n"
usage += "           semesterId       identification string for a specific semster\n"
usage += "                            ex. F13 (Fall 2013), I13 (IAP 2013), S13 (Spring 2013)\n\n"

if len(sys.argv) < 1:
    print "\n ERROR - need to specify the semester id.\n"
    print usage
    sys.exit(0)

semesterId = sys.argv[1]

# Open database connection
db = Database.DatabaseHandle()
# Prepare a cursor object using cursor() method
cursor = db.getCursor()

# Make a new objects of students
students = Database.Container()

# Prepare SQL query to select all TAs from the Students table (our database)
sql = "select * from Students"

try:
    # Execute the SQL command
    cursor.execute(sql)
    # Fetch all the results
    results = cursor.fetchall()
    nStudents = 0
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
        nStudents += 1

except:
    print " ERROR - unable to fetch data from Students table."
    # disconnect from server
    db.disco()
    sys.exit()

print "\n Read %3d students records from the database.\n"%(nStudents) 

# now lets see whether we can find the email for the students in our list

# extract students names from the given list
nMatches = 0
firstName = ''
lastName = ''
dataFile = "%s/spreadsheets/%sRawTas.csv"%(os.getenv('TAPAS_TOOLS_DATA','./'),semesterId)
for line in os.popen('cat ' + dataFile + ' | tr -d \' \'').readlines(): # run command
    line = line[:-1] # stripping '\n'
    f = line.split(',')
    firstName = f[0]
    lastName  = f[1]

    print "\n Test -- First: " + firstName + "  Last: " + lastName

    matched = 0
    for email, student in students.getHash().iteritems():
        #print " Compare:  >%s<  >%s<"%(lastName,student.lastName)
        if student.lastName == lastName and student.firstName == firstName:
            nMatches += 1
            print " Match %2d: %s %s --> %s"%(nMatches,firstName,lastName,email)
            matched = 1
            break
        elif student.lastName == lastName:
            print " Match?   First:  %s  <-->  %s    Last:  %s  <-->  %s"%\
                  (firstName,student.firstName,lastName,student.lastName)
            print " maybe  %s"%(email)
            matched += 1

    if matched == 0:
        print " NO MATCH -- Student: " + firstName + " " + lastName


print ""
