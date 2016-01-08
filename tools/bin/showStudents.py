#!/usr/bin/python
#---------------------------------------------------------------------------------------------------
# Show all our students.
#---------------------------------------------------------------------------------------------------
import sys
import MySQLdb
sys.path.append("python/")
import Database

usage  = " usage:  showStudents.py\n\n"

# Open database connection
db = Database.DatabaseHandle()
# Prepare a cursor object using cursor() method
cursor = db.getCursor()

# Make a new objects of students
students = Database.Container()
rc = students.fillWithStudents(db.handle)
if rc != 0:
    print " ERROR - filling students."
    # disconnect from server
    db.disco()
    sys.exit()

students.show()

# disconnect from server
db.disco()
