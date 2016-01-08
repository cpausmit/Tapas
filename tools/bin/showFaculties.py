#!/usr/bin/python
#---------------------------------------------------------------------------------------------------
# Show all our faculty.
#---------------------------------------------------------------------------------------------------
import sys
import MySQLdb
import Database

usage  = " usage:  showFaculties.py\n\n"

# Open database connection
db = Database.DatabaseHandle()
# Prepare a cursor object using cursor() method
cursor = db.getCursor()

# Make a new objects of students
faculties = Database.Container()
rc = faculties.fillWithFaculties(db.handle)
if rc != 0:
    print " ERROR - filling faculties."
    # disconnect from server
    db.disco()
    sys.exit()

faculties.show()

# disconnect from server
db.disco()
