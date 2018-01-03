#!/usr/bin/python
#---------------------------------------------------------------------------------------------------
# Add assignments to the database using the unique e-mail address and the unique task. If the
# record already exists the insert will fail.
#
#---------------------------------------------------------------------------------------------------
import sys,os,re
import MySQLdb
import Database

EMPTY_EMAIL = "EMPTY@mit.edu"

#---------------------------------------------------------------------------------------------------
# H E L P E R
#---------------------------------------------------------------------------------------------------
def removeTable(cursor,table,flagging=True):
    # remove the potentially existing table -- can be run in flagging mode only

    if not flagging:
        # Prepare SQL query to drop the existing table
        sql = "drop table Teaching." + table + ";"
        try:
            # Execute the SQL command
            print " MYSQL> " + sql
            cursor.execute(sql)
        except:
            print ' ERROR - table removal failed.'
    else:
        print ' INFO - table removal requested -- working in flagging mode.'

def makeTable(cursor,tableType,semesterId="",execute="",remove=""):
    # test whether requested table exists already and if not make the table

    table = tableType + semesterId

    # remove potentially existing table
    if remove == "remove":
        flagging = False
        if execute != "exec":
            flagging = True
        print " FLAGGING : %d"%flagging
        removeTable(cursor,table,flagging)
     
    # Prepare SQL query to test whether table exists
    sql = "describe " + table
    try:
        # Execute the SQL command
        print " MYSQL> " + sql
        if execute == "exec":
            cursor.execute(sql)
            print ' INFO -- table (%s) exists already.\n'%(table)
    except:
        print ' INFO - table (%s) does not yet exist.\n'%(table)

        # Prepare SQL query to create the new table
        if   tableType == 'Assignments':
            sql = "create table " + table + "(Task char(40), Person char(40));"
        elif tableType == 'Tas':
            sql = "create table " + table + "(Email char(40), FullTime tinyint, PartTime tinyint);"
        elif tableType == 'Preferences':
            sql = "create table " + table + "(Email char(40), Pref1 text, Pref2 text, Pref3 text);"
            
        try:
            # Execute the SQL command
            print " MYSQL> " + sql
            if execute == "exec":
                cursor.execute(sql)
        except:
            print ' ERROR - table creation failed.'
            
        # make the task field unique
        if   tableType == 'Assignments':
            sql = "alter table " + table + " add unique idTask (Task);"
        elif tableType == 'Tas' or tableType == 'Preferences':
            sql = "alter table " + table + " add unique idEmail (Email);"

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
usage  = " usage:  addAssignments.py <semesterId>  [ <execute = no> [ <remove = no> ] ]\n\n"
usage += "           semesterId      identification string for a specific semster\n"
usage += "                           ex. F13 (Fall 2013), I13 (IAP 2013), S13 (Spring 2013)\n"
usage += "           execute         should we execute the insertion into the database\n"
usage += "                           activate by setting: execute = exec\n"
usage += "           remove          if set this will remove all existing entries in the database\n"
usage += "                           activate by setting: remove = remove\n\n"

if len(sys.argv) < 2:
    print "\n ERROR - need to specify the semester id.\n"
    print usage
    sys.exit(0)

# Read command line arguments
semesterId = sys.argv[1]
execute = "no"
if len(sys.argv) > 2:
    execute = sys.argv[2]
remove = "no"
if len(sys.argv) > 3:
    remove = sys.argv[3]
    print '\n ATTENTION -- removing all existing assignments (%s).\n'%(remove)

# Open database connection
db = Database.DatabaseHandle()
# Prepare a cursor object using cursor() method
cursor = db.getCursor()

makeTable(cursor,'Assignments',semesterId,execute,remove)
makeTable(cursor,'Tas',        semesterId,execute,remove)
makeTable(cursor,'Preferences',semesterId,execute,remove)

lastCourse = 0
os.chdir(os.getenv('TAPAS_TOOLS_DATA','./'))
cmd = 'cat spreadsheets/' + semesterId + 'Teachers.csv | sort  -t, -k3 | grep -v ^#'
for line in os.popen(cmd).readlines():
    line = line[:-1]
    f = line.split(',')
    if len(f) > 1:
        
        email = (f[1]).strip()
        course = (f[2]).strip()
        if lastCourse != course:
            lastCourse = course
            instance = 1
        else:
            instance = instance + 1
        role = (f[3]).strip()
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
                    db.commit()

            except:
                try:
                    # Execute the update SQL command
                    sql = "update Assignments" + semesterId + \
                          " set Person = '%s' where Task = '%s';"%(email,subtask)
                    print " MYSQL> " + sql
                    if execute == "exec":
                        cursor.execute(sql)
                        db.commit()
                except:
                    print ' ERROR - insert/update failed: ' + sql

# Loop through TA candidate file and add them to our table
os.chdir(os.getenv('TAPAS_TOOLS_DATA','./'))
for line in os.popen('cat spreadsheets/' + semesterId + 'Tas.csv | grep -v ^#').readlines():
    line = line[:-1]
    f = line.split(',')
    if len(f) == 1:
        # remove leading or trialing spaces
        email = (f[0]).strip()
        # Prepare SQL query to insert record into the existing table
        sql = "insert into Tas" + semesterId + " values ('"  + email + "',1,0);"
        try:
            # Execute the SQL command
            print " MYSQL> " + sql
            if execute == "exec":
                cursor.execute(sql)
                db.commit()
        except:
            print ' ERROR - insert failed: ' + sql

# Loop through TA the assignment file
os.chdir(os.getenv('TAPAS_TOOLS_DATA','./'))
for line in os.popen('cat spreadsheets/' + semesterId + 'Assignments.csv | grep -v ^#').readlines():
    line = line[:-1]
    f = line.split(',')
    if len(f) > 1:
        email = (f[0]).strip()
        task  = (f[1]).strip()
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
                    db.commit()
            except:
                print ' ERROR - insert failed: ' + sql
                # in case no assignment yet made update existing one
                setEmail = findAssignment(cursor,semesterId,task)
                print " set email: '" + setEmail + "'"
                if setEmail == '' or  setEmail == EMPTY_EMAIL:
                    sql = "update Assignments" + semesterId + " set Person = '" \
                          + email + "' where Task = '" + subtask + "';"
                    print " SQL - " + sql
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
