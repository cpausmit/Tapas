#!/usr/bin/python
# --------------------------------------------------------------------------------------------------
# Generate the Tas table from the assignments table.
#
#---------------------------------------------------------------------------------------------------
import sys,os,re
import MySQLdb
import Database

#---------------------------------------------------------------------------------------------------
# H E L P E R
#---------------------------------------------------------------------------------------------------
def makeTasTable(cursor,execute):
    # test whether requested table exists already and if not make the table

    # Prepare SQL query to test whether table exists
    sql = "describe Tas;"
    try:
        # Execute the SQL command
        print " MYSQL> " + sql
        if execute == "exec":
            cursor.execute(sql)
            print ' INFO -- table (Tas) exists already.\n'
    except:
        print ' INFO - table (Tas) does not yet exist.\n'

        # Prepare SQL query to create the new table
        sql = "create table Tas(Term char(5), Email char(40)," + \
              " FullTime tinyint(4), PartTime tinyint(4));" + \
              " alter table Tas add constraint onePerTerm unique(Term, Email);"
        try:
            # Execute the SQL command
            print " MYSQL> " + sql
            if execute == "exec":
                cursor.execute(sql)
        except:
            print ' ERROR - table creation failed.'

def getAllSemesters(cursor):
    # convert all entries in the given table

    semesters = []
    
    # Prepare SQL query to insert record into the existing table
    sql = "select * from Semesters;"
    try:
        # Execute the SQL command
        cursor.execute(sql)
        results = cursor.fetchall()
    except:
        print ' ERROR - select failed: ' + sql
        return
        
    for row in results:
        semesters.append(row[0])

    return semesters

def convertAssignments(cursor,semester,execute):
    # convert all entries in the given table

    # Prepare SQL query to insert record into the existing table
    fullTime = {}
    partTime = {}

    sql = "select * from Assignments where Term = '" + semester + "';"
    try:
        # Execute the SQL command
        cursor.execute(sql)
        results = cursor.fetchall()
    except:
        print ' ERROR - select failed: ' + sql
        return
        
    for row in results:
        term = row[0]
        task = row[1]
        person = row[2]

        if '@' not in person:
            print " SUSPICIOUS EMAIL: " + person
            print "            TERM:  " + term
            print "            TASK:  " + task
            #sys.exit(0)

        if not person in fullTime:
            #print ' Init key: ' + person
            fullTime[person] = 0
            partTime[person] = 0

        if "TaF" in task or "TaH" in task:
            fullTime[person] = 1
        if "TaP" in task:
            partTime[person] = 1
            
            
    for key in sorted(fullTime.keys()):

        full = fullTime[key] 
        part = partTime[key] 

        if full != 0 or part != 0:
            sql = "insert into Tas values ('" + semester + "','" + key + "',%d,%d"%(full,part)+ ");"
            try:
                # Execute the SQL command
                print " MYSQL> " + sql
                if execute == "exec":
                    cursor.execute(sql)
            except:
                print ' ERROR - insert failed: ' + sql

    return
        
#---------------------------------------------------------------------------------------------------
# M A I N
#---------------------------------------------------------------------------------------------------
usage  = " usage:  convertAllTas.py [ <execute = no> ]\n\n"
usage += "           execute         should we execute the insertion into the database\n"
usage += "                           activate by setting: execute = exec\n\n"

# Read command line arguments
execute = "no"
if len(sys.argv) > 1:
    execute = sys.argv[1]

# Open database connection
db = Database.DatabaseHandle()
# Prepare a cursor object using cursor() method
cursor = db.getCursor()

# Make the new summary table
makeTasTable(cursor,execute)

# Get all semesters
semesters = getAllSemesters(cursor)

# Loop through the relevant tables
for semester in semesters:
    print ' Convert semester: ' + semester
    convertAssignments(cursor,semester,execute)

# make sure to commit all changes
db.commit()
# disconnect from server
db.disco()

# exit
sys.exit()
