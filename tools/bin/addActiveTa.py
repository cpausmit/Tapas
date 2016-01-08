#!/usr/bin/python
#---------------------------------------------------------------------------------------------------
# Add an active TA to the database using the unique e-mail address. If the record already exists the
# existing record can be deleted.
#
#---------------------------------------------------------------------------------------------------
import sys,os,re
import MySQLdb
import Database

usage  = " usage:  addActiveTa.py    <email>\n\n"
usage += "           email           students email address             (ex. example@mit.edu)\n\n"

if len(sys.argv) != 2:
    print "\n ERROR - need to specify full set of required parameters (%d)\n"%(len(sys.argv))
    print usage
    sys.exit(0)

# Read command line arguments
email    = sys.argv[1]
   
# Open database connection
db = Database.DatabaseHandle()
# Prepare a cursor object using cursor() method
cursor = db.getCursor()

# Find the list of active TA table
sql = "select * from ActiveTables where TableName like 'Tas%'";
nMatches = 0
try:
    # Execute the SQL command
    cursor.execute(sql)
    # Fetch all the rows in a list of lists.
    results = cursor.fetchall()
    for row in results:
        activeTaTable = row[0]
        nMatches  += 1;
        # Now print fetched result
        print " Matched table '%s'"%(activeTaTable)
except:
    print " Error (%s): unable to fetch data."%(sql)
    # disconnect from server
    db.disco()
    sys.exit()

if nMatches != 1:  # make sure there is one uniquely matching table
    print " ERROR - found several matches (%d)."%(nMatches)
    sys.exit()

# Prepare SQL query to select a record from the database.
sql = "select * from %s where Email = '%s'"%(activeTaTable,email)
nMatches = 0

try:
    # Execute the SQL command
    cursor.execute(sql)
    # Fetch all the rows in a list of lists.
    results = cursor.fetchall()
    for row in results:
        eMail      = row[0]
        nMatches  += 1;
        # Now print fetched result
        print " Matched TA email with existing record('%s')"%(eMail)

except:
    print " Error (%s): unable to fetch data."%(sql)
    # disconnect from server
    db.disco()
    sys.exit()

if nMatches == 0:    # now we just add the new student
    sql = "insert into %s (Email,FullTime,PartTime) values ('%s',1,0)"%(activeTaTable,email)
    print " SQL> " + sql
    try:
        # Execute the SQL command
        cursor.execute(sql)
    except:
        print " ERROR - insertion of new TA (%s) into the database failed."%(email)
else:
    print "\n WARNING - record exists already, see above.\n"
    yes = raw_input("Do you want to delete the existing record? [N/y] ")

    if yes != "y":
        print "\n EXIT without further action.\n"
        # disconnect from server
        db.disco()
        sys.exit()

    # delete the existing recrod from the table
    sql = " delete from %s where Email = '%s'"%(activeTaTable,email)
    print " SQL> " + sql
    try:
        # Execute the SQL command
        cursor.execute(sql)
    except:
        print " ERROR - deletion of existing record failed (%s)."%(email)
        
    
# disconnect from server
db.disco()
