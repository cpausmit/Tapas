#!/usr/bin/env python
#
# Read the evaluations and generate the Assignment slots.
#
import sys
import Evaluation
import Database

def readDb():
    # Open database connection
    db = Database.DatabaseHandle()
    
    # Make a new objects of teachers
    teachers = Database.Container()
    rc = teachers.fillWithTeachers(db.handle)
    if rc != 0:
        print " ERROR - filling teachers."
        # disconnect from server
        db.disco()
        sys.exit()
    
    # Make a new objects of students
    students = Database.Container()
    rc = students.fillWithStudents(db.handle)
    if rc != 0:
        print " ERROR - filling students."
        # disconnect from server
        db.disco()
        sys.exit()
    
    # Make a new objects of assignments
    assignments = Database.Container()
    rc = assignments.fillWithAssignments(db.handle)
    if rc != 0:
        print " ERROR - filling assignments."
        # disconnect from server
        db.disco()
        sys.exit()
    
    ## # filter out the active people and assignments
    ## print ' Finding all active elements in term: %s'%(term)
    ## activeStudents = Database.Container()
    ## activeTeachers = Database.Container()
    ## activeAssignments = Database.Container()
    ## 
    ## for task, assignment in assignments.getHash().iteritems():
    ##     if assignment.term == tapasTerm:
    ##         activeAssignments.addElement(assignment.task,assignment)
    ##         if assignment.person in students.getHash():
    ##             activeStudents.addElement(assignment.person,students.retrieveElement(assignment.person))
    ##         if assignment.person in teachers.getHash():
    ##             activeTeachers.addElement(assignment.person,teachers.retrieveElement(assignment.person))
    ## 
    
    return (teachers,students,assignments)    

def getEvaluationsFromCache(evalCache):

    evs = []
    with open(evalCache,'r') as f:
        for line in f:
            line = line[:-1]
            ev = Evaluation.Eval('UNKNOWN','UNKNOWN','UNKNOWN','UNKNOWN','UNKNOWN',-1)
            rc = ev.readline(line)
            if rc == 0:
                evs.append(ev)

    return evs

def findLastName(name,list):
    nMatch = 0
    for eml, entry in list.getHash().iteritems():
        if name == entry.lastName:
            nMatch += 1
    return nMatch
            
def findTasks(tapasTerm,assignments,person):
    tasks = ""
    for eml, element in assignments.getHash().iteritems():
        if element.term == tapasTerm and element.person == person:
            tasks = tasks + "," + element.task
    return tasks

#---------------------------------------------------------------------------------------------------
# M A I N
#---------------------------------------------------------------------------------------------------
# command line
term = sys.argv[1]

evalCache = ".%s.evals"%(term)
evs = getEvaluationsFromCache(evalCache)

# read all database information
(teachers,students,assignments) = readDb()

# show assignments for given term
table = []
for task,assignment in assignments.getHash().iteritems():
    
    if assignment.term == term:
        assignment.show()
        ##print "%s,%s"%(assignment.person,assignment.task)

        try:
            add = True
            t = Database.Task(task)
            person = teachers.retrieveElement(assignment.person)

            entry = ""

            if t.type == 'Lec':
                if t.number == '8.13' or t.number == '8.14':
                    e = "%s %s,%s,%s,Labs,1"%(person.firstName,person.lastName,person.eMail,t.number)
                elif t.number == '8.01' or t.number == '8.02':
                    e = "%s %s,%s,%s,TealLectures,1"%(person.firstName,person.lastName,person.eMail,t.number)
                else:
                    e = "%s %s,%s,%s,Lectures,1"%(person.firstName,person.lastName,person.eMail,t.number)
            elif t.type == 'Rec':
                e = "%s %s,%s,%s,Recitations,2"%(person.firstName,person.lastName,person.eMail,t.number)
            elif t.type == 'Adm':
                e = "%s %s,%s,%s,Admin,1"%(person.firstName,person.lastName,person.eMail,t.number)
            else:
                #print " What? %s : %s -> %s"%(task,t,person)
                add = False
                
            if add:
                e = "%s %s"%(t.number,e)
                table.append(e)
        except:
            pass
            print ' NOT A TEACHER '


for e in sorted(table):
    print " ".join(e.split(" ")[1:])

sys.exit(0)
        
for ev in evs:

    print "\n =O=O=O=O=O NEXT =O=O=O=O=O "

    #nMatchStudents = findLastName(ev.lastName,activeStudents)
    #nMatchTeachers = findLastName(ev.lastName,activeTeachers)
    #
    #if nMatchStudents+nMatchTeachers > 1:
    #    print '\n ==== AMBIGUOUS ===='
    #    ev.show()
    #    print " nStudents: %d, NTeachers: %d"%(nMatchStudents,nMatchTeachers)
        
    done = False
    person = False
    for eml,student in students.getHash().iteritems():
        if ev.lastName == student.lastName:
            done = True
            #print ' ==== match ===='
            #ev.show()
            #student.show()
            person = student
            ev.update(student.eMail)
    if not done:
        for eml,teacher in teachers.getHash().iteritems():
            if ev.lastName == teacher.lastName:
                done = True
                #print ' ==== match ===='
                #ev.show()
                #teacher.show()
                person = teacher
                ev.update(teacher.eMail)
                
    if not done:
        print '\n ERROR - could not match evaluation.\n '
        ev.show()
        print '\n '
        pass
    else:
        ev.show()
        try:
            pos = person.position    
            print "%s %s,%s,%s,Lectures,1"%(person.firstName,person.lastName,person.eMail,ev.number)
        except:
            print ' Not a teacher.'
