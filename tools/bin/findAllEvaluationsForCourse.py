#!/usr/bin/env python
from bs4 import BeautifulSoup
import sys, re, os
import requests
import pandas as pd
import Evaluation
import Database

def writeEvalCache(evalCache,evs):
    
    with open(evalCache,'w') as f:
        for ev in evs:
            f.write(ev.writeline())

    return 0
            
def getEvaluationsFromCache(evalCache):

    evs = []
    with open(evalCache,'r') as f:
        for line in f:
            line = line[:-1]
            ev = Evaluation.Eval('UNKNOWN','UNKNOWN','UNKNOWN','UNKNOWN',-1)
            rc = ev.readline(line)
            if rc == 0:
                evs.append(ev)

    return evs
    
def getEvaluationsFromWeb(term):
    
    # base
    trunc = 'https://edu-apps.mit.edu/ose-rpt/'
    evaluation_url = 'subjectEvaluationSearch.htm?'
    parameters = 'departmentId=+++8&search=Search&termId=%s&subjectCode=&instructorName='%term
    
    # the url
    url = trunc + evaluation_url + parameters
    
    # get the page output
    r = requests.get(url,cookies=cookies)
    
    data = r.text
    
    # scrape it
    evs = []
    soup = BeautifulSoup(data,"lxml")
    paras = soup.find_all('p')
    for row in paras:
        for link in row.find_all('a'):
            evaluation_link = link.get('href')
            if 'subjectId' in evaluation_link:
    
                p = re.compile('subjectId=.*$')
                matches = p.findall(evaluation_link)
                if len(matches) == 0:
                    p = re.compile('subjectId=.*&')
                    matches = p.findall(evaluation_link)
                if len(matches) > 0:
                    number = matches[0].split("=")[1]
                    print " %s --> %s"%(term,number)
                    evs = getEvaluationsForSubject(trunc+evaluation_link,cookies,number,evs)
                    
    return evs
                
def getTerm(mitTerm):

    year = int(mitTerm[:4])
    
    if 'FA' in mitTerm:
        tapasTerm = "F%4d"%(year-1)
    elif 'SP' in mitTerm:
        tapasTerm = "S%4d"%(year)
    else:
        print " ERROR - MIT term (%s) not defined."%(mitTerm)
        sys.exit(1)
        
    return tapasTerm
        
def getCookies(cookie_file):

    cookies = requests.cookies.RequestsCookieJar()

    with open(cookie_file,"r") as f:
        data = f.read()

    lines = data.split(";")
    for cookie in lines:
        cookie = cookie.strip()
        (cookie_key,cookie_value) = cookie.split('=')
        cookies.set(cookie_key,cookie_value,domain='mit.edu', path='/')
        #print cookie_key + " --> " + cookie_value

    return cookies

def getEvaluationsForSubject(url,cookies,number,evs):
    
    r = requests.get(url,cookies=cookies)
    data = r.text
    soup = BeautifulSoup(data,"lxml")
    
    table = soup.find_all('table')[4]
    rows = table.find_all('tr')[2:]

    for row in rows:
        tds = row.find_all('td')
        if len(tds)>0:                              # make sure we do not have crashes
            strongs = tds[0].find_all('strong')
            if len(strongs)>0:                      # make sure we do not have crashes
                name = strongs[0].get_text()
                last_name = name.split(',')[0]
                first_name = name.split(',')[-1]
                overall_grade = float(row.find_all('td')[4].find_all('span')[0].get_text())
                
                #print "%20s %20s %4.1f"%(last_name, first_name, overall_grade)
                ev = Evaluation.Eval(number,last_name,first_name,'UNKNOWN',overall_grade)
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
            
#===================================================================================================
#                                       M A I N
#===================================================================================================
# get our cookies
cookies = getCookies("/home/paus/.cookies")

# command line
term = sys.argv[1]
tapasTerm = getTerm(term)

print " TERM: %s (MIT: %s)"%(tapasTerm,term)
evalCache = ".%s.evals"%(tapasTerm)

if os.path.isfile(evalCache):
    print ' Evaluations cache (%s) exists already.'%(evalCache)
    evs = getEvaluationsFromCache(evalCache)
else:
    evs = getEvaluationsFromWeb(term)
    print ' Writing evaluations cache (%s).'%(evalCache)
    writeEvalCache(evalCache,evs)
            
# Open database connection
db = Database.DatabaseHandle()

# Make a new objects of faculties
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

# Make a new objects of students
assignments = Database.Container()
rc = assignments.fillWithAssignments(db.handle)
if rc != 0:
    print " ERROR - filling assignments."
    # disconnect from server
    db.disco()
    sys.exit()

# filter out the active people and assignments
print ' Finding all active elements in term: %s'%(term)
activeStudents = Database.Container()
activeTeachers = Database.Container()
activeAssignments = Database.Container()
for task, assignment in assignments.getHash().iteritems():
    if assignment.term == tapasTerm:
        activeAssignments.addElement(assignment.task,assignment)
        if assignment.person in students.getHash():
            activeStudents.addElement(assignment.person,students.retrieveElement(assignment.person))
        if assignment.person in teachers.getHash():
            activeTeachers.addElement(assignment.person,teachers.retrieveElement(assignment.person))
    
# find matching emails for the evaluation
for ev in evs:

    #nMatchStudents = findLastName(ev.lastName,activeStudents)
    #nMatchTeachers = findLastName(ev.lastName,activeTeachers)
    #
    #if nMatchStudents+nMatchTeachers > 1:
    #    print '\n ==== AMBIGUOUS ===='
    #    ev.show()
    #    print " nStudents: %d, NTeachers: %d"%(nMatchStudents,nMatchTeachers)
        
    done = False
    for eml, student in activeStudents.getHash().iteritems():
        if ev.lastName == student.lastName:
            tasks = findTasks(tapasTerm,activeAssignments,student.eMail)
            if ev.number in tasks:
                done = True
                #print '\n ==== MATCH ===='
                #ev.show()
                #student.show()
                ev.update(student.eMail)
    if not done:
        for eml, teacher in activeTeachers.getHash().iteritems():
            if ev.lastName == teacher.lastName:
                tasks = findTasks(tapasTerm,activeAssignments,teacher.eMail)
                if ev.number in tasks:
                    done = True
                    #print '\n ==== MATCH ===='
                    #ev.show()
                    #teacher.show()
                    ev.update(teacher.eMail)
                    
    if not done:
        #print '\n ERROR - could not match evaluation.\n '
        #ev.show()
        #print '\n '
        pass


for ev in evs:

    if ev.email == 'UNKNOWN':
        #print ' UNKNOWN: '
        ev.show()
        continue

    for task, assignment in activeAssignments.getHash().iteritems():
        if assignment.term == tapasTerm and ev.email == assignment.person:
            assignment.update(ev.evalO)

# do the updates
for task, assignment in activeAssignments.getHash().iteritems():
    if assignment.evalO != -1:
        assignment.updateDb(db)

# finish
db.disco()
sys.exit()
