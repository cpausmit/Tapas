#!/usr/bin/python
#---------------------------------------------------------------------------------------------------
# Clear an existing assignment in the database using the unique task Id.
#
#---------------------------------------------------------------------------------------------------
import sys,os,re
import MySQLdb
import Database

EMPTY_EMAIL = "EMPTY@mit.edu"

#---------------------------------------------------------------------------------------------------
# H E L P E R
#---------------------------------------------------------------------------------------------------
def findAssignment(cursor,semesterId,task):
    # find person of an existing assignment

    email = 'EMTPY'
    results = []
    
    # Prepare SQL query to insert record into the existing table
    sql = "select * from Assignments" + semesterId + " where Task = '" + task + "';"
    try:
        # Execute the SQL command
        cursor.execute(sql)
        results = cursor.fetchall()
    except:
        print ' ERROR - select failed: ' + sql
        email = 'ERROR'
        
    if len(results) == 1:
        email = results[0][1]

    return email
        
#---------------------------------------------------------------------------------------------------
# M A I N
#---------------------------------------------------------------------------------------------------
usage  = " usage:  clearAssignment.py <taskId>  [ <execute = no> ]\n\n"
usage += "           taskId          identification string for a specific assignment\n"
usage += "           execute         should we execute the insertion into the database\n"
usage += "                           activate by setting: execute = exec\n\n"

if len(sys.argv) < 2:
    print "\n ERROR - need to specify the taskId.\n"
    print usage
    sys.exit(0)

# Read command line arguments
taskId = sys.argv[1]
execute = "no"
if len(sys.argv) > 2:
    execute = sys.argv[2]

# Figure out which semester we are talking about
semesterId = taskId.split('-')[0]
print " Task    : " + taskId
print " Semester: " + semesterId

# Open database connection
db = Database.DatabaseHandle()
# Prepare a cursor object using cursor() method
cursor = db.getCursor()

# Prepare SQL query to insert record into the existing table
sql = "update Assignments" + semesterId + \
      " set Person = '%s' where Task = '%s';"%(EMPTY_EMAIL,taskId)
try:
    # Execute the SQL command
    print " MYSQL> " + sql
    if execute == "exec":
        cursor.execute(sql)
        db.commit()

except:
    print ' ERROR - update failed: ' + sql
                
# disconnect from server
db.disco()

# exit
sys.exit()
