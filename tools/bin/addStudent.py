#!/usr/bin/python
#---------------------------------------------------------------------------------------------------
# Add a student to the database using the unique e-mail address. If the record already exists the
# existing record can be deleted.
#
#---------------------------------------------------------------------------------------------------
import sys,os,re
import MySQLdb
import Database

usage  = " usage:  addStudent.py     <email>  <division>  <research>\n\n"
usage += "           email           students email address             (ex. example@mit.edu)\n"
usage += "           division        division of the physics department (ex. ABCP)\n"
usage += "           research        research specialization            (ex. CMX)\n\n"

if len(sys.argv) < 4:
    print "\n ERROR - need to specify full set of required parameters.\n"
    print usage
    sys.exit(0)

# Read command line arguments
email    = sys.argv[1]
division = sys.argv[2]
research = sys.argv[3]

if len(sys.argv) > 4:
    # specify full record
    firstName  = sys.argv[4]
    lastName   = sys.argv[5]
    year       = int(sys.argv[6])
    supervisor = sys.argv[7]
    advisor    = sys.argv[8]
else:
   # find student in our spreadsheet
   firstName = ''
   
   os.chdir(os.getenv('TAPAS_TOOLS_DATA','./'))
   for line in os.popen('cat spreadsheets/grads_F2017.csv spreadsheets/grads_F2016.csv spreadsheets/grads_F2014.csv spreadsheets/grads_2013.csv spreadsheets/grads_2012.csv spreadsheets/grads_2009.csv spreadsheets/ugrad_2015.csv').readlines():   # run command
   #for line in os.popen('cat spreadsheets/grads_F2013.csv').readlines():   # run command
   #for line in os.popen('cat spreadsheets/grads_2012.csv').readlines():   # run command
       line = line[:-1]                     # stripping '\n'
       if re.search(email,line):
           line = line.replace(' ','')
           line = line.replace('"','')
           f = line.split(',')
           #print " LINE: " + line + "\n"
           firstName  = f[0]
           lastName   = f[1]
           g          = f[2].split('/')
           year       = int(g[2])
           supervisor = f.pop()
           advisor    = f.pop()
   
           print " Found in overall spreadsheet\n    ( '%s', '%s', '%s', '%s', '%s', %d, '%s', '%s')"%\
               (firstName,lastName,email,advisor,supervisor,year,division,research)
           break
   
# make sure the student was found
if firstName == "":
    print "\n ERROR - Student was not found in spreadsheet ( Wrong email %s ? ).\n"%email
    sys.exit()
else:
    student = Database.Student(firstName,lastName,email,advisor,supervisor,year,division,research)
    
# Open database connection
db = Database.DatabaseHandle()
# Prepare a cursor object using cursor() method
cursor = db.getCursor()

# Prepare SQL query to select a record from the database.
sql = "select * from Students where Email = '" + email + "'"
nMatches = 0

try:
    # Execute the SQL command
    cursor.execute(sql)
    # Fetch all the rows in a list of lists.
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
        nMatches  += 1;
        # Now print fetched result
        print " Matched student with existing record\n    ('%s','%s','%s','%s','%s',%d,'%s','%s')"% \
              (firstName,lastName,eMail,advisor,supervisor,year,division,research)

except:
    print " Error (): unable to fetch data."
    # disconnect from server
    db.disco()
    sys.exit()

if nMatches == 0:    # now we just add the new student
    sql = " insert into Students values %s"%(student.insertString())
    print " SQL> " + sql
    try:
        # Execute the SQL command
        cursor.execute(sql)
    except:
        print " ERROR - insertion of new student (%s) into the database failed."%(email)
else:
    print "\n WARNING - record exists already, see above.\n"
    yes = raw_input("Do you want to delete the existing record? [N/y] ")

    if yes != "y":
        print "\n EXIT without further action.\n"
        # disconnect from server
        db.disco()
        sys.exit()

    # delete the existing recrod from the table
    sql = " delete from Students where Email = '%s'"%email
    #print " SQL> " + sql
    try:
        # Execute the SQL command
        cursor.execute(sql)
    except:
        print " ERROR - deletion of existing record failed (%s)."%(email)
        
    
# disconnect from server
db.disco()
