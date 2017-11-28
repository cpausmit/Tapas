# ==================================================================================================
#
#    >> create table Courses  (Number char(10), Name char(80), Version int);
#
#    >> create table Teacher  (FirstName char(20), LastName char(20),
#                              Email char(40), Position char(20), Status char(20));
#
#    >> create table Students (FirstName char(20), LastName char(20),
#                              Email char(40), AdvisorEmail char(40), SupervisorEmail char(40),
#                              Year int, Division char(4), Research char(6));
# ==================================================================================================
import MySQLdb
import sys

class DatabaseHandle:
    'Class to provide a unique database handle for all work on the database.'

    def __init__(self,
                 file     = "/etc/my.cnf",
                 group    = "mysql-teaching",
                 database = "Teaching"):
        self.handle = MySQLdb.connect(read_default_file=file,
                                      read_default_group=group,
                                      db=database)

    def getCursor(self):
        return self.handle.cursor()

    def getHandle(self):
        return self.handle
    
    def commit(self):
        self.handle.commit()

    def disco(self):
        self.handle.close()

class Course:
    'Base class for any MIT course.'

    def __init__(self, number,name,version,teacher = 'EMPTY@mit.edu',admin = 'EMPTY@mit.edu'):
        self.number  = number
        self.name    = name
        self.version = version
        self.teacher = teacher
        self.admin   = admin
       
    def setTeacher(self, teacher):
        if self.teacher == 'EMPTY@mit.edu':
            self.teacher = teacher
            if self.admin == 'EMPTY@mit.edu':
                self.admin   = teacher
        else:
            self.teacher += ',' + teacher

    def setAdmin(self, admin):
        self.admin = admin
            
    def show(self):
        print " Course: %s: %s  -- version %d"%(self.number,self.name,self.version)

class Teacher:
    'Base class for any teaching Personnel.'

    def __init__(self, firstName,lastName,eMail):
        self.firstName = firstName
        self.lastName  = lastName
        self.eMail     = eMail
       
    def show(self):
        print " Name (Last, First): %s, %s (%s)"%(self.lastName,self.firstName,self.eMail)

class Student(Teacher):
    'Students that fill the slots of Teaching Assistants in the department.'

    def __init__(self, firstName,lastName,eMail,advisorEmail,supervisorEmail,year,division,research):
        Teacher.__init__(self, firstName,lastName,eMail)
        self.advisorEmail    = advisorEmail
        self.supervisorEmail = supervisorEmail
        self.year            = year
        self.division        = division
        self.research        = research

    def show(self):
        Teacher.show(self)
        print "   Visors (Ad, Super): %s, %s  -- %4d %s %s"% \
              (self.advisorEmail,self.supervisorEmail,self.year,self.division,self.research)

    def insertString(self):
        string = "('%s','%s','%s','%s','%s',%d,'%s','%s')"% \
              (self.firstName,self.lastName,self.eMail,self.advisorEmail,self.supervisorEmail,
               self.year,self.division,self.research)
        return string

class Teacher(Teacher):
    'Teachers that are teacher and therefore either lecture or give recitations.'

    def __init__(self, firstName,lastName,eMail,position,status):
        Teacher.__init__(self, firstName,lastName,eMail)
        self.position        = position
        self.status          = status

    def show(self):
        Teacher.show(self)
        print "   Position: %s  Status, %s"%(self.position,self.status)

    def insertString(self):
        string = "('%s','%s','%s','%s','%s')"% \
              (self.firstName,self.lastName,self.eMail,self.position,self.status)
        return string

class Container:
    'Container class for any type of teacher or course. Basically a hash array. Keys: email or course number.'

    def __init__(self):
        self.hash = { }

    def addElement(self, key, element):
        self.hash[key] = element

    def retrieveElement(self, key):
        return self.hash[key]

    def popElement(self, key):
        return self.hash.pop(key)
    
    def getHash(self):
        return self.hash

    def show(self):
        for key, value in self.hash.iteritems():
            sys.stdout.write(" Key: %-6s -- "%key)
            value.show()

    def fillWithCourses(self,database,debug=False):
        if debug:
            print " Start fill courses."
        # grab the cursor
        cursor = database.cursor()
        # Prepare SQL query to select all courses from the Courses table
        sql = "SELECT * FROM Courses"
        if debug:
            print " SQL> " + sql
        try:
            # Execute the SQL command
            cursor.execute(sql)
            # Fetch all the results
            results = cursor.fetchall()
            for row in results:
                number     = row[0]
                name       = row[1]
                version    = row[2]
                # Now print fetched result
                if debug:
                    print " found Course with ('%s','%s',%d);"%(number,name,version)
                # create a new course and add it to our courses object
                course = Course(number,name,version)
                if debug:
                    print " Course create."
                self.addElement(number,course);
                
        except:
            print " ERROR - unable to fetch data from Courses table."
            return 1

        # all went well
        return 0

    def fillWithTeachers(self,database,debug=False):
        if debug:
            print " Start fill Teachers."
        # grab the cursor
        cursor = database.cursor()
        # Prepare SQL query to select all courses from the Courses table
        sql = "SELECT * FROM Teachers"
        if debug:
            print " SQL> " + sql
        try:
            # Execute the SQL command
            cursor.execute(sql)
            # Fetch all the results
            results = cursor.fetchall()

            for row in results:
                firstName  = row[0]
                lastName   = row[1]
                eMail      = row[2]
                position   = row[3]
                status     = row[4]
                # Now print fetched result
                if debug:
                    print " found Teacher with ('%s','%s','%s','%s','%s');"% \
                          (firstName,lastName,eMail,position,status)

                # create a new teacher and add it to our teachers object
                teacher = Teacher(firstName,lastName,eMail,position,status)
                if debug:
                    print " Teacher create."
                self.addElement(eMail,teacher);
        except:
            print " ERROR - unable to fetch data from Teachers table."
            return 1

        # all went well
        return 0

    def fillWithStudents(self,database,debug=False):
        if debug:
            print " Start fill Students."
        # grab the cursor
        cursor = database.cursor()
        # Prepare SQL query to select all courses from the Courses table
        sql = "SELECT * FROM Students"
        if debug:
            print " SQL> " + sql
        try:
            # Execute the SQL command
            cursor.execute(sql)
            # Fetch all the results
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
                # Now print fetched result
                if debug:
                    print " found Student with ('%s','%s','%s','%s','%s',%d,'%s','%s');"% \
                          (firstName,lastName,eMail,advisor,supervisor,year,division,research)

                # create a new student and add it to our students object
                student = Student(firstName,lastName,eMail,advisor,supervisor,year,\
                                  division,research)
                if debug:
                    print " Student create."
                self.addElement(eMail,student);
                
        except:
            print " ERROR - unable to fetch data from Students table."
            return 1

        # all went well
        return 0
