#!/usr/bin/python
#---------------------------------------------------------------------------------------------------
# Add a faculty to the database using the unique e-mail address. If the record already exists the
# existing record can be deleted and in another run it can be recreated with the new settings.
#
#---------------------------------------------------------------------------------------------------
import sys,os,re
import MySQLdb
import Database

debug = True

usage  = " usage:  addFaculty.py     <firstname>  <lastname>  <email>  <position>  <status>\n\n"
usage += "           firstnam        first name                     (ex. John)\n"
usage += "           lastname        last name                      (ex. Doe)\n"
usage += "           email           students email address         (ex. example@mit.edu)\n"
usage += "           position        position in physics department (ex. SENIOR_PROFESSOR, JUNIOR_PROFESSOR)\n"
usage += "           status          active or what else            (ex. ACTIVE)\n\n"

if len(sys.argv) < 6:
    print "\n ERROR - need to specify full set of required parameters.\n"
    print usage
    sys.exit(0)

# Read command line arguments
FIRSTNAME = sys.argv[1]
LASTNAME  = sys.argv[2]
EMAIL     = sys.argv[3]
POSITION  = sys.argv[4]
STATUS    = sys.argv[5]

faculty = Database.Faculty(FIRSTNAME,LASTNAME,EMAIL,POSITION,STATUS)

# Open database connection
db = Database.DatabaseHandle()
# Prepare a cursor object using cursor() method
cursor = db.getCursor()

# Prepare SQL query to select a record from the database.
sql = "select * from Teachers where Email = '" + EMAIL + "'"

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
        position   = row[3]
        status     = row[4]
        # Now print fetched result
        if debug:
            print " found Faculty with ('%s','%s','%s','%s','%s');"% \
                  (firstName,lastName,eMail,position,status)

        nMatches += 1

except:
    print " ERROR - unable to fetch data from Teachers table."
    # disconnect from server
    db.disco()
    sys.exit()

if nMatches == 0:    # now we just add the new student
    sql = " insert into Teachers values %s"%faculty.insertString()
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

    # delete the existing record from the table
    sql = " delete from Teachers where Email = '%s'"%EMAIL
    #print " SQL> " + sql
    try:
        # Execute the SQL command
        cursor.execute(sql)
    except:
        print " ERROR - deletion of existing record failed (%s)."%(email)
        
    
# disconnect from server
db.disco()
