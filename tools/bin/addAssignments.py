#!/usr/bin/python
#---------------------------------------------------------------------------------------------------
# Add assignments to the database using the unique e-mail address and the unique task. If the
# record already exists the insert will fail.
#
#---------------------------------------------------------------------------------------------------
import sys,os,re
import MySQLdb
import Database

#---------------------------------------------------------------------------------------------------
# H E L P E R
#---------------------------------------------------------------------------------------------------
def makeTable(cursor,semesterId="",execute=""):
    # test whether requested table exists already and if not make the table

    table = "Assignments" + semesterId

    # Prepare SQL query to test whether table exists
    sql = "select 1 from " + table + " limit 1;"
    try:
        # Execute the SQL command
        print " MYSQL> " + sql
        if execute == "exec":
            cursor.execute(sql)
            print ' INFO -- table (%s) exists already.\n'%(table)
    except:
        print ' INFO - table (%s) does not yet exist.\n'%(table)

        # Prepare SQL query to create the new table
        sql = "create table " + table + "(Task char(40), Person char(40));"
        try:
            # Execute the SQL command
            print " MYSQL> " + sql
            if execute == "exec":
                cursor.execute(sql)
        except:
            print ' ERROR - table creation failed.'
            
    # make the task field unique
    sql = "alter table " + table + " add unique idTask (Task);"
    try:
        # Execute the SQL command
        print " MYSQL> " + sql
        if execute == "exec":
            cursor.execute(sql)
    except:
        print ' ERROR - creating unique index field failed.'


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
usage  = " usage:  addAssignments.py <semesterId>  [ <execute = no> ]\n\n"
usage += "           semesterId      identification string for a specific semster\n"
usage += "                           ex. F13 (Fall 2013), I13 (IAP 2013), S13 (Spring 2013)\n"
usage += "           execute         should we execute the insertion into the database\n"
usage += "                           activate by setting: execute = exec\n\n"

if len(sys.argv) < 2:
    print "\n ERROR - need to specify the semester id.\n"
    print usage
    sys.exit(0)

# Read command line arguments
semesterId = sys.argv[1]
execute = "no"
if len(sys.argv) > 2:
    execute = sys.argv[2]

# Open database connection
db = Database.DatabaseHandle()
# Prepare a cursor object using cursor() method
cursor = db.getCursor()

makeTable(cursor,semesterId,execute)

lastCourse = 0
cmd = 'cat spreadsheets/' + semesterId + 'Teachers.csv | sort  -t, -k3 | grep -v ^#'
for line in os.popen(cmd).readlines():
    line = line[:-1]
    f = line.split(',')
    if len(f) > 1:
        
        email = f[1]
        course = f[2]
        if lastCourse != course:
            lastCourse = course
            instance = 1
        else:
            instance = instance + 1
        role = f[3]
        if   role == 'Admin':
            task = semesterId + '-' + course + '-Adm-%d'%(instance)
        elif role == 'Recitations':
            task = semesterId + '-' + course + '-Rec-%d'%(instance)
        else:
            task = semesterId + '-' + course + '-Lec-%d'%(instance)
        g = task.split('/')
        for subtask in g:
            print " Assignment '%s' -> '%s'"%(email,subtask)
            
            # Prepare SQL query to insert record into the existing table
            sql = "insert into Assignments" + semesterId + " values ('" \
                  + subtask + "','" + email + "');"
            try:
                # Execute the SQL command
                print " MYSQL> " + sql
                if execute == "exec":
                    cursor.execute(sql)
            except:
                print ' ERROR - insert failed: ' + sql

# Loop through TA the assignment file
for line in os.popen('cat spreadsheets/' + semesterId + 'Assignments.csv | grep -v ^#').readlines():
    line = line[:-1]
    f = line.split(',')
    if len(f) > 1:
        email = f[0]
        task  = f[1]
        g = task.split('/')
        for subtask in g:
            print " Assignment '%s' -> '%s'"%(email,subtask)
            # Prepare SQL query to insert record into the existing table
            sql = "insert into Assignments" + semesterId + " values ('" \
                  + subtask + "','" + email + "');"
            try:
                # Execute the SQL command
                print " MYSQL> " + sql
                if execute == "exec":
                    cursor.execute(sql)
            except:
                print ' ERROR - insert failed: ' + sql
                # in case no assignment yet made update existing one
                setEmail = findAssignment(cursor,semesterId,task)
                print " set email: '" + setEmail + "'"
                if setEmail == '':
                    sql = "update Assignments" + semesterId + " set Person = '" \
                          + email + "' where Task = '" + subtask + "';"
                    print " SQL - " + sql
                    try:
                        # Execute the SQL command
                        print " MYSQL> " + sql
                        if execute == "exec":
                            cursor.execute(sql)
                    except:
                        print ' ERROR - update failed: ' + sql
                    
                
# disconnect from server
db.disco()

# exit
sys.exit()
